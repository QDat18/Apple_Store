<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header("Location: manage_orders.php");
    exit;
}

$order_id = intval($_GET['id']);
$order = null;
$order_items = [];

// Truy vấn thông tin đơn hàng
$stmt = $conn->prepare("SELECT o.*, u.email, ud.first_name, ud.last_name 
                       FROM orders o
                       JOIN users u ON o.user_id = u.id
                       JOIN user_detail ud ON u.id = ud.user_id  
                       WHERE o.id = ?");

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
}
$stmt->close();

if ($order) {
    // Truy vấn chi tiết đơn hàng với thông tin sản phẩm và biến thể
    $stmt = $conn->prepare("SELECT oi.*, p.product_name, pv.variant_code, 
                           GROUP_CONCAT(DISTINCT vav_storage.value) as storage,
                           GROUP_CONCAT(DISTINCT vav_color.value) as color
                           FROM order_items oi
                           JOIN product_variants pv ON oi.variant_id = pv.id 
                           JOIN products p ON pv.product_id = p.id
                           LEFT JOIN product_variant_attribute_links pval_storage ON pval_storage.variant_id = pv.id
                           LEFT JOIN variant_attribute_values vav_storage ON vav_storage.id = pval_storage.attribute_value_id 
                               AND vav_storage.attribute_id = 2
                           LEFT JOIN product_variant_attribute_links pval_color ON pval_color.variant_id = pv.id
                           LEFT JOIN variant_attribute_values vav_color ON vav_color.id = pval_color.attribute_value_id 
                               AND vav_color.attribute_id = 1
                           WHERE oi.order_id = ?
                           GROUP BY oi.id");

    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng #<?= htmlspecialchars($order_id) ?></title>
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .order-details {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin: 2rem 0;
        }

        .order-details h1 {
            color: #1d1d1f;
            font-size: 1.8rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f5f5f7;
        }

        .order-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
            background: #f5f5f7;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .order-info p {
            margin: 0.5rem 0;
            color: #1d1d1f;
            line-height: 1.5;
        }

        .order-info strong {
            color: #0066cc;
            display: inline-block;
            min-width: 120px;
        }

        .order-items {
            margin-top: 2rem;
        }

        .order-items h2 {
            color: #1d1d1f;
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .order-items th {
            background: #f5f5f7;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #1d1d1f;
            border-bottom: 2px solid #d2d2d7;
        }

        .order-items td {
            padding: 1rem;
            border-bottom: 1px solid #d2d2d7;
            color: #424245;
        }

        .order-items tbody tr:hover {
            background: rgba(0, 102, 204, 0.05);
        }

        .order-items tfoot td {
            padding: 1.5rem 1rem;
            background: #f5f5f7;
            font-weight: 600;
        }

        .order-items .text-right {
            text-align: right;
        }

        .order-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: #0066cc;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background: #0055aa;
        }

        .inline-form {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .inline-form select {
            padding: 0.75rem;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            font-size: 1rem;
            color: #1d1d1f;
            background: #fff;
            cursor: pointer;
        }

        .inline-form select:focus {
            outline: none;
            border-color: #0066cc;
            box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
        }

        .error-message {
            text-align: center;
            padding: 3rem;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .error-message p {
            color: #ff3b30;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
        }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .status-Pending {
            background: #ffe8cc;
            color: #ff9500;
        }

        .status-Processing {
            background: #e4f3ff;
            color: #0066cc;
        }

        .status-Shipped {
            background: #e4fff1;
            color: #34c759;
        }

        .status-Delivered {
            background: #dcfce7;
            color: #22c55e;
        }

        .status-Cancelled {
            background: #ffe4e4;
            color: #ff3b30;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .order-details {
                padding: 1rem;
                margin: 1rem 0;
            }

            .order-info {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .order-items {
                overflow-x: auto;
            }

            .order-actions {
                flex-direction: column;
            }

            .inline-form {
                flex-direction: column;
                width: 100%;
            }

            .inline-form select,
            .inline-form button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php include 'admin_header.php'; ?>

    <main class="admin-container">
        <?php if ($order): ?>
            <div class="order-details">
                <h1>Chi Tiết Đơn Hàng #<?= htmlspecialchars($order['order_code']) ?></h1>

                <div class="order-info">
                    <p><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($order['order_code']) ?></p>
                    <p><strong>Khách hàng:</strong>
                        <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone_number']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></p>
                    <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
                    <p><strong>Trạng thái:</strong> <?= htmlspecialchars($order['status']) ?></p>
                </div>

                <div class="order-items">
                    <h2>Sản phẩm trong đơn</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Biến thể</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td>
                                        <?php
                                        $variant_info = [];
                                        if (!empty($item['storage']))
                                            $variant_info[] = $item['storage'];
                                        if (!empty($item['color']))
                                            $variant_info[] = $item['color'];
                                        echo htmlspecialchars(implode(' - ', $variant_info));
                                        ?>
                                    </td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td><?= number_format($item['price'], 0, ',', '.') ?>₫</td>
                                    <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>₫</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-right"><strong>Tổng tiền:</strong></td>
                                <td><strong><?= number_format($order['total_amount'], 0, ',', '.') ?>₫</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="order-actions">
                    <a href="manage_orders.php" class="btn">Quay lại</a>
                    <?php if ($order['status'] !== 'Delivered'): ?>
                        <form method="POST" action="update_order_status.php" class="inline-form">
                            <input type="hidden" name="order_id" value="<?= $order_id ?>">
                            <select name="status">
                                <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                <option value="Processing" <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Đang xử lý
                                </option>
                                <option value="Shipped" <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Đang giao</option>
                                <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Đã giao</option>
                                <option value="Cancelled" <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Cập nhật trạng thái</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="error-message">
                <p>Không tìm thấy đơn hàng</p>
                <a href="manage_orders.php" class="btn">Quay lại</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'admin_footer.php'; ?>
</body>

</html>