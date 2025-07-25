<?php
session_start();
require_once '../config/db.php';
require_once '../vendor/autoload.php'; // mPDF

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Validate order ID
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$order_id) {
    die("Lỗi: ID hóa đơn không hợp lệ.");
}

try {
    // Fetch purchase order details
    $stmt = $conn->prepare("
        SELECT po.*, s.name AS supplier_name, s.phone AS supplier_phone, 
               s.address AS supplier_address, CONCAT(ud.first_name, ' ', ud.last_name) AS user_full_name, 
               u.email AS user_email
        FROM purchase_orders po
        JOIN suppliers s ON po.supplier_id = s.id 
        JOIN users u ON po.user_id = u.id
        JOIN user_detail ud ON u.id = ud.user_id
        WHERE po.id = ?");

    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }

    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchase_order = $result->fetch_assoc();
    $stmt->close();

    if (!$purchase_order) {
        throw new Exception("Không tìm thấy hóa đơn nhập với ID #{$order_id}");
    }

    // Fetch purchase items
    $stmt_items = $conn->prepare("
        SELECT pi.quantity, pi.price, p.product_name, pv.variant_code
        FROM purchase_items pi
        JOIN product_variants pv ON pi.variant_id = pv.id
        JOIN products p ON pv.product_id = p.id
        WHERE pi.purchase_order_id = ?");

    if (!$stmt_items) {
        throw new Exception("Lỗi chuẩn bị truy vấn chi tiết: " . $conn->error);
    }

    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $purchase_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_items->close();

    // Configure mPDF
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 15,
        'margin_bottom' => 15,
        'default_font_size' => 10,
        'default_font' => 'dejavusans'
    ]);

    // Set document metadata
    $mpdf->SetTitle("Hóa đơn nhập #{$order_id} - Anh Em Rọt Store");
    $mpdf->SetAuthor('Anh Em Rọt Store');
    $mpdf->SetCreator('Anh Em Rọt Store System');

    // Load HTML template
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="vi">

    <head>
        <meta charset="UTF-8">
        <title>Hóa đơn nhập #<?php echo htmlspecialchars($order_id); ?></title>
        <style>
            :root {
                --primary-color: #2c3e50;
                /* Darker primary for better contrast */
                --secondary-color: #3498db;
                /* A nice blue for accents */
                --border-color: #dcdcdc;
                /* Lighter border */
                --text-color: #333;
                /* Darker text for readability */
                --background-light: #f9f9f9;
                /* Slightly lighter background for tables */
                --header-bg: #e9e9e9;
                /* Header background for sections */
            }

            body {
                font-family: 'dejavusans', sans-serif;
                font-size: 10pt;
                line-height: 1.6;
                color: var(--text-color);
                margin: 0;
                background-color: #fff;
            }

            .container {
                width: 100%;
                margin: 0 auto;
                padding: 0;
            }

            .header-section {
                text-align: center;
                margin-bottom: 25px;
            }

            .header-section h1 {
                color: var(--primary-color);
                font-size: 24pt;
                margin-bottom: 5px;
                border-bottom: 2px solid var(--secondary-color);
                display: inline-block;
                padding-bottom: 5px;
            }

            .company-info {
                text-align: center;
                margin-bottom: 25px;
                font-size: 10pt;
                color: #555;
            }

            .company-info strong {
                color: var(--primary-color);
            }

            .section {
                margin-bottom: 20px;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                /* Softer corners */
                overflow: hidden;
                /* Ensures content respects border-radius */
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
                /* Subtle shadow */
            }

            .section-title {
                background-color: var(--header-bg);
                color: var(--primary-color);
                padding: 10px 15px;
                font-size: 13pt;
                /* Slightly larger for emphasis */
                font-weight: bold;
                border-bottom: 1px solid var(--border-color);
                margin: 0;
            }

            .section-content {
                padding: 15px;
            }

            .info-grid {
                display: grid;
                grid-template-columns: 120px 1fr;
                /* Fixed width for labels */
                gap: 8px 15px;
                font-size: 10pt;
                line-height: 1.5;
            }

            .info-grid strong {
                color: var(--primary-color);
                font-weight: normal;
                /* Labels are not excessively bold */
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
                /* Remove top/bottom margin, controlled by section padding */
                font-size: 10pt;
                table-layout: fixed;
                /* For consistent column widths if needed */
            }

            th,
            td {
                border: 1px solid var(--border-color);
                padding: 10px;
                /* More padding for better spacing */
                text-align: left;
                word-wrap: break-word;
                /* Prevents long words from breaking layout */
            }

            th {
                background-color: var(--background-light);
                font-weight: bold;
                color: var(--primary-color);
                text-transform: uppercase;
                /* Uppercase headers */
            }

            tbody tr:nth-child(even) {
                background-color: #f6f6f6;
                /* Subtle row banding */
            }

            .text-right {
                text-align: right;
            }

            .total-row td {
                background-color: var(--secondary-color);
                /* Highlight total row */
                color: #fff;
                font-weight: bold;
                font-size: 11pt;
                border: 1px solid var(--secondary-color);
                /* Match border color */
            }

            .signatures {
                margin-top: 40px;
                /* Adjusted margin */
                page-break-inside: avoid;
                display: flex;
                justify-content: space-around;
                /* Spaced equally */
                width: 100%;
            }

            .signature-block {
                width: 45%;
                /* Slightly more width */
                text-align: center;
                padding-top: 15px;
            }

            .signature-block p {
                margin: 5px 0;
            }

            .signature-line {
                border-bottom: 1px dashed #777;
                /* Dashed line for signature */
                width: 70%;
                /* Fixed width for the line */
                margin: 60px auto 10px auto;
                /* Space for signature */
            }

            .signature-name {
                margin-top: 5px;
                font-weight: bold;
                color: var(--primary-color);
            }

            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .section {
                    border: 1px solid var(--border-color);
                    /* Ensure borders print */
                    box-shadow: none;
                    /* No shadow in print */
                }

                .total-row td {
                    background-color: var(--secondary-color) !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                th {
                    background-color: var(--background-light) !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="company-info">
                <h2>Anh Em Rọt Store</h2>
                <span><strong>Địa chỉ:</strong> 123 Đường ABC, Quận XYZ, TP.HCM</span><br>
                <span><strong>Điện thoại:</strong> 0123.456.789</span><br>
                <span><strong>Email:</strong> contact@aerot.com</span>
            </div>
            <div class="header-section">
                <h1>HÓA ĐƠN NHẬP HÀNG</h1>
            </div>
            
            <div class="section">
                <h3 class="section-title">Thông tin hóa đơn</h3>
                <div class="section-content info-grid">
                    <strong>Mã hóa đơn:</strong>
                    <span>#<?php echo htmlspecialchars($purchase_order['id']); ?><br></span>
                    <strong>Ngày tạo:</strong>
                    <span><?php echo date('d/m/Y H:i', strtotime($purchase_order['order_date'])); ?><br></span>
                    <strong>Trạng thái:</strong>
                    <span><?php echo htmlspecialchars($purchase_order['status']); ?><br></span>
                    <strong>Người tạo:</strong>
                    <span><?php echo htmlspecialchars($purchase_order['user_full_name']); ?></span>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">Thông tin nhà cung cấp</h3>
                <div class="section-content info-grid">
                    <strong>Tên nhà cung cấp:</strong>
                    <span><?php echo htmlspecialchars($purchase_order['supplier_name']); ?><br></span>
                    <strong>Số điện thoại:</strong>
                    <span><?php echo htmlspecialchars($purchase_order['supplier_phone'] ?? 'N/A'); ?><br></span>
                    <strong>Địa chỉ:</strong>
                    <span><?php echo htmlspecialchars($purchase_order['supplier_address'] ?? 'N/A'); ?></span>
                </div>
            </div>

            <div class="section">
                <h3 class="section-title">Chi tiết đơn hàng</h3>
                <div class="section-content" style="padding: 0;">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">STT</th>
                                <th style="width: 40%;">Tên sản phẩm</th>
                                <th style="width: 15%;" class="text-right">Số lượng</th>
                                <th style="width: 20%;" class="text-right">Đơn giá</th>
                                <th style="width: 20%;" class="text-right">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($purchase_items as $index => $item):
                                $subtotal = $item['quantity'] * $item['price'];
                                $total += $subtotal;
                                ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($item['product_name'] . ' (' . $item['variant_code'] . ')'); ?>
                                    </td>
                                    <td class="text-right"><?php echo number_format($item['quantity'], 0, ',', '.'); ?></td>
                                    <td class="text-right"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                                    <td class="text-right"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4" class="text-right"><strong>Tổng cộng:</strong></td>
                                <td class="text-right"><strong><?php echo number_format($total, 0, ',', '.'); ?>đ</strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="signatures">
                <div class="signature-block">
                    <p>Người lập phiếu</p>
                    <p>(Ký và ghi rõ họ tên)</p>
                    <div class="signature-line"></div>
                    <p class="signature-name"><?php echo htmlspecialchars($purchase_order['user_full_name']); ?></p>
                </div>
                <div class="signature-block">
                    <p>Người giao hàng</p>
                    <p>(Ký và ghi rõ họ tên)</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">.................................</p>
                </div>
            </div>
        </div>
    </body>

    </html>
    <?php
    $html = ob_get_clean();

    // Write HTML and output PDF
    $mpdf->WriteHTML($html);
    $mpdf->Output("HoaDonNhap_{$order_id}.pdf", \Mpdf\Output\Destination::INLINE);

} catch (Exception $e) {
    error_log("PDF Generation Error: " . $e->getMessage());
    http_response_code(500);
    die("Có lỗi xảy ra khi tạo hóa đơn PDF: " . htmlspecialchars($e->getMessage()));
}
?>