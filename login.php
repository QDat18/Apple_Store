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
        $stmt = $conn->prepare("SELECT id, password, role, is_verified, is_updated FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashedPass, $role, $is_verified, $is_updated);
            $stmt->fetch();

            if (password_verify($password, $hashedPass)) {
                if ($is_verified == 0) {
                    $errors[] = "Tài khoản chưa được xác thực email. Vui lòng kiểm tra email và xác thực tài khoản.";
                } else {
                    // Lấy thông tin từ user_detail
                    $detail_stmt = $conn->prepare("SELECT first_name, last_name FROM user_detail WHERE user_id = ?");
                    $detail_stmt->bind_param("i", $id);
                    $detail_stmt->execute();
                    $detail_stmt->bind_result($first_name, $last_name);
                    $detail_stmt->fetch();
                    $detail_stmt->close();

                    $_SESSION['user_id'] = $id;
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $role;
                    $_SESSION['first_name'] = $first_name ?? '';
                    $_SESSION['last_name'] = $last_name ?? '';

                    if ($is_updated == 0) {
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
    <?php include 'includes/header.php' ?>
    <main class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h1 class="auth-title">Đăng nhập</h1>
                <p class="auth-subtitle">Đăng nhập để tiếp tục mua sắm</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="color: red; margin: 10px 0;">
                    <?php foreach ($errors as $e): ?>
                        <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-input" placeholder="Nhập email của bạn"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" name="password" id="password" class="form-input" placeholder="Nhập mật khẩu"
                        required autocomplete="current-password">
                </div>
                <div class="form-checkbox" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <input type="checkbox" id="remember-me">
                        <label for="remember-me">Ghi nhớ tài khoản</label>
                    </div>
                    <a href="forgot_password.php" class="form-link"
                        style="font-size: 14px; text-decoration: none; color: #007bff;">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="auth-button">Đăng nhập</button>
            </form>
            <div class="auth-footer">
                <span>Chưa có tài khoản? </span>
                <a href="register.php" class="form-link">Đăng ký ngay</a>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php' ?>
    <script src="scripts/header.js"></script>
</body>

</html>