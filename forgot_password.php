<?php
session_start();
require_once 'config/db.php';
require_once 'send_otp_email.php';

$errors = [];
$success = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (!$email) {
        $errors[] = "Vui lòng nhập email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($user_id);
            $stmt->fetch();
            
            $reset_token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt_reset = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt_reset->bind_param("sss", $email, $reset_token, $expires_at);
            
            if ($stmt_reset->execute() && send_otp_email($email, $reset_token)) {
                $success = true;
                $message = "Email đặt lại mật khẩu đã được gửi. Vui lòng kiểm tra hộp thư của bạn (bao gồm thư mục spam/thư rác).";
            } else {
                $errors[] = "Có lỗi khi gửi email. Vui lòng thử lại.";
            }
            $stmt_reset->close();
        } else {
            $errors[] = "Email không tồn tại.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu | Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <main class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1 class="auth-title">Quên mật khẩu</h1>
                <p class="auth-subtitle">Nhập email để nhận liên kết đặt lại mật khẩu</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="color: red; margin: 10px 0;">
                    <?php foreach ($errors as $e): ?>
                        <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" style="color: green; margin: 10px 0;">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-input" 
                           placeholder="Nhập email đã đăng ký" 
                           value="<?= htmlspecialchars($email) ?>" 
                           required>
                </div>
                <button type="submit" class="auth-button">
                    <i class="fas fa-paper-plane"></i> Gửi liên kết đặt lại
                </button>
            </form>
            <div class="auth-footer">
                <a href="login.php" class="form-link">Quay lại đăng nhập</a>
                <span> | </span>
                <a href="register.php" class="form-link">Đăng ký ngay</a>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
</body>
</html>