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

// Kiểm tra đơn hàng mới
$new_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND created_at > NOW() - INTERVAL 24 HOUR")->fetch_row()[0];

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
    <title>Admin Dashboard | Apple Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <header class="admin-header">
        <h1>Apple Store Admin</h1>
        <nav>
            <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage_products.php"><i class="fas fa-mobile-alt"></i> Products</a>
            <a href="manage_users.php"><i class="fas fa-users"></i> Users</a>
            <a href="manage_orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </header>

    <main class="admin-container">
        <?php if ($new_orders > 0): ?>
            <div class="notification">
                <i class="fas fa-bell"></i>
                <span>You have <?= $new_orders ?> new order<?= $new_orders > 1 ? 's' : '' ?> in the last 24 hours!</span>
                <a href="manage_orders.php?status=pending">View Orders</a>
            </div>
        <?php endif; ?>

        <section class="stats">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3>Total Users</h3>
                <p><?= number_format($total_users) ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-mobile-alt"></i>
                <h3>Products</h3>
                <p><?= number_format($total_products) ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-bag"></i>
                <h3>Orders</h3>
                <p><?= number_format($total_orders) ?></p>
            </div>
            <div class="stat-card">
                <i class="fas fa-dollar-sign"></i>
                <h3>Revenue</h3>
                <p>$<?= number_format($total_revenue, 2) ?></p>
            </div>
        </section>

        <section class="recent-orders">
            <h2>Recent Orders</h2>
            <?php if (empty($recent_orders)): ?>
                <div style="text-align: center; padding: 40px; color: var(--text-secondary);">
                    <i class="fas fa-shopping-bag" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.3;"></i>
                    <p>No orders yet.</p>
                </div>
            <?php else: ?>
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><strong>#<?= htmlspecialchars($order['id']) ?></strong></td>
                                <td><?= htmlspecialchars($order['email']) ?></td>
                                <td><strong>$<?= number_format($order['total'], 2) ?></strong></td>
                                <td>
                                    <span class="status-badge status-<?= $order['status'] ?>">
                                        <?php
                                        $status_icons = [
                                            'pending' => '<i class="fas fa-clock"></i>',
                                            'processing' => '<i class="fas fa-cog fa-spin"></i>',
                                            'shipped' => '<i class="fas fa-shipping-fast"></i>',
                                            'delivered' => '<i class="fas fa-check-circle"></i>',
                                            'cancelled' => '<i class="fas fa-times-circle"></i>'
                                        ];
                                        $status_text = [
                                            'pending' => 'Pending',
                                            'processing' => 'Processing',
                                            'shipped' => 'Shipped',
                                            'delivered' => 'Delivered',
                                            'cancelled' => 'Cancelled'
                                        ];
                                        echo $status_icons[$order['status']] . ' ' . $status_text[$order['status']];
                                        ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <a href="view_order.php?id=<?= $order['id'] ?>" class="action-button">
                                        <i class="fas fa-eye"></i> View
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
        <p>© 2025 Apple Store. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Highlight active menu
            const currentPath = window.location.pathname.split('/').pop();
            document.querySelectorAll('.admin-header nav a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });

            // Check new orders every 30s
            setInterval(() => {
                fetch('check_new_orders.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.new_orders > 0 && !document.querySelector('.notification')) {
                            const notification = document.createElement('div');
                            notification.className = 'notification';
                            notification.innerHTML = `
                                <i class="fas fa-bell"></i>
                                <span>You have ${data.new_orders} new order${data.new_orders > 1 ? 's' : ''} in the last 24 hours!</span>
                                <a href="manage_orders.php?status=pending">View Orders</a>
                            `;
                            document.querySelector('.admin-container').prepend(notification);
                        }
                    })
                    .catch(console.error);
            }, 30000);

            // Animate stat cards
            document.querySelectorAll('.stat-card').forEach((card, i) => {
                card.style.animationDelay = `${i * 0.1}s`;
                card.style.animation = 'slideIn 0.6s ease-out forwards';
            });
        });
    </script>

    <style>
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: rgba(255, 149, 0, 0.1);
            color: var(--warning-color);
        }

        .status-processing {
            background: rgba(88, 86, 214, 0.1);
            color: var(--secondary-color);
        }

        .status-shipped {
            background: rgba(0, 122, 255, 0.1);
            color: var(--primary-color);
        }

        .status-delivered {
            background: rgba(52, 199, 89, 0.1);
            color: var(--success-color);
        }

        .status-cancelled {
            background: rgba(255, 59, 48, 0.1);
            color: var(--error-color);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>
</html>
