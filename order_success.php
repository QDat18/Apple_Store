<?php
session_start();
require_once 'config/db.php';

// Check admin role (not affecting customers)
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Get order_code from URL
$order_code = isset($_GET['order_code']) ? trim($_GET['order_code']) : '';
$order_data = isset($_SESSION['order_data']) ? $_SESSION['order_data'] : null;

// Validate order_code and fetch from DB if session data is missing or invalid
if (!$order_code || !$order_data || $order_data['order_code'] !== $order_code) {
    if ($order_code && isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("
            SELECT
                o.order_code, o.full_name, o.email, o.phone_number AS phone, 
                o.shipping_address AS address, o.total_amount AS total, o.payment_method
            FROM orders o
            WHERE o.order_code = ? AND o.user_id = ?
        ");
        if (!$stmt) {
            error_log("Prepare failed for order query: " . $conn->error);
            $order_data = null;
        } else {
            $stmt->bind_param("si", $order_code, $_SESSION['user_id']);
            if (!$stmt->execute()) {
                error_log("Execute failed for order query: " . $stmt->error);
                $order_data = null;
            } else {
                $result = $stmt->get_result();
                $db_order_data = $result->fetch_assoc();
                $stmt->close();
                if ($db_order_data) {
                    $order_data = [
                        'order_code' => $db_order_data['order_code'],
                        'full_name' => $db_order_data['full_name'],
                        'email' => $db_order_data['email'],
                        'phone' => $db_order_data['phone'],
                        'address' => $db_order_data['address'],
                        'ward' => '',
                        'district' => '',
                        'city' => '',
                        'payment_method' => $db_order_data['payment_method'],
                        'total' => $db_order_data['total'],
                        'items' => []
                    ];
                    // Fetch order items
                    $stmt_items = $conn->prepare("
                        SELECT
                            oi.quantity, oi.price, pv.id AS variant_id, p.product_name AS name, 
                            pv.variant_image AS image,
                            (SELECT vav.value FROM variant_attribute_values vav 
                             JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id 
                             WHERE pval.variant_id = pv.id AND vav.attribute_id = 2) AS storage,
                            (SELECT vav.value FROM variant_attribute_values vav 
                             JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id 
                             WHERE pval.variant_id = pv.id AND vav.attribute_id = 1) AS color
                        FROM order_items oi
                        JOIN product_variants pv ON oi.variant_id = pv.id
                        JOIN products p ON pv.product_id = p.id
                        WHERE oi.order_id = (SELECT id FROM orders WHERE order_code = ?)
                    ");
                    if ($stmt_items) {
                        $stmt_items->bind_param("s", $order_code);
                        if ($stmt_items->execute()) {
                            $items_result = $stmt_items->get_result();
                            while ($item_row = $items_result->fetch_assoc()) {
                                $order_data['items'][] = $item_row;
                            }
                        } else {
                            error_log("Execute failed for order_items query: " . $stmt_items->error);
                        }
                        $stmt_items->close();
                    } else {
                        error_log("Prepare failed for order_items query: " . $conn->error);
                    }
                } else {
                    $order_data = null;
                }
            }
        }
    } else {
        $order_data = null;
    }
}

// Check required fields for display
$required_fields_display = ['order_code', 'full_name', 'email', 'phone', 'address', 'payment_method', 'total', 'items'];
foreach ($required_fields_display as $field) {
    if (!isset($order_data[$field]) || (is_array($order_data[$field]) && empty($order_data[$field]) && $field === 'items')) {
        error_log("Missing or empty field $field in order_data for display. Order Code: " . ($order_data['order_code'] ?? 'N/A'));
    }
}

// Send email with PDF
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf;

$mail = new PHPMailer(true);
$pdfFileName = 'hoa-don-ban-hang-' . $order_data['order_code'] . '.pdf';
$email_send_error = null;

try {
    $mail->isSMTP();
    $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = getenv('SMTP_USERNAME') ?: 'dathoami2k5@gmail.com';
    $mail->Password = getenv('SMTP_PASSWORD') ?: 'pmmy ddcn xulj ruvb';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = getenv('SMTP_PORT') ?: 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom(getenv('MAIL_FROM_ADDRESS') ?: 'dathoami2k5@gmail.com', getenv('MAIL_FROM_NAME') ?: 'Anh Em Rọt Store');
    $mail->addAddress($order_data['email'], $order_data['full_name']);

    $pdfContent = generatePDF($order_data);
    if (!$pdfContent) {
        throw new Exception("Không thể tạo file PDF hóa đơn.");
    }

    $temp_dir = __DIR__ . '/temp/';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0777, true);
    }
    $temp_pdf_path = $temp_dir . $pdfFileName;
    if (!file_put_contents($temp_pdf_path, $pdfContent)) {
        throw new Exception("Không thể lưu file PDF tạm thời.");
    }

    $mail->addAttachment($temp_pdf_path, $pdfFileName);
    $mail->isHTML(true);
    $mail->Subject = 'Hóa đơn bán hàng từ Anh Em Rọt Store - Đơn hàng ' . $order_data['order_code'];
    $mail->Body = '<h2>Cảm ơn bạn đã mua hàng!</h2>
                   <p>Kính gửi ' . htmlspecialchars($order_data['name']) . ',</p>
                   <p>Chúng tôi đã đính kèm hóa đơn bán hàng cho đơn hàng mã <strong>' . htmlspecialchars($order_data['order_code']) . '</strong>. Vui lòng kiểm tra thông tin.</p>
                   <p>Bạn có thể theo dõi trạng thái đơn hàng tại tài khoản của bạn trên website của chúng tôi.</p>
                   <p>Trân trọng,<br>Anh Em Rọt Store</p>';

    $mail->send();
    error_log("Email sent successfully for order_code: $order_code");
} catch (Exception $e) {
    error_log("Email sending failed for order_code $order_code: " . $e->getMessage());
    $email_send_error = "Không thể gửi email hóa đơn. Vui lòng kiểm tra lại địa chỉ email hoặc liên hệ hỗ trợ qua hotline: 0123 456 789.";
} finally {
    if (file_exists($temp_pdf_path)) {
        if (!unlink($temp_pdf_path)) {
            error_log("Failed to delete temporary PDF: $temp_pdf_path");
        }
    }
}

// PDF generation function
function generatePDF($order_data)
{
    try {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $items_html = '';
        foreach ($order_data['items'] as $item) {
            $item_price_display = (float) $item['price'];
            $subtotal = $item_price_display * $item['quantity'];
            $spec_display = [];
            if (!empty($item['storage'])) {
                $spec_display[] = htmlspecialchars($item['storage']);
            }
            if (!empty($item['color'])) {
                $spec_display[] = htmlspecialchars($item['color']);
            }
            $specs = implode(' - ', $spec_display);

            $items_html .= '<tr>
                <td style="padding: 8px;">' . htmlspecialchars($item['full_name']) . '</td>
                <td style="padding: 8px;">' . $specs . '</td>
                <td style="padding: 8px; text-align: center;">' . $item['quantity'] . '</td>
                <td style="padding: 8px; text-align: right;">VNĐ' . number_format($item_price_display, 2) . '</td>
                <td style="padding: 8px; text-align: right;">VNĐ ' . number_format($subtotal, 2) . '</td>
            </tr>';
        }

        $final_total_display = (float) $order_data['total'];
        $payment_method_display = htmlspecialchars($order_data['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng' : ($order_data['payment_method'] === 'bank' ? 'Chuyển khoản ngân hàng' : 'Ví MoMo'));

        $mpdf->WriteHTML('
            <h1 style="text-align: center; color: #333;">Hóa Đơn Bán Hàng</h1>
            <p style="text-align: center; font-size: 14px; color: #555;">Mã đơn hàng: <strong>' . htmlspecialchars($order_data['order_code']) . '</strong></p>
            <p style="text-align: center; font-size: 12px; color: #777;">Ngày phát hành: ' . date('d/m/Y, H:i A T') . '</p>
            <hr style="border: 0.5px solid #eee; margin: 20px 0;">
            <h3>Thông tin khách hàng:</h3>
            <p><strong>Họ và tên:</strong> ' . htmlspecialchars($order_data['full_name']) . '</p>
            <p><strong>Email:</strong> ' . htmlspecialchars($order_data['email']) . '</p>
            <p><strong>Số điện thoại:</strong> ' . htmlspecialchars($order_data['phone']) . '</p>
            <p><strong>Địa chỉ:</strong> ' . htmlspecialchars($order_data['address']) . '</p>
            <p><strong>Phương thức thanh toán:</strong> ' . $payment_method_display . '</p>
            <hr style="border: 0.5px solid #eee; margin: 20px 0;">
            <h3>Chi tiết sản phẩm:</h3>
            <table border="1" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; text-align: left;">Sản phẩm</th>
                        <th style="padding: 8px; text-align: left;">Chi tiết</th>
                        <th style="padding: 8px; text-align: center;">Số lượng</th>
                        <th style="padding: 8px; text-align: right;">Giá</th>
                        <th style="padding: 8px; text-align: right;">Tổng</th>
                    </tr>
                </thead>
                <tbody>' . $items_html . '</tbody>
            </table>
            <p style="text-align: right; font-size: 16px; font-weight: bold; color: #333; margin-top: 20px;">Tổng cộng: ' . number_format($final_total_display, 2) . 'VNĐ</p>
            <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #777;">
                <p>Cảm ơn quý khách đã tin tưởng và mua sắm tại Anh Em Rọt Store!</p>
                <p>Mọi thắc mắc xin liên hệ hotline: 0123 456 789</p>
            </div>
        ');
        return $mpdf->Output('', 'S');
    } catch (Exception $e) {
        error_log("PDF generation failed for order_code {$order_data['order_code']}: " . $e->getMessage());
        return false;
    }
}
?>
<script>
    window.addEventListener('DOMContentLoaded', function () {
        if (typeof updateCartCount === 'function') updateCartCount(0);
        if (typeof showToast === 'function') showToast('Đặt hàng thành công!');
    });
</script>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng thành công - Anh Em Rọt Store</title>
    <link rel="icon" href="/Apple_Shop/assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/Apple_Shop/css/header.css">
    <link rel="stylesheet" href="/Apple_Shop/css/product.css">
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #2d3748;
            --accent-color: #48bb78;
            --danger-color: #f56565;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
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
            margin: 4rem auto;
            padding: 2rem;
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            text-align: center;
        }

        .success-icon {
            color: var(--accent-color);
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.8s ease-out;
        }

        .success-title {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            animation: slideInUp 0.8s ease-out;
        }

        .success-message {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            animation: fadeIn 1s ease-out;
        }

        .order-info-summary {
            background-color: #f0f4f8;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
            animation: fadeIn 1.2s ease-out;
        }

        .order-info-summary p {
            margin-bottom: 0.75rem;
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .order-info-summary p strong {
            color: var(--primary-color);
            min-width: 150px;
            display: inline-block;
        }

        .download-btn,
        .back-to-home {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.9rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            margin: 0.5rem;
        }

        .download-btn {
            background-color: var(--accent-color);
            color: white;
            border: 2px solid var(--accent-color);
        }

        .download-btn:hover {
            background-color: #38a169;
            box-shadow: var(--shadow-sm);
        }

        .back-to-home {
            background-color: transparent;
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .back-to-home:hover {
            background-color: var(--border-color);
            box-shadow: var(--shadow-sm);
        }

        .error-message {
            color: var(--danger-color);
            margin-top: 1rem;
            font-weight: bold;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <i class="fas fa-check-circle success-icon"></i>
        <h1 class="success-title">Đặt hàng thành công!</h1>
        <p class="success-message">Cảm ơn bạn đã mua sắm tại Anh Em Rọt Store. Đơn hàng của bạn đã được xác nhận và đang
            được xử lý.</p>
        <?php if (!empty($order_data)): ?>
            <div class="order-info-summary">
                <p><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($order_data['order_code']) ?></p>
                <p><strong>Họ và tên:</strong> <?= htmlspecialchars($order_data['full_name']) ?></p>
                <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order_data['address']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($order_data['email']) ?></p>
                <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order_data['phone']) ?></p>
                <p><strong>Phương thức thanh toán:</strong>
                    <?= htmlspecialchars($order_data['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng' : ($order_data['payment_method'] === 'bank' ? 'Chuyển khoản ngân hàng' : 'Ví MoMo')) ?>
                </p>
                <p><strong>Tổng tiền:</strong><?= number_format((float) $order_data['total'], 2) ?> VND</p>
            </div>
            <a href="/Apple_Shop/sales_invoice.php?order_code=<?= htmlspecialchars($order_data['order_code']) ?>"
                class="download-btn"><i class="fas fa-file-pdf"></i> Xem và tải hóa đơn</a>
            <a href="/Apple_Shop/index.php" class="back-to-home"><i class="fas fa-home"></i> Về trang chủ</a>
            <?php if (isset($email_send_error)): ?>
                <p class="error-message"><?= htmlspecialchars($email_send_error) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p class="error-message">Không tìm thấy thông tin đơn hàng. Vui lòng kiểm tra lại mã đơn hàng hoặc liên hệ hỗ
                trợ.</p>
            <a href="/Apple_Shop/index.php" class="back-to-home"><i class="fas fa-home"></i> Về trang chủ</a>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
    <?php // Unset session cuối cùng, sau khi đã render xong
    unset($_SESSION['order_data']);
    ?>
    <script src="scripts/header.js"></script>
</body>

</html>