<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '../config/db.php';
require_once '../vendor/autoload.php';
require_once 'admin_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
$smtp_config = [
    'host' => 'smtp.gmail.com',
    'username' => 'dathoami2k5@gmail.com',
    'password' => 'pmmy ddcn xulj ruvb',
    'port' => 587,
    'from_email' => 'dathoami2k5@gmail.com',
    'from_name' => 'Anh Em Rọt Store'
];
define('EMAIL_QUEUE_LIMIT', 50);
define('EMAIL_SEND_DELAY', 500000);


$errors = [];
$success = false;
$template_name_to_save = '';
$display_message = '';

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($token, $_SESSION['csrf_token']);
}

function addToEmailQueue($conn, $recipient_id, $recipient_email, $subject, $message_body) {
    if (!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {
        error_log(date('Y-m-d H:i:s') . " - Invalid email address: {$recipient_email}\n", 3, __DIR__ . '/../logs/email_errors.log');
        return false;
    }

    $stmt = $conn->prepare("INSERT INTO email_queue (recipient_id, recipient_email, subject, body, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
    if ($stmt) {
        $stmt->bind_param("isss", $recipient_id, $recipient_email, $subject, $message_body);
        $stmt->execute();
        $stmt->close();
        // Thêm thông báo
        $notification_stmt = $conn->prepare("INSERT INTO notifications (title, message, type, is_read, created_at) VALUES (?, ?, 'success', FALSE, NOW())");
        $title = "Email đã được thêm vào hàng đợi";
        $message = "Email đến {$recipient_email} đã được thêm vào hàng đợi thành công.";
        $notification_stmt->bind_param("ss", $title, $message);
        $notification_stmt->execute();
        $notification_stmt->close();
        return true;
    } else {
        error_log(date('Y-m-d H:i:s') . " - Failed to prepare email queue statement: " . $conn->error . "\n", 3, __DIR__ . '/../logs/email_errors.log');
        return false;
    }
}

generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Lỗi bảo mật: CSRF token không hợp lệ. Vui lòng thử lại.";
    } else {
        $subject_template = trim(filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
        $message_template = trim($_POST['message'] ?? '');

        if (isset($_POST['save_template'])) {
            $template_name_to_save = trim(filter_input(INPUT_POST, 'template_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
            if (empty($template_name_to_save)) {
                $errors[] = "Tên mẫu không được để trống khi lưu.";
            } elseif (empty($subject_template) && empty($message_template)) {
                $errors[] = "Không thể lưu mẫu rỗng. Vui lòng nhập tiêu đề hoặc nội dung.";
            } else {
                $check_stmt = $conn->prepare("SELECT COUNT(*) FROM email_templates WHERE name = ?");
                $check_stmt->bind_param("s", $template_name_to_save);
                $check_stmt->execute();
                $check_stmt->bind_result($count);
                $check_stmt->fetch();
                $check_stmt->close();

                if ($count > 0) {
                    $errors[] = "Tên mẫu '$template_name_to_save' đã tồn tại. Vui lòng chọn tên khác.";
                } else {
                    $insert_stmt = $conn->prepare("INSERT INTO email_templates (name, subject, body, created_at) VALUES (?, ?, ?, NOW())");
                    $insert_stmt->bind_param("sss", $template_name_to_save, $subject_template, $message_template);
                    if ($insert_stmt->execute()) {
                        $success = true;
                        $display_message = "Mẫu email '$template_name_to_save' đã được lưu thành công!";
                        $notification_stmt = $conn->prepare("INSERT INTO notifications (title, message, type, is_read, created_at) VALUES (?, ?, 'success', FALSE, NOW())");
                        $title = "Mẫu email đã được lưu";
                        $message = "Mẫu email '$template_name_to_save' đã được lưu thành công.";
                        $notification_stmt->bind_param("ss", $title, $message);
                        $notification_stmt->execute();
                        $notification_stmt->close();
                    } else {
                        $errors[] = "Lỗi khi lưu mẫu email: " . $conn->error;
                    }
                    $insert_stmt->close();
                }
            }
        } else {
            $recipients_type = filter_input(INPUT_POST, 'recipients_type', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'all';
            $selected_user_ids = filter_input(INPUT_POST, 'selected_users', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? [];

            if (empty($subject_template)) {
                $errors[] = "Tiêu đề email không được để trống.";
            }
            if (empty($message_template)) {
                $errors[] = "Nội dung email không được để trống.";
            }

            if (empty($errors)) {
                $emails_to_queue = [];
                $queued_count = 0;

                if ($recipients_type === 'all') {
                    $stmt_customers = $conn->prepare("SELECT u.id, u.email, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name 
                                                    FROM users u 
                                                    JOIN user_detail ud ON u.id = ud.user_id 
                                                    WHERE u.role = 'customer' 
                                                    ORDER BY ud.first_name ASC");
                    if ($stmt_customers) {
                        $stmt_customers->execute();
                        $result_customers = $stmt_customers->get_result();
                        while ($row = $result_customers->fetch_assoc()) {
                            $emails_to_queue[] = $row;
                        }
                        $stmt_customers->close();
                    } else {
                        $errors[] = "Lỗi khi truy vấn danh sách người dùng: " . $conn->error;
                        error_log(date('Y-m-d H:i:s') . " - Failed to prepare user list statement during POST: " . $conn->error . "\n", 3, __DIR__ . '/../logs/email_errors.log');
                    }
                } elseif ($recipients_type === 'selected' && !empty($selected_user_ids)) {
                    $selected_user_ids = array_map('intval', $selected_user_ids);
                    $placeholders = implode(',', array_fill(0, count($selected_user_ids), '?'));
                    $stmt_selected_emails = $conn->prepare("SELECT u.id, u.email, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name 
                                                           FROM users u 
                                                           JOIN user_detail ud ON u.id = ud.user_id 
                                                           WHERE u.id IN ($placeholders)");
                    if ($stmt_selected_emails) {
                        $types = str_repeat('i', count($selected_user_ids));
                        $stmt_selected_emails->bind_param($types, ...$selected_user_ids);
                        $stmt_selected_emails->execute();
                        $result_selected_emails = $stmt_selected_emails->get_result();
                        while ($row = $result_selected_emails->fetch_assoc()) {
                            $emails_to_queue[] = $row;
                        }
                        $stmt_selected_emails->close();
                    } else {
                        $errors[] = "Lỗi khi truy vấn người dùng đã chọn: " . $conn->error;
                        error_log(date('Y-m-d H:i:s') . " - Failed to prepare selected user statement: " . $conn->error . "\n", 3, __DIR__ . '/../logs/email_errors.log');
                    }
                } else {
                    $errors[] = "Vui lòng chọn ít nhất một người dùng hoặc chọn 'Tất cả người dùng'.";
                }

                if (empty($errors) && !empty($emails_to_queue)) {
                    foreach ($emails_to_queue as $recipient) {
                        $personal_subject = str_replace(['{{full_name}}', '{{email}}'], 
                            [htmlspecialchars($recipient['full_name'] ?? ''), htmlspecialchars($recipient['email'] ?? '')], 
                            $subject_template);
                        $personal_message = str_replace(['{{full_name}}', '{{email}}'], 
                            [htmlspecialchars($recipient['full_name'] ?? ''), htmlspecialchars($recipient['email'] ?? '')], 
                            $message_template);

                        if (addToEmailQueue($conn, $recipient['id'], $recipient['email'], $personal_subject, $personal_message)) {
                            $queued_count++;
                        }
                    }

                    if ($queued_count > 0) {
                        $success = true;
                        $display_message = "Đã thêm $queued_count email vào hàng đợi. Email sẽ được gửi đi trong thời gian sớm nhất.";
                    } else {
                        $errors[] = "Không có email nào được thêm vào hàng đợi.";
                    }
                }
            }
        }
    }
    generateCsrfToken();
}

$customers = [];
$stmt_users = $conn->prepare("SELECT u.id, u.email, CONCAT(ud.first_name, ' ', ud.last_name) AS full_name 
                             FROM users u 
                             JOIN user_detail ud ON u.id = ud.user_id 
                             WHERE u.role = 'customer' 
                             ORDER BY ud.first_name ASC");
if ($stmt_users) {
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();
    while ($row = $result_users->fetch_assoc()) {
        $customers[] = $row;
    }
    $stmt_users->close();
} else {
    $errors[] = "Lỗi khi truy vấn danh sách người dùng: " . $conn->error;
    error_log(date('Y-m-d H:i:s') . " - Failed to prepare user list statement: " . $conn->error . "\n", 3, __DIR__ . '/../logs/email_errors.log');
}

$email_templates = [];
$stmt_templates = $conn->prepare("SELECT id, name, subject, body FROM email_templates ORDER BY name ASC");
if ($stmt_templates) {
    $stmt_templates->execute();
    $result_templates = $stmt_templates->get_result();
    while ($row = $result_templates->fetch_assoc()) {
        $email_templates[] = $row;
    }
    $stmt_templates->close();
} else {
    error_log(date('Y-m-d H:i:s') . " - Failed to prepare email templates statement: " . $conn->error . "\n", 3, __DIR__ . '/../logs/email_errors.log');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi Email đến Người dùng | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.tiny.cloud/1/1fb4gph4gh6kk3hj46hr2u56fevti2eqpzjm0tendiqscegi/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        :root {
            --primary-color: #1a3c6d; /* Đồng bộ với print_purchase_invoice.php */
            --secondary-color: #3498db;
            --success-color: #16a34a;
            --danger-color: #dc2626;
            --warning-color: #f59e0b;
            --info-color: #0891b2;
            --gray-color: #6b7280;
            --text-color: #2d2d2d;
            --bg-color: #f9fafb;
            --card-bg: #ffffff;
            --border-color: #e0e0e0;
            --shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'dejavusans', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            color: #fff;
            opacity: 1;
            transition: opacity 0.5s ease-out;
            border-left: 4px solid;
        }

        .notification.success-notification {
            background: rgba(22, 163, 74, 0.1);
            border-color: var(--success-color);
            color: var(--success-color);
        }

        .notification.error-notification {
            background: rgba(220, 38, 38, 0.1);
            border-color: var(--danger-color);
            color: var(--danger-color);
        }

        .notification.fade-out {
            opacity: 0;
        }

        .email-management-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .header-actions .btn-preview {
            background: var(--secondary-color);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.3s ease;
        }

        .header-actions .btn-preview:hover {
            background: #2980b9;
        }

        .placeholder-info {
            background: rgba(52, 152, 219, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid var(--secondary-color);
            font-size: 0.875rem;
        }

        .placeholder-info ul {
            margin: 10px 0 0;
            padding-left: 20px;
        }

        .email-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: var(--primary-color);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.875rem;
            font-family: 'dejavusans', sans-serif;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }

        .tinymce-editor {
            min-height: 350px;
        }

        .template-save-group {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .btn-save {
            background: var(--success-color);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.3s ease;
        }

        .btn-save:hover {
            background: #15803d;
        }

        .recipient-options .radio-group {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.875rem;
        }

        .customer-selection {
            background: rgba(52, 152, 219, 0.05);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--secondary-color);
        }

        .customer-list-container {
            max-height: 250px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
        }

        .customer-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            font-size: 0.875rem;
        }

        .customer-label {
            margin-left: 10px;
            color: var(--text-color);
        }

        .btn-submit {
            background: var(--secondary-color);
            color: #fff;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background: #2980b9;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 700px;
            box-shadow: var(--shadow);
            position: relative;
        }

        .close-button {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--gray-color);
            transition: color 0.3s ease;
        }

        .close-button:hover {
            color: var(--danger-color);
        }

        .modal-body h3 {
            margin-bottom: 15px;
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        #previewBodyIframe {
            width: 100%;
            height: 450px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: #fff;
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }

            .email-management-card {
                padding: 15px;
            }

            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .template-save-group {
                flex-direction: column;
            }

            .btn-save, .btn-submit {
                width: 100%;
            }

            .recipient-options .radio-group {
                flex-direction: column;
                gap: 10px;
            }

            .modal-content {
                width: 95%;
            }

            #previewBodyIframe {
                height: 300px;
            }
        }
    </style>
</head>
<body>
    <main class="admin-container">
        <div id="notification-area">
            <?php if (!empty($errors)): ?>
                <div class="notification error-notification">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif ($success): ?>
                <div class="notification success-notification">
                    <p><?= htmlspecialchars($display_message) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <section class="email-management-card">
            <div class="card-header">
                <h2>Gửi Email đến Người dùng</h2>
                <div class="header-actions">
                    <button type="button" id="previewEmailBtn" class="btn-preview">
                        <i class="fas fa-eye"></i> Xem trước
                    </button>
                </div>
            </div>

            <div class="placeholder-info">
                <p><strong>Placeholder hỗ trợ:</strong></p>
                <ul>
                    <li><code>{{full_name}}</code>: Tên đầy đủ của người nhận</li>
                    <li><code>{{email}}</code>: Địa chỉ email của người nhận</li>
                </ul>
            </div>

            <form action="send_email_to_users.php" method="POST" class="email-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="form-group">
                    <label for="template_select">Chọn mẫu email:</label>
                    <select id="template_select" name="template_select" class="form-control">
                        <option value="">-- Chọn mẫu có sẵn --</option>
                        <?php foreach ($email_templates as $template): ?>
                            <option value="<?= $template['id'] ?>" 
                                    data-subject="<?= htmlspecialchars($template['subject']) ?>"
                                    data-body="<?= htmlspecialchars($template['body']) ?>">
                                <?= htmlspecialchars($template['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Tiêu đề Email:</label>
                    <input type="text" id="subject" name="subject" 
                           value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" 
                           class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="message">Nội dung Email:</label>
                    <textarea id="message" name="message" class="form-control tinymce-editor"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                </div>

                <div class="form-group template-save-group">
                    <div style="flex: 1;">
                        <label for="template_name">Lưu mẫu hiện tại thành:</label>
                        <input type="text" id="template_name" name="template_name" 
                               class="form-control" placeholder="Nhập tên mẫu mới" 
                               value="<?= htmlspecialchars($template_name_to_save) ?>">
                    </div>
                    <button type="submit" name="save_template" value="1" class="btn-save">
                        <i class="fas fa-save"></i> Lưu Mẫu
                    </button>
                </div>

                <div class="form-group recipient-options">
                    <label>Đối tượng nhận:</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" id="recipients_all" name="recipients_type" value="all" 
                                   <?= (isset($_POST['recipients_type']) && $_POST['recipients_type'] === 'all') ? 'checked' : (!isset($_POST['recipients_type']) ? 'checked' : '') ?>>
                            Tất cả người dùng
                        </label>
                        <label class="radio-label">
                            <input type="radio" id="recipients_selected" name="recipients_type" value="selected" 
                                   <?= (isset($_POST['recipients_type']) && $_POST['recipients_type'] === 'selected') ? 'checked' : '' ?>>
                            Chọn người dùng cụ thể
                        </label>
                    </div>
                </div>

                <div class="form-group customer-selection" id="customer_selection_container" 
                     style="display: <?= (isset($_POST['recipients_type']) && $_POST['recipients_type'] === 'selected') ? 'block' : 'none'; ?>;">
                    <label>Chọn người dùng:</label>
                    <div class="customer-list-container">
                        <?php if (empty($customers)): ?>
                            <p class="no-customers">Không có người dùng nào để lựa chọn.</p>
                        <?php else: ?>
                            <?php foreach ($customers as $customer): ?>
                                <div class="customer-item">
                                    <input type="checkbox" id="user_<?= $customer['id'] ?>" 
                                           name="selected_users[]" value="<?= $customer['id'] ?>" 
                                           <?= in_array($customer['id'], $_POST['selected_users'] ?? []) ? 'checked' : '' ?>>
                                    <label for="user_<?= $customer['id'] ?>" class="customer-label">
                                        <?= htmlspecialchars($customer['full_name'] ?? $customer['email']) ?> 
                                        (<?= htmlspecialchars($customer['email']) ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Gửi Email
                    </button>
                </div>
            </form>
        </section>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <div id="emailPreviewModal" class="modal">
        <div class="modal-content">
            <span class="close-button">×</span>
            <h2>Xem trước Email</h2>
            <div class="modal-body">
                <h3>Tiêu đề: <span id="previewSubject"></span></h3>
                <iframe id="previewBodyIframe"></iframe>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
                button.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                });
            });

            const currentPath = window.location.pathname.split('/').pop();
            document.querySelectorAll('.sidebar nav a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            const recipientsAllRadio = document.getElementById('recipients_all');
            const recipientsSelectedRadio = document.getElementById('recipients_selected');
            const customerSelectionContainer = document.getElementById('customer_selection_container');

            function toggleCustomerSelection() {
                customerSelectionContainer.style.display = recipientsSelectedRadio.checked ? 'block' : 'none';
            }

            toggleCustomerSelection();
            recipientsAllRadio.addEventListener('change', toggleCustomerSelection);
            recipientsSelectedRadio.addEventListener('change', toggleCustomerSelection);

            tinymce.init({
                selector: '#message',
                plugins: 'link image lists table emoticons',
                toolbar: 'undo redo | bold italic underline | link image | numlist bullist | emoticons',
                menubar: false,
                height: 400,
                content_style: 'body { font-family: dejavusans, Arial, sans-serif; font-size: 14px; color: #2d2d2d; }',
                placeholder: 'Nhập nội dung email...',
                setup: function (editor) {
                    editor.on('init', function () {
                        editor.getContainer().style.borderRadius = '6px';
                    });
                }
            });

            const templateSelect = document.getElementById('template_select');
            const subjectInput = document.getElementById('subject');
            const messageEditor = tinymce.get('message');

            templateSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const subject = selectedOption.getAttribute('data-subject') || '';
                const body = selectedOption.getAttribute('data-body') || '';
                subjectInput.value = subject;
                messageEditor.setContent(body);
            });

            const previewEmailBtn = document.getElementById('previewEmailBtn');
            const emailPreviewModal = document.getElementById('emailPreviewModal');
            const closeButton = emailPreviewModal.querySelector('.close-button');
            const previewSubjectSpan = document.getElementById('previewSubject');
            const previewBodyIframe = document.getElementById('previewBodyIframe');

            previewEmailBtn.addEventListener('click', function () {
                const subject = subjectInput.value;
                const message = messageEditor.getContent();
                const sampleCustomer = { full_name: 'Nguyen Van A', email: 'nguoinhan@example.com' };

                let previewSubject = subject.replace(/{{full_name}}/g, sampleCustomer.full_name)
                                         .replace(/{{email}}/g, sampleCustomer.email);
                let previewBody = message.replace(/{{full_name}}/g, sampleCustomer.full_name)
                                       .replace(/{{email}}/g, sampleCustomer.email);

                previewSubjectSpan.textContent = previewSubject;
                const iframeDoc = previewBodyIframe.contentDocument || previewBodyIframe.contentWindow.document;
                iframeDoc.open();
                iframeDoc.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body { font-family: dejavusans, Arial, sans-serif; line-height: 1.6; color: #2d2d2d; padding: 20px; }
                            h1, h2, h3 { color: #1a3c6d; }
                            a { color: #3498db; text-decoration: none; }
                            a:hover { text-decoration: underline; }
                        </style>
                    </head>
                    <body>${previewBody}</body>
                    </html>
                `);
                iframeDoc.close();
                emailPreviewModal.style.display = 'flex';
            });

            closeButton.addEventListener('click', function () {
                emailPreviewModal.style.display = 'none';
            });

            window.addEventListener('click', function (event) {
                if (event.target === emailPreviewModal) {
                    emailPreviewModal.style.display = 'none';
                }
            });

            const notificationArea = document.getElementById('notification-area');
            const notifications = notificationArea.querySelectorAll('.notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    notification.classList.add('fade-out');
                    setTimeout(() => notification.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>