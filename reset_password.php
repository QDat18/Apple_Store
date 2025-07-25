<?php
session_start();
require_once 'config/db.php';

$errors = [];
$success = false;
$token = $_GET['token'] ?? '';

if (!$token) {
    $errors[] = "Token không hợp lệ.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!$password || !$confirm_password) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Mật khẩu nhập lại không khớp.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    } else {
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > CURRENT_TIMESTAMP");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($email);
            $stmt->fetch();
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update_stmt->bind_param("ss", $hashed_password, $email);
            
            if ($update_stmt->execute()) {
                $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                $delete_stmt->bind_param("s", $token);
                $delete_stmt->execute();
                $delete_stmt->close();
                
                $success = true;
                $message = "Mật khẩu đã được đặt lại thành công. Bạn có thể đăng nhập ngay bây giờ.";
            } else {
                $errors[] = "Có lỗi khi đặt lại mật khẩu. Vui lòng thử lại.";
            }
            $update_stmt->close();
        } else {
            $errors[] = "Token không hợp lệ hoặc đã hết hạn.";
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
    <title>Đặt lại mật khẩu | Anh Em Rọt Store</title>
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
                <h1 class="auth-title">Đặt lại mật khẩu</h1>
                <p class="auth-subtitle">Nhập mật khẩu mới cho tài khoản của bạn</p>
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
                    <a href="login.php" class="auth-button" style="display: inline-block; text-decoration: none; padding: 12px 30px;">
                        <i class="fas fa-sign-in-alt"></i> Đăng nhập ngay
                    </a>
                </div>
            <?php endif; ?>

            <?php if (!$success && $token): ?>
                <form class="auth-form" method="POST">
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" name="password" id="password" class="form-input" 
                               placeholder="Nhập mật khẩu mới (tối thiểu 6 ký tự)" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input" 
                               placeholder="Nhập lại mật khẩu mới" 
                               required>
                    </div>
                    <button type="submit" class="auth-button">
                        <i class="fas fa-save"></i> Đặt lại mật khẩu
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="auth-footer">
                <a href="login.php" class="form-link">Quay lại đăng nhập</a>
                <span> | </span>
                <a href="register.php" class="form-link">Đăng ký ngay</a>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
    <script>
        document.querySelector('.auth-form')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu nhập lại không khớp!');
                return false;
            }
            if (password.length < 6) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 6 ký tự!');
                return false;
            }
        });
    </script>
</body>
</html>