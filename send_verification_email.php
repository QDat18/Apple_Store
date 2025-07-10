<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_verification_email($email, $verify_token) {
    $site_name = "Anh Em Rọt Store";
    $site_url = "http://localhost/Apple_Shop"; // Cập nhật domain thực tế
    $verify_url = $site_url . "/verify_email.php?token=" . $verify_token;
    
    $subject = "Xác thực tài khoản - " . $site_name;
    
    $message = "    
    <!DOCTYPE html>
    <html lang='vi'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Xác thực tài khoản</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .button { display: inline-block; background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; text-align: center; }
            .button:hover { background: #0056b3; }
            .footer { text-align: center; color: #666; margin-top: 30px; font-size: 14px; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>🛒 " . $site_name . "</h1>
            <p>Chào mừng bạn đến với cửa hàng của chúng tôi!</p>
        </div>
        <div class='content'>
            <h2>Xác thực tài khoản của bạn</h2>
            <p>Xin chào,</p>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>" . $site_name . "</strong>!</p>
            <p>Để hoàn tất quá trình đăng ký, vui lòng nhấp vào nút bên dưới để xác thực email của bạn (link có hiệu lực trong 24 giờ):</p>
            <div style='text-align: center;'>
                <a href='" . $verify_url . "' class='button'>✉️ Xác thực Email</a>
            </div>
            <p>Hoặc bạn có thể sao chép và dán đường link sau vào trình duyệt:</p>
            <p style='background: #e9ecef; padding: 10px; border-radius: 5px; word-break: break-all;'>
                <a href='" . $verify_url . "'>" . $verify_url . "</a>
            </p>
            <div class='warning'>
                <strong>⚠️ Lưu ý quan trọng:</strong>
                <ul>
                    <li>Link xác thực này chỉ có hiệu lực trong 24 giờ.</li>
                    <li>Nếu bạn không thực hiện đăng ký này, vui lòng bỏ qua email.</li>
                    <li>Sau khi xác thực, bạn sẽ cần cập nhật thông tin cá nhân để hoàn tất đăng ký.</li>
                </ul>
            </div>
            <p>Nếu bạn gặp bất kỳ vấn đề nào, vui lòng liên hệ với chúng tôi qua email support@yourdomain.com.</p>
            <p>Trân trọng,<br><strong>Đội ngũ " . $site_name . "</strong></p>
        </div>
        <div class='footer'>
            <p>© 2025 " . $site_name . ". Tất cả các quyền được bảo lưu.</p>
            <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        </div>
    </body>
    </html>";

    $mail = new PHPMailer(true);
    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Hoặc dịch vụ SMTP khác
        $mail->SMTPAuth = true;
        $mail->Username = 'dathoami2k5@gmail.com'; // Thay bằng email của bạn
        $mail->Password = 'pmmy ddcn xulj ruvb'; // Thay bằng mật khẩu ứng dụng
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Thiết lập thông tin người gửi và người nhận
        $mail->setFrom('noreply@yourdomain.com', $site_name);
        $mail->addAddress($email);
        $mail->addReplyTo('support@yourdomain.com', 'Hỗ trợ');

        // Cấu hình email
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send verification email to: $email. Error: {$mail->ErrorInfo}");
        return false;
    }
}

function resend_verification_email($email) {
    global $conn;

    // Giới hạn gửi lại email
    if (!isset($_SESSION['resend_attempts'])) {
        $_SESSION['resend_attempts'] = 0;
        $_SESSION['resend_last_attempt'] = time();
    }
    if ($_SESSION['resend_attempts'] >= 3 && (time() - $_SESSION['resend_last_attempt'] < 3600)) {
        return array('success' => false, 'message' => 'Quá nhiều lần gửi lại email. Vui lòng thử lại sau 1 giờ.');
    }
    $_SESSION['resend_attempts']++;
    $_SESSION['resend_last_attempt'] = time();

    // Kiểm tra email
    $stmt = $conn->prepare("SELECT id, verify_token, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $verify_token, $is_verified);
        $stmt->fetch();

        if ($is_verified == 1) {
            return array('success' => false, 'message' => 'Email đã được xác thực trước đó.');
        }

        // Tạo token mới nếu cần
        if (empty($verify_token)) {
            $verify_token = bin2hex(random_bytes(32));
            $verify_token_expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
            $update_stmt = $conn->prepare("UPDATE users SET verify_token = ?, verify_token_expires_at = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $verify_token, $verify_token_expires_at, $user_id);
            $update_stmt->execute();
            $update_stmt->close();
        }

        // Gửi email
        if (send_verification_email($email, $verify_token)) {
            $_SESSION['resend_attempts'] = 0; // Đặt lại số lần thử
            return array('success' => true, 'message' => 'Email xác thực đã được gửi lại. Vui lòng kiểm tra email (bao gồm thư mục spam/thư rác).');
        } else {
            return array('success' => false, 'message' => 'Có lỗi khi gửi email. Vui lòng thử lại.');
        }
    } else {
        return array('success' => false, 'message' => 'Email không tồn tại.');
    }
    $stmt->close();
}

if (isset($_GET['resend'])) {
    session_start();
    require_once 'config/db.php';

    $message = '';
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);
        if (!empty($email)) {
            $result = resend_verification_email($email);
            $success = $result['success'];
            $message = $result['message'];
        } else {
            $message = 'Vui lòng nhập email.';
        }
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gửi lại email xác thực | Anh Em Rọt Store</title>
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
                <h1 class="auth-title">Gửi lại email xác thực</h1>
                <p class="auth-subtitle">Nhập email để nhận lại link xác thực</p>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="<?= $success ? 'alert alert-success' : 'alert alert-danger' ?>" 
                     style="color: <?= $success ? 'green' : 'red' ?>; margin: 10px 0;">
                    <p><?= htmlspecialchars($message) ?></p>
                </div>
            <?php endif; ?>
            
            <form class="auth-form" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-input" 
                           placeholder="Nhập email đã đăng ký" required>
                </div>
                <button type="submit" class="auth-button">
                    <i class="fas fa-paper-plane"></i> Gửi lại email xác thực
                </button>
            </form>
            
            <div class="auth-footer">
                <a href="login.php" class="form-link">Quay lại đăng nhập</a>
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
<?php
}
?>