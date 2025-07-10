<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = false;

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $status = $_POST['status'] ?? '';

    if (!in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
        $errors[] = "Trạng thái không hợp lệ.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            $success = true;
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'update_order_status', ?)");
            $details = "Updated order ID: $id to status: $status";
            $log_stmt->bind_param("is", $_SESSION['user_id'], $details);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Lấy danh sách đơn hàng
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['pending', 'processing', 'shipped', 'delivered', 'cancelled']) ? $_GET['status'] : '';
$orders = [];
if ($status_filter) {
    $stmt = $conn->prepare("SELECT o.id, u.email, o.total, o.status, o.created_at 
                            FROM orders o 
                            JOIN users u ON o.user_id = u.id 
                            WHERE o.status = ? 
                            ORDER BY o.created_at DESC");
    $stmt->bind_param("s", $status_filter);
} else {
    $stmt = $conn->prepare("SELECT o.id, u.email, o.total, o.status, o.created_at 
                            FROM orders o 
                            JOIN users u ON o.user_id = u.id 
                            ORDER BY o.created_at DESC");
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng | Anh Em Rọt Store</title>
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
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success">
                <p>Cập nhật trạng thái thành công!</p>
            </div>
        <?php endif; ?>

        <section class="orders-grid">
            <h2>Danh sách đơn hàng</h2>
            <div class="filter">
                <a href="manage_orders.php" class="filter-link <?= empty($status_filter) ? 'active' : '' ?>">Tất cả</a>
                <a href="manage_orders.php?status=pending" class="filter-link <?= $status_filter === 'pending' ? 'active' : '' ?>">Chờ xử lý</a>
                <a href="manage_orders.php?status=processing" class="filter-link <?= $status_filter === 'processing' ? 'active' : '' ?>">Đang xử lý</a>
                <a href="manage_orders.php?status=shipped" class="filter-link <?= $status_filter === 'shipped' ? 'active' : '' ?>">Đã giao</a>
                <a href="manage_orders.php?status=delivered" class="filter-link <?= $status_filter === 'delivered' ? 'active' : '' ?>">Hoàn thành</a>
                <a href="manage_orders.php?status=cancelled" class="filter-link <?= $status_filter === 'cancelled' ? 'active' : '' ?>">Đã hủy</a>
            </div>
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
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= htmlspecialchars($o['id']) ?></td>
                            <td><?= htmlspecialchars($o['email']) ?></td>
                            <td>$<?= number_format($o['total'], 2) ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="pending" <?= $o['status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                        <option value="processing" <?= $o['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                        <option value="shipped" <?= $o['status'] === 'shipped' ? 'selected' : '' ?>>Đã giao</option>
                                        <option value="delivered" <?= $o['status'] === 'delivered' ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="cancelled" <?= $o['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                </form>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                            <td>
                                <a href="view_order.php?id=<?= $o['id'] ?>" class="action-button">Xem</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>
</body>
</html>