<?php
session_start();
require_once '../config/db.php';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Log truy cập dashboard
$user_id = $_SESSION['user_id'];
$log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'visit_admin_dashboard', ?)");
$details = "Admin visited dashboard";
$log_stmt->bind_param("is", $user_id, $details);
$log_stmt->execute();
$log_stmt->close();

// Thống kê
$total_users = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0];
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$total_revenue = $conn->query("SELECT SUM(total) FROM orders WHERE status = 'delivered'")->fetch_row()[0] ?? 0;

// Lấy 5 đơn hàng gần nhất
$recent_orders = [];
$stmt = $conn->prepare("SELECT o.id, u.email, o.total, o.status, o.created_at 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.id 
                        ORDER BY o.created_at DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recent_orders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <header class="admin-header">
        <h1>Admin Dashboard - Anh Em Rọt Store</h1>
        <nav>
            <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage_products.php"><i class="fas fa-box"></i> Sản phẩm</a>
            <a href="manage_users.php"><i class="fas fa-users"></i> Người dùng</a>
            <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Đơn hàng</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </nav>
    </header>

    <main class="admin-container">
        <section class="stats">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Tổng người dùng</h3>
                <p><?= htmlspecialchars($total_users) ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <h3>Tổng sản phẩm</h3>
                <p><?= htmlspecialchars($total_products) ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <h3>Tổng đơn hàng</h3>
                <p><?= htmlspecialchars($total_orders) ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>Doanh thu</h3>
                <p>$<?= number_format($total_revenue, 2) ?></p>
            </div>
        </section>

        <section class="recent-orders">
            <h2>Đơn hàng gần đây</h2>
            <?php if (empty($recent_orders)): ?>
                <p>Không có đơn hàng nào.</p>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Email khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['email']) ?></td>
                                <td>$<?= number_format($order['total'], 2) ?></td>
                                <td>
                                    <?php
                                    $status = $order['status'];
                                    $status_text = [
                                        'pending' => 'Chờ xử lý',
                                        'processing' => 'Đang xử lý',
                                        'shipped' => 'Đã giao',
                                        'delivered' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy'
                                    ];
                                    echo htmlspecialchars($status_text[$status]);
                                    ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="view_order.php?id=<?= $order['id'] ?>" class="action-button">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <script>
        // JavaScript đơn giản để làm nổi bật menu active
        document.querySelectorAll('.admin-header nav a').forEach(link => {
            link.addEventListener('click', () => {
                document.querySelector('.admin-header nav a.active')?.classList.remove('active');
                link.classList.add('active');
            });
        });
    </script>
</body>
</html>