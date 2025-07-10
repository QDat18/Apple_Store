<?php
session_start();
require_once 'config/db.php';

$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check token and expiration
    $stmt = $conn->prepare("SELECT id, email, is_verified FROM users WHERE verify_token = ? AND is_verified = 0 AND (verify_token_expires_at IS NULL OR verify_token_expires_at > NOW())");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $email, $is_verified);
        $stmt->fetch();
        
        // Update verification status
        $update_stmt = $conn->prepare("UPDATE users SET is_verified = 1, verify_token = NULL, verify_token_expires_at = NULL WHERE id = ?");
        $update_stmt->bind_param("i", $user_id);
        
        if ($update_stmt->execute()) {
            $success = true;
            $message = "Email đã được xác thực thành công! Bạn có thể đăng nhập ngay bây giờ.";
            
            // Log verification
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'verify_email', ?)");
            $details = "User verified email: $email";
            $log_stmt->bind_param("is", $user_id, $details);
            $log_stmt->execute();
            $log_stmt->close();
            
            // Clear pending session
            unset($_SESSION['pending_verify_email']);
            unset($_SESSION['verify_token']);
        } else {
            $message = "Có lỗi xảy ra khi xác thực email. Vui lòng thử lại.";
        }
        $update_stmt->close();
    } else {
        $message = "Token không hợp lệ, đã hết hạn hoặc email đã được xác thực trước đó.";
    }
    $stmt->close();
} else {
    $message = "Token xác thực không được cung cấp.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email | Anh Em Rọt Store</title>
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
                <h1 class="auth-title">Xác thực Email</h1>
                <p class="auth-subtitle">Kết quả xác thực tài khoản</p>
            </div>

            <div class="<?= $success ? 'alert alert-success' : 'alert alert-danger' ?>" 
                 style="color: <?= $success ? 'green' : 'red' ?>; margin: 20px 0; padding: 15px; border-radius: 5px; background: <?= $success ? '#d4edda' : '#f8d7da' ?>;">
                <i class="fas <?= $success ? 'fa-check-circle' : 'fa-times-circle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>

            <div class="auth-footer" style="text-align: center; margin-top: 30px;">
                <?php if ($success): ?>
                    <a href="login.php" class="auth-button" style="display: inline-block; text-decoration: none; padding: 12px 30px;">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập ngay
                    </a>
                <?php else: ?>
                    <a href="send_verification_email.php?resend=1" class="form-link">Gửi lại email xác thực</a>
                    <span> hoặc </span>
                    <a href="register.php" class="form-link">Đăng ký lại</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>