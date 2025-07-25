<?php

require_once __DIR__ . '/../config/db.php'; 

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmailActual($recipient_email, $subject, $message_body) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình Server SMTP của bạn
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Thay bằng host SMTP của bạn (ví dụ: smtp.gmail.com)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'dathoami2k5@gmail.com'; // Thay bằng email của bạn
        $mail->Password   = 'pmmy ddcn xulj ruvb'; // Thay bằng Mật khẩu ứng dụng (App Password) của email bạn
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Hoặc PHPMailer::ENCRYPTION_SMTPS cho cổng 465
        $mail->Port       = 587; // Hoặc 465 cho SMTPS
        $mail->CharSet    = 'UTF-8'; // Đặt bộ ký tự cho email là UTF-8

        // Người gửi (nên khớp với Username)
        $mail->setFrom('dathoami2k5@gmail.com', 'Anh Em Rọt Store');
        $mail->addAddress($recipient_email); // Thêm người nhận

        $mail->isHTML(true); // Đặt định dạng email là HTML
        $mail->Subject = $subject; // Tiêu đề email
        $mail->Body    = $message_body; // Nội dung HTML của email
        $mail->AltBody = strip_tags($message_body); // Nội dung thay thế cho email không hỗ trợ HTML

        $mail->send(); // Gửi email
        return true; // Trả về true nếu gửi thành công
    } catch (Exception $e) {
        // Ghi log lỗi chi tiết vào một file log
        error_log(date('Y-m-d H:i:s') . " - Error sending to {$recipient_email}: {$mail->ErrorInfo} - Exception: {$e->getMessage()}\n", 3, __DIR__ . '/../logs/email_errors.log');
        return $mail->ErrorInfo; // Trả về thông báo lỗi nếu gửi thất bại
    }
}

// Bắt đầu quá trình xử lý hàng đợi
echo "Script email queue started at " . date('Y-m-d H:i:s') . "\n";

// Lấy các email đang chờ gửi từ hàng đợi
// Giới hạn 50 email mỗi lần chạy để tránh quá tải server hoặc SMTP rate limits
// Bạn có thể điều chỉnh LIMIT tùy theo hiệu suất server và giới hạn của nhà cung cấp SMTP
$stmt = $conn->prepare("SELECT id, recipient_email, subject, body FROM email_queue WHERE status = 'pending' ORDER BY created_at ASC LIMIT 50"); 
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    $emails_processed = 0;
    $emails_sent = 0;
    $emails_failed = 0;

    while ($row = $result->fetch_assoc()) {
        $email_id = $row['id'];
        $recipient_email = $row['recipient_email'];
        $subject = $row['subject'];
        $body = $row['body'];

        $emails_processed++;
        echo "Processing email ID: {$email_id} to {$recipient_email}...\n";

        $send_result = sendEmailActual($recipient_email, $subject, $body);

        if ($send_result === true) {
            // Cập nhật trạng thái thành 'sent' và ghi thời gian gửi
            $update_stmt = $conn->prepare("UPDATE email_queue SET status = 'sent', sent_at = NOW() WHERE id = ?");
            if ($update_stmt) {
                $update_stmt->bind_param("i", $email_id);
                $update_stmt->execute();
                $update_stmt->close();
                $emails_sent++;
                echo "Email ID {$email_id} sent successfully.\n";
            } else {
                error_log(date('Y-m-d H:i:s') . " - Failed to prepare update statement for sent email ID {$email_id}: " . $conn->error);
            }
        } else {
            // Cập nhật trạng thái thành 'failed' và lưu thông báo lỗi
            $error_message = substr($send_result, 0, 500); // Cắt bớt lỗi nếu quá dài
            $update_stmt = $conn->prepare("UPDATE email_queue SET status = 'failed', error_message = ?, sent_at = NOW() WHERE id = ?");
            if ($update_stmt) {
                $update_stmt->bind_param("si", $error_message, $email_id);
                $update_stmt->execute();
                $update_stmt->close();
                $emails_failed++;
                echo "Email ID {$email_id} failed: {$error_message}\n";
            } else {
                error_log(date('Y-m-d H:i:s') . " - Failed to prepare update statement for failed email ID {$email_id}: " . $conn->error);
            }
        }
        usleep(500000); // Dừng 0.5 giây (500,000 micro giây) giữa mỗi email
    }
    $stmt->close();
    echo "Total emails processed: {$emails_processed}\n";
    echo "Emails sent: {$emails_sent}\n";
    echo "Emails failed: {$emails_failed}\n";
} else {
    error_log(date('Y-m-d H:i:s') . " - Failed to prepare queue selection statement: " . $conn->error);
    echo "Error: Could not prepare statement for email queue selection.\n";
}

$conn->close();
echo "Script email queue finished at " . date('Y-m-d H:i:s') . "\n";
?>