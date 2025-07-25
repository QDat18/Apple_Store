<?php
require_once '../config/db.php';
require 'admin_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại: " . mysqli_connect_error());
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = intval($_POST['id']);
    $status = $_POST['status'] ?? '';

    if (!in_array($status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])) {
        $errors[] = "Trạng thái không hợp lệ.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmt === false) {
            $errors[] = "Lỗi chuẩn bị truy vấn: " . $conn->error;
        } else {
            $stmt->bind_param("si", $status, $id);
            if ($stmt->execute()) {
                $success = true;
                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details, ip_address, created_at) VALUES (?, 'update_order_status', ?, ?, NOW())");
                $details = "Updated order ID: $id to status: $status";
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $log_stmt->bind_param("iss", $_SESSION['user_id'], $details, $ip_address);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Lấy tham số tìm kiếm, sắp xếp và phân trang
$search_query = $_GET['search'] ?? '';
$status_filter = isset($_GET['status']) && in_array($_GET['status'], ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded']) ? $_GET['status'] : '';
$sort_by = $_GET['sort_by'] ?? 'order_date';
$sort_order = $_GET['sort_order'] ?? 'DESC';

$allowed_sort_by = ['id', 'email', 'total_amount', 'status', 'order_date'];
if (!in_array($sort_by, $allowed_sort_by)) {
    $sort_by = 'order_date';
}
if (!in_array(strtoupper($sort_order), ['ASC', 'DESC'])) {
    $sort_order = 'DESC';
}

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT o.id, u.email, o.total_amount, o.status, o.order_date
        FROM orders o
        JOIN users u ON o.user_id = u.id";
$where_clauses = [];
$params = [];
$types = "";

if ($status_filter) {
    $where_clauses[] = "o.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($search_query) {
    $where_clauses[] = "(o.id LIKE ? OR u.email LIKE ?)";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $types .= "ss";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$count_stmt = $conn->prepare(str_replace("o.id, u.email, o.total_amount, o.status, o.order_date", "COUNT(*)", $sql));
if ($count_stmt === false) {
    $errors[] = "Lỗi chuẩn bị truy vấn đếm: " . $conn->error;
} else {
    if (!empty($params)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $total_orders = $count_stmt->get_result()->fetch_row()[0];
    $count_stmt->close();
}

$total_pages = ceil($total_orders / $limit);

$sql .= " ORDER BY " . $sort_by . " " . $sort_order . " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $errors[] = "Lỗi chuẩn bị truy vấn chính: " . $conn->error;
} else {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
if ($stmt === false) {
    $errors[] = "Lỗi chuẩn bị truy vấn thống kê: " . $conn->error;
} else {
    $stmt->execute();
    $result = $stmt->get_result();
    $order_status_counts = [
        'pending' => 0,
        'processing' => 0,
        'shipped' => 0,
        'delivered' => 0,
        'cancelled' => 0,
        'refunded' => 0
    ];
    while ($row = $result->fetch_assoc()) {
        $status_key = strtolower($row['status']);
        if (isset($order_status_counts[$status_key])) {
            $order_status_counts[$status_key] = $row['count'];
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            /* Màu sắc nhẹ nhàng hơn */
            --primary-color: #2979ff;
            --secondary-color: #f8f9fa;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --info-color: #03a9f4;
            --dark-color: #2c3e50;
            --light-color: #ffffff;
            --border-color: #e0e0e0;
            --table-header: #f5f6fa;
            --text-color: #37474f;
            --hover-color: #f5f5f5;
        }

        .card {
            background: var(--light-color);
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            padding: 2rem;
            max-width: 1400px;
            width: 150%;
        }

        .card h2 {
            color: #000000;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* Filter Section */
        .filter-sort-section {
            background: var(--secondary-color);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .filter-sort-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            margin-bottom: 0;
        }


        .form-group label {
            color: #000000;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--light-color);
            color: var(--dark-color);
            font-size: 0.9rem;
        }

        .filter-sort-section label:hover,
        .form-group label:hover {
            color: var(--primary-color);
        }

        /* Table Styles */
        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 2rem;
            background: var(--light-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .data-table th {
            background: var(--table-header);
            color: #000000;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
        }

        .data-table td {
            padding: 1rem;
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
        }

        .data-table tbody tr:hover {
            background: var(--hover-color);
        }

        /* Status Colors */
        .status-pending {
            color: var(--warning-color);
        }

        .status-processing {
            color: var(--info-color);
        }

        .status-shipped {
            color: var(--primary-color);
        }

        .status-delivered {
            color: var(--success-color);
        }

        .status-cancelled {
            color: var(--danger-color);
        }

        .status-refunded {
            color: #6c757d;
        }

        /* Chart Container */
        .chart-container {
            background: var(--light-color);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
        }

        .pagination a {
            background: var(--secondary-color);
            color: var(--dark-color);
            transition: all 0.2s ease;
        }

        .pagination a:hover {
            background: var(--primary-color);
            color: var(--light-color);
        }

        .pagination .current {
            background: var(--primary-color);
            color: var(--light-color);
        }

        .page-info {
            color: var(--dark-color);
            font-size: 0.9rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--light-color);
        }

        .btn-primary:hover {
            background: #005cbd;
            transform: translateY(-1px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--dark-color);
        }

        .empty-state i {
            font-size: 2rem;
            color: var(--border-color);
            margin-bottom: 1rem;
        }

        /* Notifications */
        .notification {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .notification.success {
            background: rgba(41, 204, 106, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .notification.error {
            background: rgba(255, 59, 48, 0.1);
            color: var(--danger-color);
            border: 1px solid var(--danger-color);
        }
    </style>
</head>

<body>
    <main class="admin-container">
        <?php if (!empty($errors)): ?>
            <div class="notification error">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif ($success || isset($_GET['success'])): ?>
            <div class="notification success">
                <p>Thao tác thành công!</p>
            </div>
        <?php endif; ?>

        <section class="card">
            <h2>Danh sách đơn hàng</h2>

            <div class="filter-sort-section">
                <form class="filter-sort-form" method="GET" action="manage_orders.php">
                    <div class="form-group">
                        <label for="status">Lọc theo trạng thái:</label>
                        <select id="status" name="status" onchange="this.form.submit()">
                            <option value="" <?= empty($status_filter) ? 'selected' : '' ?>>Tất cả</option>
                            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Chờ xử lý
                            </option>
                            <option value="processing" <?= $status_filter === 'processing' ? 'selected' : '' ?>>Đang xử lý
                            </option>
                            <option value="shipped" <?= $status_filter === 'shipped' ? 'selected' : '' ?>>Đã giao</option>
                            <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>Hoàn thành
                            </option>
                            <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Đã hủy
                            </option>
                            <option value="refunded" <?= $status_filter === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sort_by">Sắp xếp theo:</label>
                        <select id="sort_by" name="sort_by">
                            <option value="id" <?= $sort_by === 'id' ? 'selected' : '' ?>>Mã đơn</option>
                            <option value="email" <?= $sort_by === 'email' ? 'selected' : '' ?>>Email</option>
                            <option value="total_amount" <?= $sort_by === 'total_amount' ? 'selected' : '' ?>>Tổng tiền
                            </option>
                            <option value="status" <?= $sort_by === 'status' ? 'selected' : '' ?>>Trạng thái</option>
                            <option value="order_date" <?= $sort_by === 'order_date' ? 'selected' : '' ?>>Ngày đặt</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sort_order">Thứ tự:</label>
                        <select id="sort_order" name="sort_order">
                            <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                            <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
                        </select>
                    </div>
                    <div class="form-group search-group">
                        <input type="text" name="search" placeholder="Tìm theo ID/Email..."
                            value="<?= htmlspecialchars($search_query) ?>">
                        <button type="submit" class="btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><a
                                    href="?<?= http_build_query(array_merge($_GET, ['sort_by' => 'id', 'sort_order' => ($sort_by === 'id' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])) ?>">Mã
                                    đơn<?php if ($sort_by === 'id'): ?><i
                                            class="fas fa-sort<?= $sort_order === 'ASC' ? '-up' : '-down' ?>"></i><?php endif; ?></a>
                            </th>
                            <th><a
                                    href="?<?= http_build_query(array_merge($_GET, ['sort_by' => 'email', 'sort_order' => ($sort_by === 'email' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])) ?>">Email
                                    khách hàng<?php if ($sort_by === 'email'): ?><i
                                            class="fas fa-sort<?= $sort_order === 'ASC' ? '-up' : '-down' ?>"></i><?php endif; ?></a>
                            </th>
                            <th><a
                                    href="?<?= http_build_query(array_merge($_GET, ['sort_by' => 'total_amount', 'sort_order' => ($sort_by === 'total_amount' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])) ?>">Tổng
                                    tiền<?php if ($sort_by === 'total_amount'): ?><i
                                            class="fas fa-sort<?= $sort_order === 'ASC' ? '-up' : '-down' ?>"></i><?php endif; ?></a>
                            </th>
                            <th><a
                                    href="?<?= http_build_query(array_merge($_GET, ['sort_by' => 'status', 'sort_order' => ($sort_by === 'status' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])) ?>">Trạng
                                    thái<?php if ($sort_by === 'status'): ?><i
                                            class="fas fa-sort<?= $sort_order === 'ASC' ? '-up' : '-down' ?>"></i><?php endif; ?></a>
                            </th>
                            <th><a
                                    href="?<?= http_build_query(array_merge($_GET, ['sort_by' => 'order_date', 'sort_order' => ($sort_by === 'order_date' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])) ?>">Ngày
                                    đặt<?php if ($sort_by === 'order_date'): ?><i
                                            class="fas fa-sort<?= $sort_order === 'ASC' ? '-up' : '-down' ?>"></i><?php endif; ?></a>
                            </th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <h3>Không tìm thấy đơn hàng nào</h3>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td><?= htmlspecialchars($o['id']) ?></td>
                                    <td><?= htmlspecialchars($o['email']) ?></td>
                                    <td>$<?= number_format($o['total_amount'], 2) ?></td>
                                    <td>
                                        <form method="POST" action="manage_orders.php?<?= http_build_query($_GET) ?>"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái đơn hàng này?');">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="pending" <?= strtolower($o['status']) === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                                <option value="processing" <?= strtolower($o['status']) === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                                <option value="shipped" <?= strtolower($o['status']) === 'shipped' ? 'selected' : '' ?>>Đã giao</option>
                                                <option value="delivered" <?= strtolower($o['status']) === 'delivered' ? 'selected' : '' ?>>Hoàn thành</option>
                                                <option value="cancelled" <?= strtolower($o['status']) === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                                <option value="refunded" <?= strtolower($o['status']) === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($o['order_date'])) ?></td>
                                    <td class="action-buttons">
                                        <a href="view_order.php?id=<?= $o['id'] ?>" class="action-btn btn-primary"><i
                                                class="fas fa-eye"></i> Xem</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <?php if ($total_pages > 1): ?>
                    <?php if ($page > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">« Trang trước</a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <?php
                        $query_params = array_merge($_GET, ['page' => $i]);
                        $link = '?' . http_build_query($query_params);
                        ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= $link ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Trang sau »</a>
                    <?php endif; ?>
                <?php endif; ?>
                <span class="page-info">Trang <?= $page ?> / <?= $total_pages ?> (Tổng: <?= $total_orders ?> đơn
                    hàng)</span>
            </div>
        </section>

        <section class="card chart-container">
            <h2>Thống kê trạng thái đơn hàng</h2>
            <canvas id="orderStatusChart" style="max-width: 600px; margin: 0 auto;"></canvas>
        </section>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <script src="../scripts/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('orderStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Chờ xử lý', 'Đang xử lý', 'Đã giao', 'Hoàn thành', 'Đã hủy', 'Đã hoàn tiền'],
                    datasets: [{
                        data: [
                            <?php echo $order_status_counts['pending']; ?>,
                            <?php echo $order_status_counts['processing']; ?>,
                            <?php echo $order_status_counts['shipped']; ?>,
                            <?php echo $order_status_counts['delivered']; ?>,
                            <?php echo $order_status_counts['cancelled']; ?>,
                            <?php echo $order_status_counts['refunded']; ?>
                        ],
                        backgroundColor: [
                            '#ff9f0a80', // warning
                            '#64d2ff80', // info
                            '#0071e380', // primary
                            '#29cc6a80', // success
                            '#ff3b3080', // danger
                            '#6c757d80'  // grey
                        ],
                        borderColor: [
                            '#ff9f0a', // warning
                            '#64d2ff', // info
                            '#0071e3', // primary
                            '#29cc6a', // success
                            '#ff3b30', // danger
                            '#6c757d'  // grey
                        ],
                        borderWidth: 2
                    }]
                }, options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: 'var(--text-primary)' }
                        },
                        title: {
                            display: true,
                            text: 'Thống kê trạng thái đơn hàng',
                            color: 'var(--text-primary)',
                            font: { size: 18 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} đơn (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>