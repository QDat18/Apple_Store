<?php
session_start();
require_once 'config/db.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$email || !$password) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password, role, is_verified FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $full_name, $hashed_password, $role, $is_verified);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                if ($is_verified == 0) {
                    $errors[] = "Tài khoản chưa được xác thực email. Vui lòng kiểm tra email hoặc <a href='send_verification_email.php?resend=1&email=" . urlencode($email) . "'>gửi lại email xác thực</a>.";
                } else {
                    $_SESSION['user_id'] = $id;
                    $_SESSION['full_name'] = $full_name;
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $role;

                    if (empty($full_name)) {
                        header("Location: update_profile.php?first_login=1");
                    } else {
                        header("Location: index.php");
                    }
                    exit;
                }
            } else {
                $errors[] = "Mật khẩu không đúng.";
            }
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
    <title>Đăng nhập | Anh Em Rọt Store</title>
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
                <h1 class="auth-title">Đăng nhập</h1>
                <p class="auth-subtitle">Đăng nhập để tiếp tục mua sắm</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="color: red; margin: 10px 0;">
                    <?php foreach ($errors as $e): ?>
                        <p><?= $e ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-input" 
                           placeholder="Nhập email của bạn" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                           required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" name="password" id="password" class="form-input" 
                           placeholder="Nhập mật khẩu" required>
                </div>
                <div class="form-checkbox">
                    <input type="checkbox" id="remember-me">
                    <label for="remember-me">Ghi nhớ tài khoản</label>
                    <a href="forgot_password.php" class="form-link">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="auth-button">Đăng nhập</button>
            </form>

            <div class="auth-footer">
                <p>Chưa có tài khoản? <a href="register.php" class="form-link">Đăng ký ngay</a></p>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>