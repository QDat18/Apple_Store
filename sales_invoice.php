<?php
session_start();
require_once 'config/db.php';

function removeDiacritics($text) {
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

$order_data = isset($_SESSION['order_data']) ? $_SESSION['order_data'] : null;

if (!$order_data) {
    header('Location: index.php');
    exit;
}

// **IMPORTANT:** Apply diacritic removal to all relevant fields *before* passing to JS
// The web page HTML still shows the original data, but the JS will use this processed data.
$order_data_for_pdf = $order_data;
$order_data_for_pdf['full_name'] = removeDiacritics($order_data_for_pdf['full_name']);
$order_data_for_pdf['address'] = removeDiacritics($order_data_for_pdf['address']);
$order_data_for_pdf['ward'] = removeDiacritics($order_data_for_pdf['ward']);
$order_data_for_pdf['district'] = removeDiacritics($order_data_for_pdf['district']);
$order_data_for_pdf['city'] = removeDiacritics($order_data_for_pdf['city']);
$order_data_for_pdf['notes'] = $order_data_for_pdf['notes'] ? removeDiacritics($order_data_for_pdf['notes']) : '';

// Process items for PDF as well
foreach ($order_data_for_pdf['items'] as &$item) {
    $item['name'] = removeDiacritics($item['name']);
    $item['storage'] = removeDiacritics($item['storage']);
    $item['color'] = removeDiacritics($item['color']);
}
unset($item); // Break the reference with the last element

// Format total to 2 decimal places for consistent display
// This applies to the original $order_data for HTML display
$order_data['total'] = number_format($order_data['total'], 2);
// And also to the PDF data
$order_data_for_pdf['total'] = number_format($order_data_for_pdf['total'], 2);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Invoice - Anh Em Rot Store</title>
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
            overflow: hidden; /* Ensures rounded corners apply to content */
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
                width: calc(100% - 1rem); /* Account for margin */
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="invoice-header">
            <img src="assets/logo/logo.png" alt="Anh Em Rot Store Logo" class="invoice-logo">
            <h1 class="invoice-title">Hóa đơn bán hàng</h1>
            <p class="invoice-subtitle">Anh Em Rot Store</p>
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
                        $item_price = in_array($item['product_id'], [1, 2]) ? $item['price'] * 0.9 : $item['price'];
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
                    <span class="summary-label">Tổng phụ:</span>
                    <span class="summary-value">$<?= number_format($overall_subtotal, 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Phí vận chuyển:</span>
                    <span class="summary-value">$0.00</span> </div>
                <div class="summary-total">
                    <span>Tổng tiền:</span>
                    <span>$<?= htmlspecialchars($order_data['total']) ?></span>
                </div>
            </div>
        </div>

        <div class="actions">
            <button class="download-btn" onclick="downloadInvoice()"><i class="fas fa-file-pdf"></i> Tải hóa đơn PDF</button>
            <a href="index.php" class="back-btn"><i class="fas fa-arrow-left"></i> Quay lại trang chủ</a>
        </div>

        <div class="invoice-footer">
            <p>Cảm ơn quý khách đã mua hàng tại Anh Em Rot Store! Chúng tôi rất hân hạnh được phục vụ.</p>
            <p>Vui lòng giữ hóa đơn này để tham khảo và bảo hành.</p>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script>
        const { jsPDF } = window.jspdf;
        // Use the diacritic-removed data for PDF generation
        const orderDataForPdf = <?php echo json_encode($order_data_for_pdf); ?>;

        function downloadInvoice() {
            const doc = new jsPDF();

            // Header Section
            doc.setFontSize(22);
            doc.setTextColor(45, 55, 72); // --secondary-color in RGB
            doc.text("SALES INVOICE", doc.internal.pageSize.getWidth() / 2, 25, { align: 'center' });

            doc.setFontSize(10);
            doc.setTextColor(113, 128, 147); // --text-secondary in RGB
            doc.text("Anh Em Rot Store", doc.internal.pageSize.getWidth() / 2, 32, { align: 'center' });
            doc.text("Address: 123 ABC Street, XYZ District, Ha Noi City", doc.internal.pageSize.getWidth() / 2, 38, { align: 'center' });
            doc.text("Phone: 0123 456 789 | Email: info@anhemrot.com", doc.internal.pageSize.getWidth() / 2, 44, { align: 'center' });

            doc.addImage('assets/logo/logo.png', 'PNG', 15, 15, 30, 30); // Logo adjusted for better placement

            doc.setDrawColor(226, 232, 240); // --border-color
            doc.line(15, 50, doc.internal.pageSize.getWidth() - 15, 50); // Line separator

            // Customer Information Section
            doc.setFontSize(14);
            doc.setTextColor(45, 55, 72);
            doc.text("CUSTOMER INFORMATION", 15, 65);
            doc.setFontSize(10);
            doc.setTextColor(74, 85, 104); // Slightly darker for details

            let currentY = 75;
            doc.text(`Order Code: ${orderDataForPdf.order_code}`, 15, currentY);
            currentY += 7;
            doc.text(`Issue Date: ${new Date().toLocaleString('en-US', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit', hour12: true,
                timeZone: 'Asia/Ho_Chi_Minh'
            }).replace(',', '')}`, 15, currentY); // English date format
            currentY += 7;
            doc.text(`Full Name: ${orderDataForPdf.full_name}`, 15, currentY);
            currentY += 7;
            doc.text(`Phone Number: ${orderDataForPdf.phone}`, 15, currentY);
            currentY += 7;
            doc.text(`Email: ${orderDataForPdf.email}`, 15, currentY);
            currentY += 7;
            doc.text(`Address: ${orderDataForPdf.address}, ${orderDataForPdf.ward}, ${orderDataForPdf.district}, ${orderDataForPdf.city}`, 15, currentY);
            currentY += 7;
            let paymentMethodText;
            if (orderDataForPdf.payment_method === 'cod') {
                paymentMethodText = 'Cash on Delivery (COD)';
            } else if (orderDataForPdf.payment_method === 'bank') {
                paymentMethodText = 'Bank Transfer';
            } else {
                paymentMethodText = 'MoMo Wallet';
            }
            doc.text(`Payment Method: ${paymentMethodText}`, 15, currentY);
            if (orderDataForPdf.notes) {
                currentY += 7;
                doc.text(`Notes: ${orderDataForPdf.notes}`, 15, currentY);
            }

            currentY += 15; // Space before items table

            // Item Details Table
            const tableHeaders = [['Product', 'Storage/Color', 'Quantity', 'Price (USD)', 'Total (USD)']];
            const tableData = orderDataForPdf.items.map((item) => {
                const itemPrice = item.product_id === 1 || item.product_id === 2 ? item.price * 0.9 : item.price;
                const subtotal = itemPrice * item.quantity;
                return [
                    `${item.name}`,
                    `${item.storage} - ${item.color}`,
                    item.quantity,
                    `$${Number(itemPrice).toFixed(2)}`,
                    `$${Number(subtotal).toFixed(2)}`
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
                margin: { horizontal: 15 },
                didDrawPage: function(data) {
                    // Footer on each page if needed
                }
            });

            currentY = doc.lastAutoTable.finalY + 10; // Update Y position after table

            // Summary (Total) Section
            doc.setFontSize(12);
            doc.setTextColor(45, 55, 72);
            doc.text(`Subtotal:`, 150, currentY, { align: 'right' });
            doc.text(`$${Number(orderDataForPdf.items.reduce((sum, item) => sum + (in_array(item.product_id, [1, 2]) ? item.price * 0.9 : item.price) * item.quantity, 0)).toFixed(2)}`, doc.internal.pageSize.getWidth() - 15, currentY, { align: 'right' });
            currentY += 7;
            doc.text(`Shipping Fee:`, 150, currentY, { align: 'right' });
            doc.text(`$0.00`, doc.internal.pageSize.getWidth() - 15, currentY, { align: 'right' });
            currentY += 10;
            doc.setFontSize(16);
            doc.setTextColor(72, 187, 120); // --success-color
            doc.text(`TOTAL:`, 150, currentY, { align: 'right' });
            doc.text(`$${orderDataForPdf.total}`, doc.internal.pageSize.getWidth() - 15, currentY, { align: 'right' });

            // Footer
            currentY += 20;
            doc.setFontSize(10);
            doc.setTextColor(113, 128, 147);
            doc.text("Thank you for your purchase from Anh Em Rot Store!", doc.internal.pageSize.getWidth() / 2, currentY, { align: 'center' });
            currentY += 6;
            doc.text("Please keep this invoice for your reference and warranty.", doc.internal.pageSize.getWidth() / 2, currentY, { align: 'center' });
            currentY += 10;
            doc.text(`Order Code: ${orderDataForPdf.order_code}`, doc.internal.pageSize.getWidth() / 2, currentY, { align: 'center' });


            doc.save(`sales-invoice-${orderDataForPdf.order_code}.pdf`);
        }

        // Helper function for in_array in JS
        function in_array(needle, haystack) {
            return haystack.indexOf(needle) !== -1;
        }
    </script>
</body>
</html>