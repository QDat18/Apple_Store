<?php
session_start();
require_once '../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$purchase_order = null;
$purchase_items = [];
$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($order_id > 0) {
    $stmt = $conn->prepare("
        SELECT po.id, po.order_date, po.total_amount, po.status, 
               s.name AS supplier_name, s.phone AS supplier_phone, s.address AS supplier_address,
               CONCAT(ud.first_name, ' ', ud.last_name) AS user_full_name, u.email AS user_email
        FROM purchase_orders po
        JOIN suppliers s ON po.supplier_id = s.id
        JOIN users u ON po.user_id = u.id
        JOIN user_detail ud ON u.id = ud.user_id
        WHERE po.id = ?");
    
    if ($stmt === false) {
        die("Lỗi chuẩn bị truy vấn: " . $conn->error);
    }
    
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchase_order = $result->fetch_assoc();
    $stmt->close();

    if ($purchase_order) {
        $stmt_items = $conn->prepare("
            SELECT pi.quantity, pi.price, p.product_name, pv.variant_code
            FROM purchase_items pi
            JOIN product_variants pv ON pi.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            WHERE pi.purchase_order_id = ?");
        
        if ($stmt_items === false) {
            die("Lỗi chuẩn bị truy vấn chi tiết: " . $conn->error);
        }
        
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $purchase_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_items->close();
    }
}

require 'admin_header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Hóa đơn nhập #<?php echo htmlspecialchars($order_id); ?> | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-color-dark: #1e40af;
            --success-color: #16a34a;
            --danger-color: #dc2626;
            --warning-color: #f59e0b;
            --info-color: #0891b2;
            --gray-color: #6b7280;
            --text-color: #1f2937;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .detail-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
        }

        .detail-card h2 {
            font-size: 1.875rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 20px;
        }

        .detail-section {
            margin-bottom: 20px;
        }

        .detail-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .detail-section p {
            margin: 8px 0;
            font-size: 0.875rem;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            color: #fff;
        }

        .status-Pending {
            background: var(--primary-color);
        }

        .status-Processing {
            background: var(--info-color);
        }

        .status-Received {
            background: var(--success-color);
        }

        .status-Cancelled {
            background: var(--danger-color);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .items-table th,
        .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table th {
            background: #f9fafb;
            font-weight: 600;
        }

        .items-table .summary-row td {
            font-weight: 600;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-back {
            background: var(--gray-color);
            color: #fff;
        }

        .btn-back:hover {
            background: #4b5563;
        }

        .btn-print {
            background: var(--primary-color);
            color: #fff;
        }

        .btn-print:hover {
            background: var(--primary-color-dark);
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 10px;
            }

            .detail-card {
                padding: 15px;
            }

            .items-table {
                font-size: 0.75rem;
                display: block;
                overflow-x: auto;
            }

            .items-table th,
            .items-table td {
                padding: 8px;
            }

            .button-group {
                flex-direction: column;
                align-items: stretch;
            }

            .btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <main class="admin-container">
        <?php if ($purchase_order): ?>
            <div class="detail-card">
                <h2>Chi tiết Hóa đơn nhập #<?php echo htmlspecialchars($purchase_order['id']); ?></h2>

                <div class="detail-section">
                    <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($purchase_order['order_date'])); ?></p>
                    <p><strong>Người tạo:</strong> <?php echo htmlspecialchars($purchase_order['user_full_name']); ?>
                        (<?php echo htmlspecialchars($purchase_order['user_email']); ?>)</p>
                    <p><strong>Tổng tiền:</strong> <?php echo number_format($purchase_order['total_amount'], 0, ',', '.'); ?> VNĐ</p>
                    <p><strong>Trạng thái:</strong>
                        <span class="status-badge status-<?php echo htmlspecialchars($purchase_order['status']); ?>">
                            <?php
                            $status_map = [
                                'Pending' => 'Chờ xử lý',
                                'Processing' => 'Đang xử lý',
                                'Received' => 'Đã nhận',
                                'Cancelled' => 'Đã hủy'
                            ];
                            echo htmlspecialchars($status_map[$purchase_order['status']] ?? $purchase_order['status']);
                            ?>
                        </span>
                    </p>
                </div>

                <div class="detail-section">
                    <h3>Thông tin Nhà cung cấp</h3>
                    <p><strong>Tên nhà cung cấp:</strong> <?php echo htmlspecialchars($purchase_order['supplier_name']); ?></p>
                    <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($purchase_order['supplier_phone'] ?? 'N/A'); ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($purchase_order['supplier_address'] ?? 'N/A'); ?></p>
                </div>

                <div class="detail-section">
                    <h3>Sản phẩm trong Hóa đơn</h3>
                    <?php if (!empty($purchase_items)): ?>
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subtotal = 0;
                                foreach ($purchase_items as $item):
                                    $item_total = $item['quantity'] * $item['price'];
                                    $subtotal += $item_total;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name'] . ' (' . $item['variant_code'] . ')'); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VNĐ</td>
                                        <td><?php echo number_format($item_total, 0, ',', '.'); ?> VNĐ</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="summary-row">
                                    <td colspan="3" style="text-align: right;">Tổng cộng:</td>
                                    <td><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Không có sản phẩm nào trong hóa đơn này.</p>
                    <?php endif; ?>
                </div>

                <div class="button-group">
                    <a href="manage_purchase_orders.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Quay lại</a>
                    <a href="print_purchase_invoice.php?id=<?php echo htmlspecialchars($purchase_order['id']); ?>" target="_blank" class="btn btn-print"><i class="fas fa-print"></i> In hóa đơn</a>
                </div>
            </div>
        <?php else: ?>
            <div class="detail-card">
                <p>Không tìm thấy hóa đơn nhập với ID đã cho.</p>
                <div class="button-group">
                    <a href="manage_purchase_orders.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Quay lại danh sách</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
                button.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                });
            });

            const currentPath = window.location.pathname.split('/').pop();
            document.querySelectorAll('.sidebar nav a').forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref === currentPath || (currentPath === 'view_purchase_order.php' && linkHref === 'manage_purchase_orders.php')) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>