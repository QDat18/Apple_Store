<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_otp_email($email, $otp) {
    $site_name = "Anh Em Rọt Store";
    $site_url = "http://localhost/Apple_Shop"; // Cập nhật domain thực tế
    
    $subject = "Mã OTP để đặt lại mật khẩu - " . $site_name;
    
    $message = "
    <!DOCTYPE html>
    <html lang='vi'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Mã OTP đặt lại mật khẩu</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .otp-code { font-size: 24px; font-weight: bold; background: #e9ecef; padding: 10px; text-align: center; border-radius: 5px; letter-spacing: 2px; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
            .footer { text-align: center; color: #666; margin-top: 30px; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>🛒 " . $site_name . "</h1>
            <p>Đặt lại mật khẩu tài khoản của bạn</p>
        </div>
        <div class='content'>
            <h2>Mã OTP của bạn</h2>
            <p>Xin chào,</p>
            <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản tại <strong>" . $site_name . "</strong>. Dưới đây là mã OTP của bạn (hiệu lực trong 10 phút):</p>
            <div class='otp-code'>" . $otp . "</div>
            <p>Nhập mã này vào trang đặt lại mật khẩu để tiếp tục.</p>
            <div class='warning'>
                <strong>⚠️ Lưu ý quan trọng:</strong>
                <ul>
                    <li>Mã OTP này chỉ có hiệu lực trong 10 phút.</li>
                    <li>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</li>
                    <li>Không chia sẻ mã OTP với bất kỳ ai.</li>
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
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'dathoami2k5@gmail.com'; // Thay bằng email của bạn
        $mail->Password = 'pmmy ddcn xulj ruvb'; // Thay bằng mật khẩu ứng dụng
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply@yourdomain.com', $site_name);
        $mail->addAddress($email);
        $mail->addReplyTo('support@yourdomain.com', 'Hỗ trợ');

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send OTP email to: $email. Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>