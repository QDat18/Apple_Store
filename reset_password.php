<?php
session_start();
require_once 'config/db.php';

$errors = [];
$success = false;

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_user_id']) || !isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_otp_expires'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];
$user_id = $_SESSION['reset_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($otp) || empty($password) || empty($confirm_password)) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Mật khẩu nhập lại không khớp.";
    }
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        $errors[] = "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.";
    }
    if ($otp !== $_SESSION['reset_otp']) {
        $errors[] = "Mã OTP không đúng.";
    }
    if (time() > $_SESSION['reset_otp_expires']) {
        $errors[] = "Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.";
    }

    if (empty($errors)) {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND email = ?");
        $stmt->bind_param("sis", $hashed_password, $user_id, $email);

        if ($stmt->execute()) {
            $success = true;
            // Log password reset
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'reset_password', ?)");
            $details = "User reset password: $email";
            $log_stmt->bind_param("is", $user_id, $details);
            $log_stmt->execute();
            $log_stmt->close();

            // Clear session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_otp']);
            unset($_SESSION['reset_otp_expires']);
            $_SESSION['reset_attempts'] = 0;
        } else {
            $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
            error_log("SQL Error in reset_password.php: " . $stmt->error);
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
                <p class="auth-subtitle">Nhập mã OTP và mật khẩu mới</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="color: red; margin: 10px 0;">
                    <?php foreach ($errors as $e): ?>
                        <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" style="color: green; margin: 10px 0;">
                    <p>✅ Mật khẩu đã được đặt lại thành công! Bạn có thể <a href="login.php" class="form-link">đăng nhập ngay</a>.</p>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form class="auth-form" method="POST">
                    <div class="form-group">
                        <label for="otp" class="form-label">Mã OTP</label>
                        <input type="text" name="otp" id="otp" class="form-input" 
                               placeholder="Nhập mã OTP từ email" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" name="password" id="password" class="form-input" 
                               placeholder="Nhập mật khẩu mới" required>
                        <div class="password-strength-container">
                            <div class="password-strength-bar">
                                <div class="password-strength-fill"></div>
                            </div>
                            <div class="password-strength-text">Độ mạnh mật khẩu</div>
                            <div class="password-strength-tips">
                                <ul>
                                    <li><span class="icon length">❌</span>Ít nhất 8 ký tự</li>
                                    <li><span class="icon uppercase">❌</span>Chứa chữ hoa (A-Z)</li>
                                    <li><span class="icon number">❌</span>Chứa số (0-9)</li>
                                    <li><span class="icon special">❌</span>Chứa ký tự đặc biệt (@$!%*?&)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input" 
                               placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                    <button type="submit" class="auth-button">
                        <i class="fas fa-lock"></i> Đặt lại mật khẩu
                    </button>
                </form>
            <?php endif; ?>

            <div class="auth-footer">
                <a href="forgot_password.php" class="form-link">Gửi lại OTP</a>
                <span> | </span>
                <a href="login.php" class="form-link">Quay lại đăng nhập</a>
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
            
            document.querySelector('.auth-button').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
            document.querySelector('.auth-button').disabled = true;
        });

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthContainer = document.querySelector('.password-strength-container');
            const strengthFill = document.querySelector('.password-strength-fill');
            const strengthText = document.querySelector('.password-strength-text');
            const lengthIcon = document.querySelector('.icon.length');
            const uppercaseIcon = document.querySelector('.icon.uppercase');
            const numberIcon = document.querySelector('.icon.number');
            const specialIcon = document.querySelector('.icon.special');

            let strength = 0;

            // Check password criteria
            if (password.length >= 8) {
                strength++;
                lengthIcon.textContent = '✅';
                lengthIcon.classList.remove('invalid');
                lengthIcon.classList.add('valid');
            } else {
                lengthIcon.textContent = '❌';
                lengthIcon.classList.remove('valid');
                lengthIcon.classList.add('invalid');
            }

            if (/[A-Z]/.test(password)) {
                strength++;
                uppercaseIcon.textContent = '✅';
                uppercaseIcon.classList.remove('invalid');
                uppercaseIcon.classList.add('valid');
            } else {
                uppercaseIcon.textContent = '❌';
                uppercaseIcon.classList.remove('valid');
                uppercaseIcon.classList.add('invalid');
            }

            if (/[0-9]/.test(password)) {
                strength++;
                numberIcon.textContent = '✅';
                numberIcon.classList.remove('invalid');
                numberIcon.classList.add('valid');
            } else {
                numberIcon.textContent = '❌';
                numberIcon.classList.remove('valid');
                numberIcon.classList.add('invalid');
            }

            if (/[@$!%*?&]/.test(password)) {
                strength++;
                specialIcon.textContent = '✅';
                specialIcon.classList.remove('invalid');
                specialIcon.classList.add('valid');
            } else {
                specialIcon.textContent = '❌';
                specialIcon.classList.remove('valid');
                specialIcon.classList.add('invalid');
            }

            // Update progress bar and text
            strengthContainer.className = 'password-strength-container';
            switch(strength) {
                case 0:
                case 1:
                    strengthContainer.classList.add('strength-weak');
                    strengthText.textContent = 'Mật khẩu yếu';
                    break;
                case 2:
                    strengthContainer.classList.add('strength-medium');
                    strengthText.textContent = 'Mật khẩu trung bình';
                    break;
                case 3:
                    strengthContainer.classList.add('strength-good');
                    strengthText.textContent = 'Mật khẩu tốt';
                    break;
                case 4:
                    strengthContainer.classList.add('strength-strong');
                    strengthText.textContent = 'Mật khẩu mạnh';
                    break;
            }
        });
    </script>
</body>
</html>