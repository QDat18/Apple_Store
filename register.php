<?php
session_start();
require_once 'config/db.php';

$errors = [];
$success = false;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate-limiting
    if (!isset($_SESSION['register_attempts'])) {
        $_SESSION['register_attempts'] = 0;
        $_SESSION['register_last_attempt'] = time();
    }
    if ($_SESSION['register_attempts'] >= 5 && (time() - $_SESSION['register_last_attempt'] < 3600)) {
        $errors[] = "Quá nhiều lần thử đăng ký. Vui lòng thử lại sau 1 giờ.";
    } else {
        $_SESSION['register_attempts']++;
        $_SESSION['register_last_attempt'] = time();

        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $captcha = trim(htmlspecialchars($_POST['captcha'] ?? ''));
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
        if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
            $errors[] = "Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.";
        }
        if (strtolower($captcha) !== strtolower($captcha_code)) {
            $errors[] = "Mã xác nhận không đúng.";
        }

        // Check if email exists
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

        // Register user
        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'customer';
            $verify_token = bin2hex(random_bytes(32));
            $full_name = '';
            $verify_token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, verify_token, is_verified, verify_token_expires_at) VALUES (?, ?, ?, ?, ?, 0, ?)");
            $stmt->bind_param("ssssss", $full_name, $email, $hashed_password, $role, $verify_token, $verify_token_expires_at);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                $success = true;
                $_SESSION['pending_verify_email'] = $email;
                $_SESSION['verify_token'] = $verify_token;
                $_SESSION['register_attempts'] = 0;

                // Log registration
                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'register', ?)");
                $details = "User registered with email: $email";
                $log_stmt->bind_param("is", $user_id, $details);
                $log_stmt->execute();
                $log_stmt->close();

                // Send verification email
                require_once 'send_verification_email.php';
                if (!send_verification_email($email, $verify_token)) {
                    $errors[] = "Đăng ký thành công nhưng không thể gửi email xác thực. Vui lòng liên hệ hỗ trợ.";
                }
            } else {
                $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
                error_log("SQL Error in register.php: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}

// Generate CAPTCHA
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
                    <p>✅ Đăng ký thành công! Vui lòng kiểm tra email (bao gồm thư mục spam hoặc thư rác) để xác nhận tài
                        khoản trước khi đăng nhập.</p>
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
                    <input type="email" name="email" id="email" class="form-input" placeholder="Nhập email của bạn"
                        value="<?= htmlspecialchars($email) ?>" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <input type="password" name="password" id="password" class="form-input"
                        placeholder="Nhập mật khẩu ít nhất 8 ký tự, chứa chữ hoa, số, ký tự đặc biệt" required
                        autocomplete="new-password">
                    <div class="password-strength-container">
                        <div class="password-strength-bar">
                            <div class="password-strength-fill"></div>
                        </div>
                        <div class="password-strength-text"></div>
                        <div class="password-strength-tips"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-input"
                        placeholder="Nhập lại mật khẩu" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="captcha" class="form-label">Mã xác nhận</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="text" name="captcha" id="captcha" class="form-input" placeholder="Nhập mã xác nhận"
                            required>
                        <span class="captcha-code" style="font-weight: bold; font-size: 18px; padding: 8px 14px; 
                                   background: #f0f0f0; border: 1px solid #ddd; border-radius: 4px; 
                                   letter-spacing: 2px; user-select: none;">
                            <?= $captcha_code ?>
                        </span>
                        <a href="#" class="captcha-refresh"><i class="fas fa-sync-alt"></i></a>
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
        // AJAX CAPTCHA refresh
        document.querySelector('.captcha-refresh').addEventListener('click', function (e) {
            e.preventDefault();
            fetch('generate_captcha.php')
                .then(response => response.text())
                .then(data => {
                    document.querySelector('.captcha-code').textContent = data;
                });
        });

        // Password strength indicator
        document.querySelector('.auth-form').addEventListener('submit', function (e) {
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

        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;
            const strengthContainer = document.querySelector('.password-strength-container');
            const strengthFill = document.querySelector('.password-strength-fill');
            const strengthText = document.querySelector('.password-strength-text');
            const strengthTips = document.querySelector('.password-strength-tips');

            let strength = 0;
            const tips = [];

            if (password.length >= 8) strength++;
            else tips.push("Mật khẩu cần ít nhất 8 ký tự.");
            if (/[A-Z]/.test(password)) strength++;
            else tips.push("Thêm ít nhất một chữ hoa (A-Z).");
            if (/[0-9]/.test(password)) strength++;
            else tips.push("Thêm ít nhất một số (0-9).");
            if (/[@$!%*?&]/.test(password)) strength++;
            else tips.push("Thêm ít nhất một ký tự đặc biệt (@$!%*?&).");

            strengthContainer.className = 'password-strength-container';
            switch (strength) {
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

            strengthTips.innerHTML = tips.length > 0 ? '<strong>Cần cải thiện:</strong><ul><li>' + tips.join('</li><li>') + '</li></ul>' : '';
        });

        // AJAX CAPTCHA refresh
        document.querySelector('.captcha-refresh').addEventListener('click', function (e) {
            e.preventDefault();
            fetch('generate_captcha.php')
                .then(response => response.text())
                .then(data => {
                    document.querySelector('.captcha-code').textContent = data;
                });
        });


        // Form validation and loading
        document.querySelector('.auth-form').addEventListener('submit', function (e) {
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
    </script>
</body>

</html>