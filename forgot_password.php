<?php
session_start();
require_once 'config/db.php';

$errors = [];
$success = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Rate-limiting
    if (!isset($_SESSION['reset_attempts'])) {
        $_SESSION['reset_attempts'] = 0;
        $_SESSION['reset_last_attempt'] = time();
    }
    if ($_SESSION['reset_attempts'] >= 3 && (time() - $_SESSION['reset_last_attempt'] < 3600)) {
        $errors[] = "Quá nhiều lần thử yêu cầu OTP. Vui lòng thử lại sau 1 giờ.";
    } else {
        $_SESSION['reset_attempts']++;
        $_SESSION['reset_last_attempt'] = time();

        if (empty($email)) {
            $errors[] = "Vui lòng nhập email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email không hợp lệ.";
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id, is_verified FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($user_id, $is_verified);
                $stmt->fetch();

                if ($is_verified == 0) {
                    $errors[] = "Email này chưa được xác thực. Vui lòng <a href='send_verification_email.php?resend=1'>gửi lại email xác thực</a>.";
                } else {
                    // Generate OTP
                    $otp = sprintf("%06d", mt_rand(100000, 999999));
                    $_SESSION['reset_otp'] = $otp;
                    $_SESSION['reset_otp_expires'] = time() + 600;
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_user_id'] = $user_id;

                    // Send OTP email
                    require_once 'send_otp_email.php';
                    if (send_otp_email($email, $otp)) {
                        $success = true;

                        // Log OTP request
                        $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'request_otp', ?)");
                        $details = "User requested OTP for password reset: $email";
                        $log_stmt->bind_param("is", $user_id, $details);
                        $log_stmt->execute();
                        $log_stmt->close();
                    } else {
                        $errors[] = "Không thể gửi OTP. Vui lòng thử lại.";
                    }
                }
            } else {
                $errors[] = "Email không tồn tại.";
            }
            $stmt->close();
        }
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
                <p class="auth-subtitle">Nhập email để nhận mã OTP</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="color: red; margin: 10px 0;">
                    <?php foreach ($errors as $e): ?>
                        <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" style="color: green; margin: 10px 0;">
                    <p>✅ OTP đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư đến (hoặc thư mục spam/thư rác) và nhập OTP tại <a href="reset_password.php" class="form-link">trang đặt lại mật khẩu</a>.</p>
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
                    <i class="fas fa-paper-plane"></i> Gửi OTP
                </button>
            </form>

            <div class="auth-footer">
                <a href="login.php" class="form-link">Quay lại đăng nhập</a>
                <span> | </span>
                <a href="register.php" class="form-link">Đăng ký tài khoản</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.querySelector('.auth-form').addEventListener('submit', function() {
            document.querySelector('.auth-button').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
            document.querySelector('.auth-button').disabled = true;
        });
    </script>
</body>
</html>