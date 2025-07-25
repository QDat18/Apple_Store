<?php
session_start();
require_once 'config/db.php'; // Required for database interaction
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch site settings for contact information
try {
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('contact_email', 'phone_number', 'address', 'facebook_url', 'twitter_url', 'instagram_url')");
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn site_settings: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $site_settings = [];
    while ($row = $result->fetch_assoc()) {
        $site_settings[$row['setting_key']] = $row['setting_value'];
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("Failed to fetch site settings: " . $e->getMessage());
    $site_settings = [
        'contact_email' => 'contact@applestore.vn',
        'phone_number' => '+84 123 456 789',
        'address' => '123 Đường ABC, Quận 1, TP.HCM',
        'facebook_url' => 'https://www.facebook.com/applestorevn',
        'twitter_url' => 'https://www.twitter.com/applestorevn',
        'instagram_url' => 'https://www.instagram.com/applestorevn'
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    // Path to PHPMailer's autoloader
    require 'vendor/autoload.php';

    // CSRF validation
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Lỗi bảo mật: CSRF token không hợp lệ.";
        error_log("CSRF token validation failed. Expected: {$_SESSION['csrf_token']}, Submitted: {$_POST['csrf_token']}");
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $message = trim($_POST['message'] ?? '');

        $errors = [];

        // Server-side validation
        if (empty($name)) {
            $errors[] = 'Họ và tên không được để trống.';
        }
        if (empty($email)) {
            $errors[] = 'Email không được để trống.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Địa chỉ email không hợp lệ.';
        }
        if (!empty($phone) && !preg_match('/^[0-9]{9,15}$/', $phone)) {
            $errors[] = 'Số điện thoại không hợp lệ.';
        }
        if (empty($message)) {
            $errors[] = 'Nội dung không được để trống.';
        }

        if (empty($errors)) {
            // Insert into contacts table
            try {
                $stmt = $conn->prepare("INSERT INTO contacts (name, email, message, created_at) VALUES (?, ?, ?, NOW())");
                if (!$stmt) {
                    throw new Exception("Lỗi chuẩn bị truy vấn contacts: " . $conn->error);
                }
                $stmt->bind_param("sss", $name, $email, $message);
                if (!$stmt->execute()) {
                    throw new Exception("Lỗi lưu thông tin liên hệ: " . $stmt->error);
                }
                $contact_id = $conn->insert_id;
                $stmt->close();

                // Log action
                $user_id = $_SESSION['user_id'] ?? null;
                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details, created_at) VALUES (?, 'submit_contact', ?, NOW())");
                if ($log_stmt) {
                    $details = "User submitted contact form. Name: $name, Email: $email, Contact ID: $contact_id";
                    $log_stmt->bind_param("is", $user_id, $details);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
            } catch (Exception $e) {
                $error = "Đã xảy ra lỗi khi lưu thông tin liên hệ. Vui lòng thử lại.";
                error_log("Contact form save failed: " . $e->getMessage());
            }

            // Send email via PHPMailer
            if (!isset($error)) {
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'dathoami2k5@gmail.com'; // Replace with your Gmail address
                    $mail->Password = 'pmmy ddcn xulj ruvb'; // Replace with your Gmail App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($email, $name);
                    $mail->addAddress($site_settings['contact_email'], 'Admin');
                    $mail->isHTML(true);
                    $mail->Subject = 'Liên hệ từ khách hàng mới: ' . htmlspecialchars($name);
                    $mail->Body = '
                        <h2>Thông tin liên hệ mới</h2>
                        <p><strong>Tên:</strong> ' . htmlspecialchars($name) . '</p>
                        <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                        <p><strong>Số điện thoại:</strong> ' . htmlspecialchars($phone ?: 'Không cung cấp') . '</p>
                        <p><strong>Nội dung:</strong></p>
                        <p>' . nl2br(htmlspecialchars($message)) . '</p>
                    ';
                    $mail->AltBody = "Tên: $name\nEmail: $email\nSố điện thoại: " . ($phone ?: 'Không cung cấp') . "\nNội dung: $message";

                    $mail->send();
                    $success = 'Gửi liên hệ thành công! Chúng tôi đã nhận được tin nhắn của bạn và sẽ phản hồi sớm.';
                    $_POST = []; // Clear form fields
                } catch (Exception $e) {
                    $error = 'Gửi email liên hệ thất bại. Thông tin đã được lưu, chúng tôi sẽ liên hệ lại sớm.';
                    error_log("PHPMailer failed: " . $mail->ErrorInfo);
                }
            }
        } else {
            $error = implode('<br>', $errors);
        }
    }
    // Regenerate CSRF token after submission
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ | Anh Em Rọt Store</title>
    <link rel="icon" href="/Apple_Shop/assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="/Apple_Shop/css/header.css">   
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #2d3748;
            --accent-color: #48bb78;
            --success-color: #48bb78;
            --danger-color: #f56565;
            --warning-color: #ed8936;
            --info-color: #4299e1;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .contact-us-section {
            padding: 4rem 0;
            background-color: var(--background-color);
        }
        .contact-info {
            background: var(--card-background);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .contact-info h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 1.5rem;
        }
        .contact-info p {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }
        .contact-info p i {
            margin-right: 0.8rem;
            color: var(--accent-color);
        }
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: var(--accent-color);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            font-size: 1.2rem;
            margin-right: 10px;
            transition: var(--transition);
        }
        .social-links a:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }
        .contact-form-container {
            background: var(--card-background);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            height: 100%;
        }
        .contact-form-container h3 {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: var(--transition);
            width: 100%;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(72, 187, 120, 0.25);
        }
        .submit-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }
        .submit-btn:hover {
            background-color: #38a169;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        .error-message, .success-message {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border-radius: 8px;
        }
        .error-message {
            background-color: #f8d7da;
            color: var(--danger-color);
        }
        .success-message {
            background-color: #d4edda;
            color: var(--accent-color);
        }
        @media (max-width: 768px) {
            .contact-info, .contact-form-container {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .contact-info h2 {
                font-size: 1.75rem;
            }
            .contact-form-container h3 {
                font-size: 1.5rem;
            }
            .contact-us-section {
                padding: 2rem 0;
            }
            .submit-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include '/Apple_Shop/includes/header.php'; ?>
    <main class="main-content">
        <section class="contact-us-section animate__animated animate__fadeInUp">
            <div class="container">
                <?php if (isset($error)): ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="success-message"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <div class="row align-items-stretch">
                    <div class="col-md-6 animate__animated animate__fadeInLeft">
                        <div class="contact-info">
                            <h2>Liên Hệ Trực Tiếp</h2>
                            <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($site_settings['address']) ?></p>
                            <p><i class="fas fa-phone-alt"></i> <?= htmlspecialchars($site_settings['phone_number']) ?></p>
                            <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($site_settings['contact_email']) ?></p>
                            <div class="social-links">
                                <a href="<?= htmlspecialchars($site_settings['facebook_url']) ?>" target="_blank"><i class="fab fa-facebook"></i></a>
                                <a href="<?= htmlspecialchars($site_settings['instagram_url']) ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                                <a href="<?= htmlspecialchars($site_settings['twitter_url']) ?>" target="_blank"><i class="fab fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 animate__animated animate__fadeInRight">
                        <div class="contact-form-container">
                            <h3>Gửi Tin Nhắn Cho Chúng Tôi</h3>
                            <form method="post" action="">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                <div class="form-group">
                                    <label for="name">Tên của bạn *</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên của bạn" required
                                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email của bạn *</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email của bạn" required
                                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Số điện thoại</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại của bạn"
                                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label for="message">Lời nhắn *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Nhập lời nhắn của bạn" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                </div>
                                <button type="submit" name="submit_contact" class="submit-btn"><i class="fas fa-paper-plane"></i> Gửi Tin Nhắn</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php include '/Apple_Shop/includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const animateElements = document.querySelectorAll('.animate__animated');
            function checkIfInView() {
                const windowHeight = window.innerHeight;
                const windowTopPosition = window.scrollY;
                const windowBottomPosition = windowTopPosition + windowHeight;
                animateElements.forEach(element => {
                    const elementHeight = element.offsetHeight;
                    const elementTopPosition = element.getBoundingClientRect().top + window.scrollY;
                    const elementBottomPosition = elementTopPosition + elementHeight;
                    if ((elementBottomPosition >= windowTopPosition) && (elementTopPosition <= windowBottomPosition)) {
                        let animationClass = 'animate__fadeIn';
                        if (element.classList.contains('animate__fadeInLeft')) {
                            animationClass = 'animate__fadeInLeft';
                        } else if (element.classList.contains('animate__fadeInRight')) {
                            animationClass = 'animate__fadeInRight';
                        } else if (element.classList.contains('animate__fadeInUp')) {
                            animationClass = 'animate__fadeInUp';
                        }
                        element.classList.add(animationClass);
                    }
                });
            }
            window.addEventListener('scroll', checkIfInView);
            window.addEventListener('load', checkIfInView);
        });
    </script>
</body>
</html>