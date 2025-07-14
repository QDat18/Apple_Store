<?php
session_start();
require_once 'config/db.php'; // Đảm bảo đường dẫn đến file db.php là đúng
require_once 'includes/functions.php'; // Chứa sanitize_input và log_action (phải tương thích MySQLi)

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
    exit;
}

$user_id = $_SESSION['user_id'];
$user = null;
$user_detail = null; // Biến mới để lưu thông tin chi tiết
$error_message = '';
$success_message = '';

// Đường dẫn upload ảnh đại diện
$upload_dir = 'uploads/avatars/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Đảm bảo thư mục tồn tại và có quyền ghi
}

// Lấy thông tin người dùng từ cơ sở dữ liệu (JOIN với user_detail)
try {
    $stmt = $conn->prepare("
        SELECT u.full_name, u.email, u.phone, u.address, u.password,
               ud.gender, ud.avatar
        FROM users u
        LEFT JOIN user_detail ud ON u.id = ud.user_id
        WHERE u.id = ?
    ");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();

    if (!$user_data) {
        session_destroy();
        header('Location: login.php');
        exit;
    }

    // Tách dữ liệu thành thông tin users và user_detail để dễ quản lý
    $user = [
        'full_name' => $user_data['full_name'],
        'email' => $user_data['email'],
        'phone' => $user_data['phone'],
        'address' => $user_data['address'],
        'password' => $user_data['password'] // Để kiểm tra mật khẩu hiện tại
    ];

    $user_detail = [
        'gender' => $user_data['gender'],
        'avatar' => $user_data['avatar']
    ];

} catch (Exception $e) {
    $error_message = "Lỗi khi tải thông tin người dùng: " . $e->getMessage();
}

// Xử lý khi người dùng gửi biểu mẫu cập nhật thông tin cá nhân
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $full_name = sanitize_input($_POST['full_name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);

    // Thông tin từ user_detail
    $gender = sanitize_input($_POST['gender']);

    $avatar_path = $user_detail['avatar']; // Giữ avatar cũ mặc định

    // Xử lý upload ảnh đại diện
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['avatar']['tmp_name'];
        // Tạo tên file duy nhất để tránh trùng lặp
        $file_name = uniqid('avatar_') . '_' . basename($_FILES['avatar']['name']);
        $target_file = $upload_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra định dạng ảnh
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $error_message = "Chỉ cho phép định dạng JPG, JPEG, PNG & GIF.";
        }
        // Kiểm tra kích thước file (ví dụ: tối đa 5MB)
        else if ($_FILES['avatar']['size'] > 5 * 1024 * 1024) {
            $error_message = "Kích thước ảnh quá lớn, tối đa 5MB.";
        }
        else {
            if (move_uploaded_file($file_tmp_name, $target_file)) {
                // Xóa avatar cũ nếu có và khác avatar mặc định (nếu bạn có avatar mặc định cứng)
                // Hoặc chỉ xóa nếu nó không phải là avatar mặc định ban đầu
                if (!empty($user_detail['avatar']) && file_exists($user_detail['avatar'])) {
                    unlink($user_detail['avatar']);
                }
                $avatar_path = $target_file;
            } else {
                $error_message = "Có lỗi khi tải ảnh lên.";
            }
        }
    }

    if (empty($error_message)) {
        // Cập nhật bảng `users`
        try {
            // Kiểm tra xem email đã tồn tại với người dùng khác chưa
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param('si', $email, $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error_message = "Email này đã được sử dụng bởi một tài khoản khác.";
                $stmt->close();
            } else {
                $stmt->close(); // Đóng statement trước khi tạo cái mới

                $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->bind_param('ssssi', $full_name, $email, $phone, $address, $user_id);
                $stmt->execute();
                // Không cần kiểm tra if execute() thành công ở đây, lỗi sẽ được bắt bởi catch block nếu có
                $stmt->close();

                // Cập nhật hoặc chèn vào bảng `user_detail`
                // Dùng INSERT ... ON DUPLICATE KEY UPDATE để xử lý cả trường hợp tạo mới và cập nhật
                $stmt = $conn->prepare("
                    INSERT INTO user_detail (user_id, gender, avatar)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        gender = VALUES(gender),
                        avatar = VALUES(avatar)
                ");
                $stmt->bind_param('iss', $user_id, $gender, $avatar_path);

                if ($stmt->execute()) {
                    $success_message = "Cập nhật thông tin thành công!";
                    // Cập nhật lại biến $user và $user_detail để hiển thị thông tin mới
                    $user['full_name'] = $full_name;
                    $user['email'] = $email;
                    $user['phone'] = $phone;
                    $user['address'] = $address;

                    $user_detail['gender'] = $gender;
                    $user_detail['avatar'] = $avatar_path;

                    log_action($_SESSION['user_id'], 'update_profile', "User updated their profile information.");
                } else {
                    $error_message = "Có lỗi xảy ra khi cập nhật thông tin chi tiết: " . $stmt->error;
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            $error_message = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
        }
    }
}

// Xử lý đổi mật khẩu (giữ nguyên logic đã sửa cho MySQLi)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = sanitize_input($_POST['current_password']);
    $new_password = sanitize_input($_POST['new_password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);

    // Lấy mật khẩu hiện tại của người dùng từ DB để xác minh
    try {
        // Mật khẩu đã được lấy ở phần tải thông tin người dùng $user['password']
        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->bind_param('si', $new_password_hash, $user_id);

                if ($stmt->execute()) {
                    $success_message = "Mật khẩu đã được thay đổi thành công!";
                    // Cập nhật mật khẩu trong biến $user để tránh tải lại
                    $user['password'] = $new_password_hash;
                    log_action($_SESSION['user_id'], 'change_password', "User changed their password.");
                } else {
                    $error_message = "Có lỗi xảy ra khi thay đổi mật khẩu: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
            }
        } else {
            $error_message = "Mật khẩu hiện tại không đúng.";
        }
    } catch (Exception $e) {
        $error_message = "Lỗi cơ sở dữ liệu khi đổi mật khẩu: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin tài khoản - Anh Em Rot Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #2d3748;
            --accent-color: #667eea;
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            flex-grow: 1;
        }

        h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 2rem;
            font-size: 2.2rem;
        }

        .profile-section {
            margin-bottom: 2.5rem;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--background-color);
        }

        .profile-section h2 {
            font-size: 1.8rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea {
            width: calc(100% - 20px);
            padding: 0.75rem 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            background-color: var(--card-background);
            color: var(--text-primary);
            transition: border-color var(--transition);
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group input[type="password"]:focus,
        .form-group input[type="file"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-color);
        }

        .btn-submit {
            display: inline-block;
            padding: 0.8rem 1.8rem;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-submit:hover {
            background: #5a67d8; /* Slightly darker accent */
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .message {
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            font-weight: 500;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Styles for avatar display (similar to dashboard_user.php) */
        .avatar-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .avatar {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
            margin: 0 auto; /* Center the image */
            display: block; /* Ensure it's treated as a block for centering */
        }
        .default-avatar {
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem auto;
                padding: 1rem;
            }
            h1 {
                font-size: 1.8rem;
            }
            .profile-section h2 {
                font-size: 1.5rem;
            }
            .form-group input,
            .form-group select,
            .form-group textarea {
                width: calc(100% - 20px);
            }
            .btn-submit {
                width: 100%;
                text-align: center;
                padding: 0.6rem 1.2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Thông tin tài khoản</h1>

        <?php if ($error_message): ?>
            <div class="message error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php if ($user && $user_detail): ?>
            <div class="profile-section">
                <h2>Thông tin cá nhân của bạn</h2>
                <div class="row">
                    <div class="col-md-3 avatar-container">
                        <?php if (!empty($user_detail['avatar']) && file_exists($user_detail['avatar'])): ?>
                            <img src="<?= htmlspecialchars($user_detail['avatar']) ?>" class="avatar" alt="Ảnh đại diện">
                        <?php else: ?>
                            <div class="avatar default-avatar">
                                <?= htmlspecialchars(strtoupper(substr($user['full_name'], 0, 1) ?: '?')) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-9">
                        <p><strong>Họ và Tên:</strong> <?= htmlspecialchars($user['full_name']) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($user['address'] ?? 'Chưa cập nhật') ?></p>
                        <p><strong>Giới tính:</strong> <?= htmlspecialchars($user_detail['gender'] ?? 'Chưa cập nhật') ?></p>
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h2>Cập nhật thông tin cá nhân</h2>
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="full_name">Họ và Tên:</label>
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại:</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="address">Địa chỉ:</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                    </div>
                     <div class="form-group">
                        <label for="gender">Giới tính:</label>
                        <select id="gender" name="gender">
                            <option value="">Chọn giới tính</option>
                            <option value="Nam" <?= ($user_detail['gender'] === 'Nam') ? 'selected' : '' ?>>Nam</option>
                            <option value="Nữ" <?= ($user_detail['gender'] === 'Nữ') ? 'selected' : '' ?>>Nữ</option>
                            <option value="Khác" <?= ($user_detail['gender'] === 'Khác') ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="avatar">Ảnh đại diện:</label>
                        <input type="file" id="avatar" name="avatar" accept="image/*">
                        <?php if (!empty($user_detail['avatar'])): ?>
                            <small>Ảnh hiện tại: <a href="<?= htmlspecialchars($user_detail['avatar']) ?>" target="_blank">Xem ảnh</a></small>
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="update_profile" class="btn-submit">Cập nhật thông tin</button>
                </form>
            </div>

            <div class="profile-section">
                <h2>Đổi mật khẩu</h2>
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại:</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu mới:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-submit">Đổi mật khẩu</button>
                </form>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: var(--danger-color);">Không tìm thấy thông tin người dùng.</p>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>