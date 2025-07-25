<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_otp_email($email, $token) {
    $site_name = "Anh Em Rọt Store";
    $site_url = "http://localhost/apple_store";
    $reset_url = $site_url . "/reset_password.php?token=" . $token;
    
    $subject = "Đặt lại mật khẩu - " . $site_name;
    
    $message = "
    <!DOCTYPE html>
    <html lang='vi'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Đặt lại mật khẩu</title>
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
            <h2>Đặt lại mật khẩu</h2>
            <p>Xin chào,</p>
            <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản tại <strong>" . $site_name . "</strong>.</p>
            <p>Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu:</p>
            <div style='text-align: center;'>
                <a href='" . $reset_url . "' class='button'>🔑 Đặt lại mật khẩu</a>
            </div>
            <p>Hoặc sao chép và dán đường link sau vào trình duyệt:</p>
            <p style='background: #e9ecef; padding: 10px; border-radius: 5px; word-break: break-all;'>
                <a href='" . $reset_url . "'>" . $reset_url . "</a>
            </p>
            <div class='warning'>
                <strong>⚠️ Lưu ý:</strong>
                <ul>
                    <li>Link đặt lại mật khẩu có hiệu lực trong 1 giờ.</li>
                    <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</li>
                </ul>
            </div>
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
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dathoami2k5@gmail.com';
        $mail->Password = 'pmmy ddcn xulj ruvb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply.anhemrotstore@gmail.com', $site_name);
        $mail->addAddress($email);
        $mail->addReplyTo('hotro.anhemrotstore@gmail.com', 'Hỗ trợ');

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send reset email to: $email. Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>