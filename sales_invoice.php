<?php
session_start();
require_once 'config/db.php';

// Lấy order_code từ GET hoặc session
$order_code = isset($_GET['order_code']) ? trim($_GET['order_code']) : '';
$order_data = isset($_SESSION['order_data']) ? $_SESSION['order_data'] : null;

// Nếu không có session hoặc order_code không khớp, truy vấn lại DB
if (!$order_data || ($order_code && $order_data['order_code'] !== $order_code)) {
    if ($order_code && isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("
            SELECT
                o.order_code, o.full_name, o.email, o.phone_number AS phone, 
                o.shipping_address AS address, o.total_amount AS total, o.payment_method, o.notes
            FROM orders o
            WHERE o.order_code = ? AND o.user_id = ?
        ");
        if ($stmt) {
            $stmt->bind_param("si", $order_code, $_SESSION['user_id']);
            if ($stmt->execute()) {
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
                        'notes' => $db_order_data['notes'],
                        'total' => $db_order_data['total'],
                        'items' => []
                    ];
                    // Lấy danh sách sản phẩm đã đặt hàng
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
                        }
                        $stmt_items->close();
                    }
                }
            }
        }
    }
}

// Nếu vẫn không có order_data, chuyển về trang chủ
if (!$order_data) {
    header('Location: index.php');
    exit;
}

// Kiểm tra cấu trúc order_data
$required_fields = ['order_code', 'full_name', 'email', 'phone', 'address', 'ward', 'district', 'city', 'payment_method', 'total', 'items'];
foreach ($required_fields as $field) {
    if (!isset($order_data[$field])) {
        error_log("Missing field $field in order_data");
        header('Location: index.php');
        exit;
    }
}

function removeDiacritics($text) {
    if (empty($text)) return $text;
    $unicode = [
        'a' => '/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/',
        'e' => '/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/',
        'i' => '/ì|í|ị|ỉ|ĩ/',
        'o' => '/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/',
        'u' => '/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/',
        'y' => '/ỳ|ý|ỵ|ỷ|ỹ/',
        'd' => '/đ/',
        'A' => '/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/',
        'E' => '/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/',
        'I' => '/Ì|Í|Ị|Ỉ|Ĩ/',
        'O' => '/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/',
        'U' => '/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/',
        'Y' => '/Ỳ|Ý|Ỵ|Ỷ|Ỹ/',
        'D' => '/Đ/'
    ];
    foreach ($unicode as $nonUnicode => $pattern) {
        $text = preg_replace($pattern, $nonUnicode, $text);
    }
    return $text;
}

// Chuẩn bị dữ liệu cho PDF (xóa dấu nếu cần)
$order_data_for_pdf = $order_data;
$order_data_for_pdf['full_name'] = removeDiacritics($order_data['full_name']);
$order_data_for_pdf['address'] = removeDiacritics($order_data['address']);
$order_data_for_pdf['ward'] = removeDiacritics($order_data['ward']);
$order_data_for_pdf['district'] = removeDiacritics($order_data['district']);
$order_data_for_pdf['city'] = removeDiacritics($order_data['city']);
$order_data_for_pdf['notes'] = $order_data['notes'] ? removeDiacritics($order_data['notes']) : '';
foreach ($order_data_for_pdf['items'] as &$item) {
    $item['name'] = removeDiacritics($item['name']);
    $item['storage'] = removeDiacritics($item['storage']);
    $item['color'] = removeDiacritics($item['color']);
    $item['price'] = (float)$item['price']; // Đảm bảo price là số
}
unset($item);

// Tính lại tổng tiền từ danh sách sản phẩm (nếu có)
$overall_subtotal = 0;
foreach ($order_data['items'] as $item) {
    $item_price = isset($item['price']) ? (float)$item['price'] : 0;
    $subtotal = $item_price * (isset($item['quantity']) ? (int)$item['quantity'] : 1);
    $overall_subtotal += $subtotal;
}
$order_data['total'] = number_format($overall_subtotal, 2);
$order_data_for_pdf['total'] = number_format($overall_subtotal, 2);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn bán hàng - Anh Em Rọt Store</title>
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
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem 1rem;
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px dashed var(--border-color);
        }

        .invoice-logo {
            max-width: 120px;
            margin-bottom: 1rem;
        }

        .invoice-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .invoice-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .invoice-date {
            font-size: 1rem;
            color: var(--text-secondary);
        }

        .invoice-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--accent-color);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            background-color: var(--background-color);
            padding: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }

        .detail-item strong {
            color: var(--text-primary);
            display: block;
            margin-bottom: 0.25rem;
        }

        .detail-item span {
            color: var(--text-secondary);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            box-shadow: var(--shadow-sm);
            border-radius: var(--border-radius);
            overflow: hidden;
        }

        .items-table th,
        .items-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .items-table th {
            background: var(--secondary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .items-table td {
            background: var(--card-background);
            color: var(--text-primary);
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .table-total {
            text-align: right;
            padding-top: 1rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .summary-block {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px dashed var(--border-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 300px;
            padding: 0.5rem 0;
        }

        .summary-label {
            color: var(--text-secondary);
        }

        .summary-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .summary-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--success-color);
            margin-top: 1rem;
            padding-top: 0.5rem;
            border-top: 2px solid var(--success-color);
        }

        .invoice-footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px dashed var(--border-color);
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .actions {
            text-align: center;
            margin-top: 2rem;
        }

        .download-btn,
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            margin: 0 0.5rem;
        }

        .download-btn {
            background: linear-gradient(135deg, var(--accent-color), var(--success-color));
            color: white;
            border: none;
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .back-btn {
            background: none;
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
        }

        .back-btn:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin: 1rem auto;
            }

            .invoice-title {
                font-size: 2rem;
            }

            .items-table th,
            .items-table td {
                padding: 0.75rem;
                font-size: 0.85rem;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .summary-block {
                align-items: center;
            }

            .summary-row {
                max-width: 100%;
            }

            .download-btn,
            .back-btn {
                width: calc(100% - 1rem);
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="invoice-header">
            <img src="assets/logo/logo.png" alt="Anh Em Rọt Store Logo" class="invoice-logo" onerror="this.src='assets/products/default.png'">
            <h1 class="invoice-title">Hóa đơn bán hàng</h1>
            <p class="invoice-subtitle">Anh Em Rọt Store</p>
            <p class="invoice-date">Ngày xuất hóa đơn: <?= date('d/m/Y, H:i A T') ?></p>
        </div>

        <div class="invoice-section">
            <h2 class="section-title">Thông tin khách hàng</h2>
            <div class="detail-grid">
                <div class="detail-item">
                    <strong>Mã đơn hàng:</strong>
                    <span><?= htmlspecialchars($order_data['order_code']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Họ và tên:</strong>
                    <span><?= htmlspecialchars($order_data['full_name']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Địa chỉ:</strong>
                    <span><?= htmlspecialchars($order_data['address'] . ', ' . $order_data['ward'] . ', ' . $order_data['district'] . ', ' . $order_data['city']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Email:</strong>
                    <span><?= htmlspecialchars($order_data['email']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Số điện thoại:</strong>
                    <span><?= htmlspecialchars($order_data['phone']) ?></span>
                </div>
                <div class="detail-item">
                    <strong>Phương thức thanh toán:</strong>
                    <span><?= htmlspecialchars($order_data['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : ($order_data['payment_method'] === 'bank' ? 'Chuyển khoản ngân hàng' : 'Ví MoMo')) ?></span>
                </div>
                <?php if ($order_data['notes']): ?>
                    <div class="detail-item">
                        <strong>Ghi chú:</strong>
                        <span><?= htmlspecialchars($order_data['notes']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="invoice-section">
            <h2 class="section-title">Chi tiết sản phẩm</h2>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Dung lượng/Màu sắc</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng cộng</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $overall_subtotal = 0;
                    foreach ($order_data['items'] as $item):
                        $item_price = in_array($item['product_id'], [1, 2]) ? (float)$item['price'] * 0.9 : (float)$item['price'];
                        $subtotal = $item_price * $item['quantity'];
                        $overall_subtotal += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= htmlspecialchars($item['storage'] . ' - ' . $item['color']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>$<?= number_format($item_price, 2) ?></td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary-block">
                <div class="summary-row">
                    <span class="summary-label">Subtotal:</span>
                    <span class="summary-value">VND<?= number_format($overall_subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Shipping Fee:</span>
                    <span class="summary-value">0.00 VND</span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span>$<?= htmlspecialchars($order_data['total']) ?></span>
                </div>
            </div>
        </div>

        <div class="actions">
            <button class="download-btn" onclick="downloadInvoice()"><i class="fas fa-file-pdf"></i> Tải và in PDF</button>
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>

        <div class="invoice-footer">
            <p>Thank you for shopping at Anh Em Rọt Store! We are happy to serve you.</p>
            <p>Please keep this invoice for reference and warranty.</p>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script>
        const { jsPDF } = window.jspdf;
        const orderDataForPdf = <?php echo json_encode($order_data_for_pdf, JSON_NUMERIC_CHECK); ?>;

        function downloadInvoice() {
            const doc = new jsPDF();

            // Header Section
            doc.setFontSize(22);
            doc.setTextColor(45, 55, 72);
            doc.text("SALES INVOICE", doc.internal.pageSize.getWidth() / 2, 25, { align: 'center' });

            doc.setFontSize(10);
            doc.setTextColor(113, 128, 147);
            doc.text("Anh Em Rot Store", doc.internal.pageSize.getWidth() / 2, 32, { align: 'center' });
            doc.text("Address: 12 Chua Boc, Ha Noi", doc.internal.pageSize.getWidth() / 2, 38, { align: 'center' });
            doc.text("Phone: 0123 456 789 | Email: info@anhemrot.com", doc.internal.pageSize.getWidth() / 2, 44, { align: 'center' });

            // Kiểm tra logo
            try {
                doc.addImage('assets/logo/logo.png', 'PNG', 15, 15, 30, 30);
            } catch (e) {
                console.warn('Không thể tải logo:', e.message);
            }

            doc.setDrawColor(226, 232, 240);
            doc.line(15, 50, doc.internal.pageSize.getWidth() - 15, 50);

            // Customer Information Section
            doc.setFontSize(14);
            doc.setTextColor(45, 55, 72);
            doc.text("CUSTOMER INFORMATION", 15, 65);
            doc.setFontSize(10);
            doc.setTextColor(74, 85, 104);

            let currentY = 75;
            doc.text(`Order Code: ${orderDataForPdf.order_code}`, 15, currentY);
            currentY += 7;
            doc.text(`Date: ${new Date().toLocaleString('vi-VN', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: true,
                timeZone: 'Asia/Ho_Chi_Minh'
            })}`, 15, currentY);
            currentY += 7;
            doc.text(`Full Name: ${orderDataForPdf.full_name}`, 15, currentY);
            currentY += 7;
            doc.text(`Phone: ${orderDataForPdf.phone}`, 15, currentY);
            currentY += 7;
            doc.text(`Email: ${orderDataForPdf.email}`, 15, currentY);
            currentY += 7;
            doc.text(`Address: ${orderDataForPdf.address}, ${orderDataForPdf.ward}, ${orderDataForPdf.district}, ${orderDataForPdf.city}`, 15, currentY);
            currentY += 7;
            const paymentMethodText = orderDataForPdf.payment_method === 'cod' ? 'Thanh toan khi nhan hang (COD)' :
                                     orderDataForPdf.payment_method === 'bank' ? 'Chuyen khoan ngan hang' : 'Vi MoMo';
            doc.text(`Payment Method: ${paymentMethodText}`, 15, currentY);
            if (orderDataForPdf.notes) {
                currentY += 7;
                doc.text(`Notes: ${orderDataForPdf.notes}`, 15, currentY);
            }

            currentY += 15;

            // Item Details Table
            const tableHeaders = [['Product', 'Storage/Color', 'Quantity', 'Price (VND)', 'Total (VND)']];
            const tableData = orderDataForPdf.items.map((item) => {
                const itemPrice = in_array(item.product_id, [1, 2]) ? Number(item.price) * 0.9 : Number(item.price);
                const subtotal = itemPrice * item.quantity;
                return [
                    item.name,
                    `${item.storage} - ${item.color}`,
                    item.quantity,
                    `$${itemPrice.toFixed(2)}`,
                    `$${subtotal.toFixed(2)}`
                ];
            });

            doc.autoTable({
                startY: currentY,
                head: tableHeaders,
                body: tableData,
                theme: 'grid',
                styles: {
                    fontSize: 9,
                    cellPadding: 3,
                    halign: 'center',
                    valign: 'middle'
                },
                headStyles: {
                    fillColor: [45, 55, 72],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    halign: 'center'
                },
                columnStyles: {
                    0: { halign: 'left' },
                    1: { halign: 'center' },
                    2: { halign: 'center' },
                    3: { halign: 'right' },
                    4: { halign: 'right' }
                },
                margin: { horizontal: 15 }
            });

            currentY = doc.lastAutoTable.finalY + 10;

            // Summary Section
            doc.setFontSize(12);
            doc.setTextColor(45, 55, 72);
            doc.text(`Subtotal:`, 150, currentY, { align: 'right' });
            const subtotal = orderDataForPdf.items.reduce((sum, item) => sum + (in_array(item.product_id, [1, 2]) ? Number(item.price) * 0.9 : Number(item.price)) * item.quantity, 0);
            doc.text(`$${subtotal.toFixed(2)}`, doc.internal.pageSize.getWidth() - 15, currentY, { align: 'right' });
            currentY += 7;
            doc.text(`Shipping Fee:`, 150, currentY, { align: 'right' });
            doc.text(`$0.00`, doc.internal.pageSize.getWidth() - 15, currentY, { align: 'right' });
            currentY += 10;
            doc.setFontSize(16);
            doc.setTextColor(72, 187, 120);
            doc.text(`TOTAL:`, 150, currentY, { align: 'right' });
            doc.text(`$${orderDataForPdf.total}`, doc.internal.pageSize.getWidth() - 15, currentY, { align: 'right' });

            // Footer
            currentY += 20;
            doc.setFontSize(10);
            doc.setTextColor(113, 128, 147);
            doc.text("Thank you for shopping at Anh Em Rot Store!", doc.internal.pageSize.getWidth() / 2, currentY, { align: 'center' });
            currentY += 6;
            doc.text("Please keep this invoice for reference and warranty.", doc.internal.pageSize.getWidth() / 2, currentY, { align: 'center' });
            currentY += 10;
            doc.text(`Order Code: ${orderDataForPdf.order_code}`, doc.internal.pageSize.getWidth() / 2, currentY, { align: 'center' });

            doc.save(`hoadonban-${orderDataForPdf.order_code}.pdf`);
        }

        function in_array(needle, haystack) {
            return haystack.includes(Number(needle));
        }
    </script>
</body>
</html>