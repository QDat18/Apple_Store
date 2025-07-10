<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manage_orders.php");
    exit;
}

$order_id = intval($_GET['id']);
$order = null;
$order_items = [];

// Lấy thông tin đơn hàng
$stmt = $conn->prepare("SELECT o.id, u.email, u.full_name, o.total, o.status, o.created_at 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
}
$stmt->close();

// Lấy chi tiết đơn hàng
if ($order) {
    $stmt = $conn->prepare("SELECT p.name, oi.quantity, oi.price 
                            FROM order_items oi 
                            JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $order_items[] = $row;
    }
    $stmt->close();
}

// Log truy cập
$log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'view_order', ?)");
$details = "Viewed order ID: $order_id";
$log_stmt->bind_param("is", $_SESSION['user_id'], $details);
$log_stmt->execute();
$log_stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <header class="admin-header">
        <h1>Admin Dashboard - Anh Em Rọt Store</h1>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage_products.php"><i class="fas fa-box"></i> Sản phẩm</a>
            <a href="manage_users.php"><i class="fas fa-users"></i> Người dùng</a>
            <a href="manage_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Đơn hàng</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </nav>
    </header>

    <main class="admin-container">
        <?php if (!$order): ?>
            <div class="alert alert-danger">
                <p>Không tìm thấy đơn hàng.</p>
            </div>
        <?php else: ?>
            <section class="order-detail">
                <h2>Chi tiết đơn hàng #<?= htmlspecialchars($order['id']) ?></h2>
                <div class="order-info">
                    <p><strong>Email khách hàng:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Tên khách hàng:</strong> <?= htmlspecialchars($order['full_name'] ?? '') ?></p>
                    <p><strong>Tổng tiền:</strong> $<?= number_format($order['total'], 2) ?></p>
                    <p><strong>Trạng thái:</strong> 
                        <?php
                        $status_text = [
                            'pending' => 'Chờ xử lý',
                            'processing' => 'Đang xử lý',
                            'shipped' => 'Đã giao',
                            'delivered' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy'
                        ];
                        echo htmlspecialchars($status_text[$order['status']]);
                        ?>
                    </p>
                    <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
                <h3>Sản phẩm trong đơn hàng</h3>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['quantity']) ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>$<?= number_format($item['quantity'] * $item['price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="manage_orders.php" class="action-button"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </section>
        <?php endif; ?>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>
</body>
</html>