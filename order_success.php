<?php
session_start();
require_once 'config/db.php'; // Đảm bảo đường dẫn đến file db.php là đúng

// Kiểm tra quyền admin (không ảnh hưởng đến chức năng gửi email, chỉ là kiểm tra bổ sung)
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
// Cho phép cả khách hàng truy cập trang này để xem thông báo đặt hàng thành công

$order_code = isset($_GET['order_code']) ? trim($_GET['order_code']) : '';
$order_data = isset($_SESSION['order_data']) ? $_SESSION['order_data'] : null;

if (!$order_code || !$order_data || $order_data['order_code'] !== $order_code) {
    header('Location: cart.php');
    exit;
}

// Gửi email khi tải trang (sau khi đặt hàng thành công)
require 'vendor/autoload.php'; // Đường dẫn đến autoload của Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf; // Di chuyển use statement ra ngoài hàm

$mail = new PHPMailer(true);
$pdfFileName = 'hoa-don-ban-hang-' . $order_data['order_code'] . '.pdf'; // Khai báo tên file PDF

try {
    // Cấu hình server
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Server SMTP của Gmail
    $mail->SMTPAuth = true;
    $mail->Username = 'dathoami2k5@gmail.com'; // Thay bằng email của bạn
    $mail->Password = 'pmmy ddcn xulj ruvb'; // Thay bằng mật khẩu ứng dụng của bạn
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8'; // Quan trọng: Đặt charset cho email

    // Người gửi
    $mail->setFrom('dathoami2k5@gmail.com', 'Anh Em Rọt Store'); // Email gửi đi phải khớp Username

    // Người nhận
    $mail->addAddress($order_data['email'], $order_data['full_name']);

    // Tạo và đính kèm file PDF
    $pdfContent = generatePDF($order_data); // Truyền $order_data vào hàm
    
    // Kiểm tra nếu tạo PDF thất bại
    if (!$pdfContent) {
        throw new Exception("Không thể tạo file PDF hóa đơn.");
    }

    file_put_contents($pdfFileName, $pdfContent);

    // Kiểm tra nếu file PDF không tồn tại sau khi tạo
    if (!file_exists($pdfFileName)) {
        throw new Exception("Không thể lưu file PDF hóa đơn tạm thời.");
    }

    $mail->addAttachment($pdfFileName);

    // Nội dung email
    $mail->isHTML(true);
    $mail->Subject = 'Hóa đơn bán hàng từ Anh Em Rọt Store - Đơn hàng ' . $order_data['order_code'];
    $mail->Body = '<h2>Cảm ơn bạn đã mua hàng!</h2>
                   <p>Kính gửi ' . htmlspecialchars($order_data['full_name']) . ',</p>
                   <p>Chúng tôi đã đính kèm hóa đơn bán hàng cho đơn hàng mã <strong>' . htmlspecialchars($order_data['order_code']) . '</strong> trong email này. Vui lòng kiểm tra thông tin.</p>
                   <p>Bạn có thể theo dõi trạng thái đơn hàng của mình tại tài khoản của bạn trên website của chúng tôi.</p>
                   <p>Trân trọng,<br>Anh Em Rọt Store</p>';

    $mail->send();
    // Không cần unlink ở đây, sẽ unlink trong finally block
} catch (Exception $e) {
    // Lưu lỗi vào log hoặc thông báo cho admin nếu cần
    error_log("Gửi email thất bại: " . $e->getMessage());
    // Gán lỗi để hiển thị trên trang
    // Bạn có thể giữ $mail->ErrorInfo nếu muốn hiển thị chi tiết lỗi của PHPMailer
    // $email_send_error = "Không thể gửi email hóa đơn: " . $mail->ErrorInfo;
    $email_send_error = "Không thể gửi email hóa đơn. Vui lòng kiểm tra lại địa chỉ email hoặc liên hệ hỗ trợ.";

} finally {
    // Luôn xóa file PDF tạm thời
    if (file_exists($pdfFileName)) {
        unlink($pdfFileName);
    }
}


// Hàm tạo PDF
function generatePDF($order_data) { // Nhận $order_data làm tham số
    try {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']); // Cấu hình UTF-8
        
        // Cần font hỗ trợ tiếng Việt nếu không có thì mặc định sẽ không hiển thị được
        // Bạn có thể thêm font vào thư mục vendor/mpdf/mpdf/ttfonts/
        // Ví dụ: $mpdf->fontdata['DejaVuSansCondensed'] = ['R' => 'DejaVuSansCondensed.ttf', 'B' => 'DejaVuSansCondensed-Bold.ttf'];
        // Sau đó set font: $mpdf->SetFont('DejaVuSansCondensed');
        
        $mpdf->WriteHTML('
            <h1 style="text-align: center; color: #333;">Hóa Đơn Bán Hàng</h1>
            <p style="text-align: center; font-size: 14px; color: #555;">Mã đơn hàng: <strong>' . htmlspecialchars($order_data['order_code']) . '</strong></p>
            <p style="text-align: center; font-size: 12px; color: #777;">Ngày phát hành: ' . date('d/m/Y, H:i A T') . '</p>
            <hr style="border: 0.5px solid #eee; margin: 20px 0;">
            <h3>Thông tin khách hàng:</h3>
            <p><strong>Họ và tên:</strong> ' . htmlspecialchars($order_data['full_name']) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($order_data['email']) . '</p>
            <p><strong>Số điện thoại:</strong> ' . htmlspecialchars($order_data['phone']) . '</p>
            <p><strong>Địa chỉ:</strong> ' . htmlspecialchars($order_data['address'] . ', ' . $order_data['ward'] . ', ' . $order_data['district'] . ', ' . $order_data['city']) . '</p>
            <hr style="border: 0.5px solid #eee; margin: 20px 0;">
            <h3>Chi tiết sản phẩm:</h3>
            <table border="1" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; text-align: left;">Sản phẩm</th>
                        <th style="padding: 8px; text-align: left;">Dung lượng/Màu</th>
                        <th style="padding: 8px; text-align: center;">Số lượng</th>
                        <th style="padding: 8px; text-align: right;">Giá</th>
                        <th style="padding: 8px; text-align: right;">Tổng</th>
                    </tr>
                </thead>
                <tbody>
                ' . implode('', array_map(function($item) {
                    // Áp dụng giảm giá 0.9 cho product_id 1 và 2 (iPhone 15 Pro Max, iPhone 15 Pro)
                    // Đây là logic bạn đã có, tôi giữ nguyên
                    $item_price = ($item['product_id'] == 1 || $item['product_id'] == 2) ? $item['price'] * 0.9 : $item['price'];
                    $subtotal = $item_price * $item['quantity'];
                    return '<tr>
                        <td style="padding: 8px;">' . htmlspecialchars($item['name']) . '</td>
                        <td style="padding: 8px;">' . htmlspecialchars($item['storage'] . ' - ' . $item['color']) . '</td>
                        <td style="padding: 8px; text-align: center;">' . $item['quantity'] . '</td>
                        <td style="padding: 8px; text-align: right;">$' . number_format($item_price, 2) . '</td>
                        <td style="padding: 8px; text-align: right;">$' . number_format($subtotal, 2) . '</td>
                    </tr>';
                }, $order_data['items'])) . '
                </tbody>
            </table>
            <br>
            <p style="text-align: right; font-size: 16px;"><strong>Tổng cộng: $' . number_format($order_data['total'], 2) . '</strong></p>
            <p style="text-align: right; font-size: 14px;">Phương thức thanh toán: ' . ($order_data['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng' : ($order_data['payment_method'] === 'bank' ? 'Chuyển khoản ngân hàng' : 'Ví MoMo')) . '</p>
            <hr style="border: 0.5px solid #eee; margin: 20px 0;">
            <p style="text-align: center; color: green; font-weight: bold;">Cảm ơn bạn đã mua hàng! Chúng tôi rất trân trọng sự ủng hộ của bạn.</p>
            <p style="text-align: center; font-size: 10px; color: #aaa;">Hóa đơn được tạo tự động bởi hệ thống Anh Em Rọt Store.</p>
        ');

        return $mpdf->Output('', 'S'); // Trả về nội dung PDF dưới dạng string
    } catch (Exception $e) {
        error_log("Lỗi tạo PDF: " . $e->getMessage());
        return false; // Trả về false nếu có lỗi
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #2d3748;
            --accent-color: #667eea;
            --success-color: #48bb78;
            --danger-color: #f56565;
            --warning-color: #ed8936;
            --info-color: #4299e1;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
            text-align: center;
        }

        .success-message {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 2rem;
        }

        .success-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--success-color);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .success-details {
            font-size: 1rem;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        .success-details p {
            margin: 0.5rem 0;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--accent-color), var(--success-color));
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin: 0.5rem;
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .back-to-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: none;
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            border-radius: var(--border-radius);
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .back-to-home:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .success-title {
                font-size: 1.5rem;
            }

            .download-btn,
            .back-to-home {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="success-message">
            <h2 class="success-title">
                <i class="fas fa-check-circle"></i>
                Đặt hàng thành công!
            </h2>
            <div class="success-details">
                <p><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($order_data['order_code']) ?></p>
                <p><strong>Họ và tên:</strong> <?= htmlspecialchars($order_data['full_name']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order_data['address'] . ', ' . $order_data['ward'] . ', ' . $order_data['district'] . ', ' . $order_data['city']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order_data['email']) ?></p>
                <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order_data['phone']) ?></p>
                <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($order_data['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng' : ($order_data['payment_method'] === 'bank' ? 'Chuyển khoản ngân hàng' : 'Ví MoMo')) ?></p>
                <p><strong>Tổng tiền:</strong> $<?= number_format($order_data['total'], 2) ?></p>
            </div>
            <a href="sales_invoice.php?order_code=<?= htmlspecialchars($order_data['order_code']) ?>" class="download-btn"><i class="fas fa-file-pdf"></i> Xem và tải hóa đơn</a>
            <a href="index.php" class="back-to-home"><i class="fas fa-home"></i> Về trang chủ</a>
            <?php if (isset($email_send_error)): ?>
                <p style="color: var(--danger-color);">Gửi email thất bại: <?= htmlspecialchars($email_send_error) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>