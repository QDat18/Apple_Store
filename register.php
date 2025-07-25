<?php
session_start();
require_once 'config/db.php';
require_once 'send_verification_email.php';

$errors = [];
$success = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $captcha = $_POST['captcha'] ?? '';
    $captcha_code = $_SESSION['captcha_code'] ?? '';

    // Validation
    if (!$email || !$password || !$confirm_password || !$captcha) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ.";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Mật khẩu nhập lại không khớp.";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }
    
    if (strtolower($captcha) !== strtolower($captcha_code)) {
        $errors[] = "Mã xác nhận không đúng.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $errors[] = "Email đã được đăng ký.";
        }
        $stmt->close();
    }

    // Register user if no errors
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $role = 'customer';
        $verify_token = bin2hex(random_bytes(32));
        $verify_token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $is_updated = 0;

        $stmt = $conn->prepare("INSERT INTO users (email, password, role, verify_token, verify_token_expires_at, is_verified, is_updated) VALUES (?, ?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("sssssi", $email, $hashed_password, $role, $verify_token, $verify_token_expires_at, $is_updated);
        
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            
            // Tạo bản ghi rỗng trong user_detail
            $detail_stmt = $conn->prepare("INSERT INTO user_detail (user_id, first_name, last_name, avatar, address, phone_number, gender, date_of_birth) VALUES (?, '', '', '', '', '', '', NULL)");
            $detail_stmt->bind_param("i", $user_id);
            $detail_stmt->execute();
            $detail_stmt->close();
            
            $success = true;
            $_SESSION['pending_verify_email'] = $email;
            $_SESSION['verify_token'] = $verify_token;
            
            if (!send_verification_email($email, $verify_token)) {
                $errors[] = "Đăng ký thành công nhưng không thể gửi email xác thực. Vui lòng liên hệ hỗ trợ.";
            }
        } else {
            $errors[] = "Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.";
        }
        $stmt->close();
    }
}

// Generate new captcha code
$captcha_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
$_SESSION['captcha_code'] = $captcha_code;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký | Anh Em Rọt Store</title>
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
                <h1 class="auth-title">Đăng ký</h1>
                <p class="auth-subtitle">Tạo tài khoản để bắt đầu mua sắm</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" style="color: red; margin: 10px 0;">
                    <?php foreach ($errors as $e): ?>
                        <p><?= htmlspecialchars($e) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($success): ?>
                <div class="alert alert-success" style="color: green; margin: 10px 0;">
                    <p>✅ Đăng ký thành công! Vui lòng kiểm tra email để xác nhận tài khoản trước khi đăng nhập.</p>
                    <p>Sau khi xác nhận email, bạn sẽ cần cập nhật đầy đủ thông tin cá nhân khi đăng nhập lần đầu.</p>
                    <p style="margin-top: 15px;">
                        <a href="send_verification_email.php?resend=1" class="form-link">
                            <i class="fas fa-paper-plane"></i> Gửi lại email xác thực
                        </a>
                    </p>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-input" 
                           placeholder="Nhập email của bạn" 
                           value="<?= htmlspecialchars($email) ?>" 
                           required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" name="password" id="password" class="form-input" 
                           placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)" 
                           required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-input" 
                           placeholder="Nhập lại mật khẩu" 
                           required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="captcha" class="form-label">Mã xác nhận</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="text" name="captcha" id="captcha" class="form-input" 
                               placeholder="Nhập mã xác nhận" 
                               style="flex: 1;" required>
                        <span style="font-weight: bold; font-size: 18px; padding: 8px 14px; 
                                   background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; 
                                   letter-spacing: 2px; user-select: none;">
                            <?= $captcha_code ?>
                        </span>
                    </div>
                </div>
                <button type="submit" class="auth-button">Đăng ký</button>
            </form>
            <div class="auth-footer">
                <span>Đã có tài khoản? </span>
                <a href="login.php" class="form-link">Đăng nhập ngay</a>
            </div>
        </div>
    </main>
    <?php include 'includes/footer.php'; ?>
    <script>
        document.querySelector('span').addEventListener('click', function() {
            location.reload();
        });
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
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