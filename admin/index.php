<?php
session_start();
require_once '../config/db.php';
require_once 'admin_header.php';

// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Log truy cập dashboard
$user_id = $_SESSION['user_id'];
$log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'visit_admin_dashboard', ?)");
$details = "Admin visited dashboard at " . date('h:i A, d/m/Y', time());
$log_stmt->bind_param("is", $user_id, $details);
$log_stmt->execute();
$log_stmt->close();

// Thống kê chung
// Corrected: The COUNT(*) queries were already good, just re-emphasizing they directly fetch from the tables.
$total_users = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0] ?? 0;
$total_orders = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0] ?? 0;
// Ensure total_revenue correctly sums only 'delivered' orders.
$total_revenue = $conn->query("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered'")->fetch_row()[0] ?? 0;

// Kiểm tra đơn hàng mới
// The existing query for new_orders is sound for checking orders in the last 24 hours with 'pending' status.
$new_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND order_date > NOW() - INTERVAL 24 HOUR")->fetch_row()[0] ?? 0;

$recent_orders = [];
$stmt = $conn->prepare("SELECT o.id, u.email, o.total_amount, o.status, o.order_date
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        ORDER BY o.order_date DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $recent_orders[] = $row;
}
$stmt->close();

// Thống kê đơn hàng theo trạng thái cho biểu đồ
$order_stats = [
    'pending' => 0,
    'processing' => 0,
    'shipped' => 0,
    'delivered' => 0,
    'cancelled' => 0
];
$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    // Ensure the status keys match those in the database (case-sensitive if applicable)
    $order_stats[$row['status']] = $row['count'];
}
$stmt->close();

// Thống kê doanh thu theo tháng cho biểu đồ đường
$revenue_stats = [];
$revenue_labels = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i month"));
    $month_label = date('M Y', strtotime("-$i month"));

    $stmt = $conn->prepare("SELECT SUM(total_amount) FROM orders WHERE status = 'delivered' AND DATE_FORMAT(order_date, '%Y-%m') = ?");
    $stmt->bind_param("s", $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $revenue = $result->fetch_row()[0] ?? 0;
    $stmt->close();

    $revenue_labels[] = $month_label;
    $revenue_stats[] = $revenue;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .admin-container-page {
            padding: 30px;
            padding-left: 60px;
            max-width: 1200px;
            margin: 20px auto;
            color: var(--text-color);
        }
    </style>
</head>

<body>
    <main class="admin-container-page">
        <?php if ($new_orders > 0): ?>
            <div class="notification">
                <i class="fas fa-bell"></i>
                <span>Bạn có <?= $new_orders ?> đơn hàng mới trong 24 giờ qua!</span>
            </div>
        <?php endif; ?>

        <section class="dashboard-stats">
            <div class="stat-card">
                <h3>Tổng khách hàng</h3>
                <p><?= number_format($total_users, 0, ',', '.') ?></p>
            </div>
            <div class="stat-card">
                <h3>Tổng sản phẩm</h3>
                <p><?= number_format($total_products, 0, ',', '.') ?></p>
            </div>
            <div class="stat-card">
                <h3>Tổng đơn hàng</h3>
                <p><?= number_format($total_orders, 0, ',', '.') ?></p>
            </div>
            <div class="stat-card">
                <h3>Doanh thu (Đã giao)</h3>
                <p><?= number_format($total_revenue, 0, ',', '.') ?> VNĐ</p>
            </div>
        </section>

        <section class="recent-orders">
            <h2>Đơn hàng gần đây</h2>
            <?php if (empty($recent_orders)): ?>
                <p>Không có đơn hàng nào.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['id']) ?></td>
                                <td><?= htmlspecialchars($order['email']) ?></td>
                                <td><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</td>
                                <td>
                                    <span class="status-badge <?= htmlspecialchars($order['status']) ?>">
                                        <?php
                                        $status_labels = [
                                            'pending' => 'Chờ xử lý',
                                            'processing' => 'Đang xử lý',
                                            'shipped' => 'Đã ship',
                                            'delivered' => 'Đã giao',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $status_labels[$order['status']] ?? htmlspecialchars($order['status']);
                                        ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="chart-container">
            <h2>Thống kê trạng thái đơn hàng</h2>
            <canvas id="orderStatusChart"></canvas>
            <script>
                const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
                new Chart(orderStatusCtx, {
                    type: 'bar',
                    data: {
                        labels: ["Chờ xử lý", "Đang xử lý", "Đã giao", "Đã ship", "Đã hủy"],
                        datasets: [{
                            label: "Số lượng đơn hàng",
                            data: [
                                <?= $order_stats['pending'] ?>,
                                <?= $order_stats['processing'] ?>,
                                <?= $order_stats['shipped'] ?>,
                                <?= $order_stats['delivered'] ?>,
                                <?= $order_stats['cancelled'] ?>
                            ],
                            backgroundColor: ["#ffc107", "#17a2b8", "#ffffff", "#28a745", "#dc3545"],
                            borderColor: ["#e0a800", "#138496", "#3f3d6f", "#218838", "#c82333"],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: "Số lượng đơn hàng",
                                    color: "#ffffff",
                                    font: { size: 20 }
                                },
                                ticks: {
                                    color: "#ffffff",
                                    font: { size: 15 }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: "Trạng thái",
                                    color: "#ffffff",
                                    font: { size: 20 }
                                },
                                ticks: {
                                    color: "#ffffff",
                                    font: { size: 15 }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: "Thống kê trạng thái đơn hàng",
                                color: "#ffffff",
                                font: { size: 25 }
                            }
                        }
                    }
                });
            </script>
        </section>

        <section class="chart-container">
            <h2>Doanh thu theo tháng</h2>
            <canvas id="revenueChart"></canvas>
            <script>
                // Line Chart for Monthly Revenue
                const revenueCtx = document.getElementById('revenueChart').getContext('2d');
                new Chart(revenueCtx, {
                    "type": "line",
                    "data": {
                        "labels": <?= json_encode($revenue_labels) ?>, // Dynamically populate labels
                        "datasets": [{
                            "label": "Doanh thu (VNĐ)",
                            "data": <?= json_encode($revenue_stats) ?>, // Dynamically populate data
                            "fill": false,
                            "borderColor": "#007bff",
                            "backgroundColor": "#007bff",
                            "tension": 0.1
                        }]
                    },
                    "options": {
                        "responsive": true,
                        "scales": {
                            "y": {
                                "beginAtZero": true,
                                "title": {
                                    "display": true,
                                    "text": "Doanh thu (VNĐ)",
                                    "color": "#ffffff",
                                    "font": { "size": 20 }
                                },
                                "ticks": {
                                    "color": "#ffffff",
                                    "callback": function (value) {
                                        return value.toLocaleString('vi-VN');
                                    }
                                }
                            },
                            "x": {
                                "title": {
                                    "display": true,
                                    "text": "Tháng",
                                    "color": "#ffffff",
                                    "font": { "size": 20 }
                                },
                                "ticks": {
                                    "color": "#ffffff"
                                }
                            }
                        },
                        "plugins": {
                            "legend": {
                                "display": true,
                                "labels": {
                                    "color": "#ffffff",
                                    "font": { "size": 20 }
                                }
                            },
                            "title": {
                                "display": true,
                                "text": "Doanh thu 12 tháng gần nhất",
                                "color": "#ffffff",
                                "font": { "size": 25 }
                            }
                        }
                    }
                });
            </script>
        </section>

        <footer style="text-align: center; padding: 20px 0;">
            © 2025 Anh Em Rọt Store. All rights reserved.
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Sidebar toggle logic
                const sidebar = document.querySelector('.sidebar');
                document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
                    button.addEventListener('click', () => {
                        sidebar.classList.toggle('collapsed');
                    });
                });

                // Highlight active menu item
                const currentPath = window.location.pathname.split('/').pop();
                document.querySelectorAll('.sidebar nav a').forEach(link => {
                    const linkHref = link.getAttribute('href').split('/').pop();
                    if (linkHref === currentPath) {
                        link.classList.add('active');
                    }
                });

                // Auto-dismiss notification after 5 seconds
                const notification = document.querySelector('.notification');
                if (notification) {
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 5000);
                }
            });
        </script>
    </main>
</body>

</html>