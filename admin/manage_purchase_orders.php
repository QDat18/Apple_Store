<?php
session_start();
require_once '../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = false;
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Lỗi bảo mật: CSRF token không hợp lệ.";
    } else {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING); // Deprecated, consider using htmlspecialchars or a custom function

        $valid_statuses = ['Pending', 'Processing', 'Received', 'Cancelled'];
        if (!$id || !in_array($status, $valid_statuses)) {
            $errors[] = "ID hoặc trạng thái không hợp lệ.";
        } else {
            $stmt = $conn->prepare("UPDATE purchase_orders SET status = ? WHERE id = ?");
            if ($stmt === false) {
                $errors[] = "Lỗi chuẩn bị truy vấn: " . $conn->error;
            } else {
                $stmt->bind_param("si", $status, $id);
                if ($stmt->execute()) {
                    $success = true;
                    $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'update_purchase_order_status', ?)");
                    $details = "Updated purchase order ID: $id to status: $status";
                    $log_stmt->bind_param("is", $_SESSION['user_id'], $details);
                    $log_stmt->execute();
                    $log_stmt->close();
                } else {
                    $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

// Lấy danh sách hóa đơn nhập
$limit = 10;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]);
$offset = ($page - 1) * $limit;

$search_query = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING) ?? ''; // Deprecated
$status_filter = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING); // Deprecated
$sort_by = filter_input(INPUT_GET, 'sort_by', FILTER_SANITIZE_STRING) ?? 'order_date'; // Deprecated
$sort_order = filter_input(INPUT_GET, 'sort_order', FILTER_SANITIZE_STRING) ?? 'DESC'; // Deprecated

$allowed_sort_by = ['id', 'supplier_name', 'user_email', 'order_date', 'total_amount', 'status'];
$sort_by = in_array($sort_by, $allowed_sort_by) ? $sort_by : 'order_date';
$allowed_sort_order = ['ASC', 'DESC'];
$sort_order = in_array(strtoupper($sort_order), $allowed_sort_order) ? $sort_order : 'DESC';

$sql = "
    SELECT po.id, po.order_date, po.total_amount, po.status, 
           s.name AS supplier_name, u.email AS user_email
    FROM purchase_orders po
    JOIN suppliers s ON po.supplier_id = s.id
    JOIN users u ON po.user_id = u.id";
$count_sql = "
    SELECT COUNT(*) 
    FROM purchase_orders po 
    JOIN suppliers s ON po.supplier_id = s.id
    JOIN users u ON po.user_id = u.id";

$where_clauses = [];
$params = [];
$types = "";

if ($status_filter && in_array($status_filter, ['Pending', 'Processing', 'Received', 'Cancelled'])) {
    $where_clauses[] = "po.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($search_query) {
    $where_clauses[] = "(po.id LIKE ? OR s.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $types .= "sss";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
    $count_sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$count_stmt = $conn->prepare($count_sql);
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

$sql .= " ORDER BY $sort_by $sort_order LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    $errors[] = "Lỗi chuẩn bị truy vấn chính: " . $conn->error;
} else {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $purchase_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Thống kê trạng thái
$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM purchase_orders GROUP BY status");
if ($stmt === false) {
    $errors[] = "Lỗi chuẩn bị truy vấn thống kê: " . $conn->error;
} else {
    $stmt->execute();
    $result = $stmt->get_result();
    $status_counts = [
        'Pending' => 0,
        'Processing' => 0,
        'Received' => 0,
        'Cancelled' => 0
    ];
    while ($row = $result->fetch_assoc()) {
        if (isset($status_counts[$row['status']])) {
            $status_counts[$row['status']] = $row['count'];
        }
    }
    $stmt->close();
}

require 'admin_header.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hóa đơn nhập | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        :root {
            --primary-color: #0056b3; /* Darker blue for stronger primary actions */
            --secondary-color: #e9ecef; /* Lighter grey for backgrounds, subtle contrast */
            --tertiary-color: #f8f9fa; /* Even lighter for distinct sections */
            --text-color: #212529; /* Darker text for readability */
            --light-text-color: #495057; /* Slightly lighter text for secondary info */
            --light-color: #ffffff; /* Pure white for elements on dark backgrounds */
            --border-color: #ced4da; /* Medium grey for borders, good contrast */
            --success-color: #28a745; /* Stronger green for success */
            --warning-color: #ffc107; /* Clearer yellow for warning */
            --danger-color: #dc3545; /* Vivid red for danger */
            --info-color: #17a2b8; /* Brighter teal for info */
            --table-header-bg: #dee2e6; /* Distinct grey for table headers */
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* More pronounced shadow */
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5; /* A very light blue-grey for the overall background */
            color: var(--text-color);
            line-height: 1.6;
        }

        .admin-container {
            padding: 2.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            color: var(--primary-color);
            font-size: 2.2rem;
            font-weight: 700;
            position: relative;
            display: inline-block;
            padding-bottom: 10px;
        }
        .section-header h2::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--info-color); /* Using info-color for a clear accent */
            border-radius: 2px;
        }

        .filter-and-search {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1.5rem;
            background-color: var(--tertiary-color); /* Using a lighter tertiary for section background */
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: var(--box-shadow);
        }

        .filter {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .filter-link {
            padding: 0.7rem 1.4rem;
            background: var(--light-color); /* White background for inactive filters */
            color: var(--text-color); /* Dark text for contrast */
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            border: 1px solid var(--border-color);
        }

        .filter-link.active,
        .filter-link:hover {
            background: var(--primary-color); /* Primary color for active/hover */
            color: var(--light-color); /* White text on primary background */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15); /* Slightly darker shadow */
            border-color: var(--primary-color);
        }

        .search-box {
            display: flex;
            gap: 0.8rem;
        }

        .search-box input {
            padding: 0.7rem 1.2rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            min-width: 300px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        .search-box input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.25); /* Primary color with transparency */
        }

        .search-box button {
            padding: 0.7rem 1.5rem;
            background: var(--primary-color);
            color: var(--light-color);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .search-box button:hover {
            background-color: #004085; /* Darker shade of primary on hover */
            transform: translateY(-2px);
        }

        .data-table-container {
            background: var(--light-color);
            border-radius: 10px;
            box-shadow: var(--box-shadow);
            margin-bottom: 2.5rem;
            padding: 2rem;
            overflow-x: auto;
        }

        .data-table-container h3 {
            color: var(--primary-color);
            font-size: 1.4rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }

        .data-table th,
        .data-table td {
            padding: 1.2rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table th {
            background: var(--table-header-bg); /* Distinct background for headers */
            font-weight: 600;
            color: var(--text-color); /* Darker text for headers */
            text-transform: uppercase;
            font-size: 0.95rem;
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .data-table thead tr:first-child th:first-child { border-top-left-radius: 8px; }
        .data-table thead tr:first-child th:last-child { border-top-right-radius: 8px; }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background-color: #f6f8fa; /* Subtle hover effect, slightly darker than main background */
        }

        .data-table th a {
            color: var(--text-color); /* Match header text color */
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            transition: color 0.2s ease;
        }
        .data-table th a:hover {
            color: var(--primary-color); /* Primary color on hover for sorting links */
        }

        .status-select {
            padding: 0.5rem 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--light-color); /* White background */
            color: var(--text-color); /* Dark text */
            font-size: 0.9rem;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23666%22%20d%3D%22M287%2C114.7L154.7%2C18.8c-8.9-5.9-20.5-5.9-29.4%2C0L5.4%2C114.7c-7.9%2C5.9-8.9%2C17.3-2%2C24.4s18.2%2C7.3%2C25.4%2C0l113.8-76.3c3.9-2.6%2C8.7-2.6%2C12.6%2C0l113.8%2C76.3c7.2%2C7.3%2C18.6%2C8.3%2C25.4%2C2C295.9%2C132%2C294.9%2C120.6%2C287%2C114.7z%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 0.7em top 50%, 0 0;
            background-size: 0.65em auto, 100%;
            cursor: pointer;
        }

        .action-buttons {
            display: flex;
            gap: 0.7rem;
            justify-content: center;
        }

        .action-buttons a {
            padding: 0.6rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .view {
            background: var(--primary-color);
            color: var(--light-color);
        }
        .view:hover {
            background: #004085;
            transform: translateY(-1px);
        }

        .print {
            background: var(--info-color); /* Using info color for a distinct print button */
            color: var(--light-color);
        }
        .print:hover {
            background: #117a8b; /* Darker shade of info on hover */
            transform: translateY(-1px);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.7rem;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            padding: 0.6rem 1.1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .pagination a {
            background: var(--light-color); /* White background for pagination links */
            color: var(--text-color); /* Dark text for contrast */
        }

        .pagination a:hover {
            background: var(--secondary-color); /* Lighter grey on hover */
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .pagination span.current {
            background: var(--primary-color);
            color: var(--light-color);
            border-color: var(--primary-color);
            cursor: default;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .notification {
            padding: 1.2rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            font-weight: 500;
            opacity: 1;
            transition: opacity 0.5s ease;
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .notification i {
            font-size: 1.5rem;
        }

        .error-notification {
            background: var(--danger-color);
            color: var(--light-color);
        }

        .success-notification {
            background: var(--success-color);
            color: var(--light-color);
        }

        /* Chart specific styles */
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        @media (max-width: 992px) {
            .filter-and-search {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box input {
                min-width: auto;
                flex: 1;
            }

            .data-table-container {
                padding: 1rem;
            }

            .data-table th,
            .data-table td {
                padding: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 1rem;
            }

            .section-header h2 {
                font-size: 1.8rem;
            }

            .filter {
                justify-content: center;
            }

            .action-buttons {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <main class="admin-container">
        <div id="notification-area">
            <?php if (!empty($errors)): ?>
                <div class="notification error-notification">
                    <i class="fas fa-times-circle"></i>
                    <div>
                        <?php foreach ($errors as $e): ?>
                            <p><?php echo htmlspecialchars($e); ?></p>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($success): ?>
                <div class="notification success-notification">
                    <i class="fas fa-check-circle"></i>
                    <p>Cập nhật trạng thái thành công!</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-header">
            <h2>Quản lý Hóa đơn nhập</h2>
        </div>

        <section class="filter-and-search">
            <div class="filter">
                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => '', 'page' => 1])); ?>"
                    class="filter-link <?php echo empty($status_filter) ? 'active' : ''; ?>">Tất cả</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'Pending', 'page' => 1])); ?>"
                    class="filter-link <?php echo $status_filter === 'Pending' ? 'active' : ''; ?>">Chờ xử lý</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'Processing', 'page' => 1])); ?>"
                    class="filter-link <?php echo $status_filter === 'Processing' ? 'active' : ''; ?>">Đang xử lý</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'Received', 'page' => 1])); ?>"
                    class="filter-link <?php echo $status_filter === 'Received' ? 'active' : ''; ?>">Đã nhận</a>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['status' => 'Cancelled', 'page' => 1])); ?>"
                    class="filter-link <?php echo $status_filter === 'Cancelled' ? 'active' : ''; ?>">Đã hủy</a>
            </div>
            <div class="search-box">
                <form method="GET" action="manage_purchase_orders.php">
                    <?php if ($status_filter): ?><input type="hidden" name="status"
                            value="<?php echo htmlspecialchars($status_filter); ?>"><?php endif; ?>
                    <?php if ($sort_by): ?><input type="hidden" name="sort_by"
                            value="<?php echo htmlspecialchars($sort_by); ?>"><?php endif; ?>
                    <?php if ($sort_order): ?><input type="hidden" name="sort_order"
                            value="<?php echo htmlspecialchars($sort_order); ?>"><?php endif; ?>
                    <input type="text" name="search" placeholder="Tìm theo ID, Nhà cung cấp, Người tạo..."
                        value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </section>

        <section class="data-table-container">
            <h3>Danh sách hóa đơn nhập</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th><a
                                href="?<?php echo http_build_query(array_merge($_GET, ['sort_by' => 'id', 'sort_order' => ($sort_by === 'id' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])); ?>">ID<?php if ($sort_by === 'id'): ?><i
                                        class="fas fa-sort<?php echo $sort_order === 'ASC' ? '-up' : '-down'; ?>"></i><?php endif; ?></a>
                        </th>
                        <th><a
                                href="?<?php echo http_build_query(array_merge($_GET, ['sort_by' => 'supplier_name', 'sort_order' => ($sort_by === 'supplier_name' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])); ?>">Nhà
                                cung cấp<?php if ($sort_by === 'supplier_name'): ?><i
                                        class="fas fa-sort<?php echo $sort_order === 'ASC' ? '-up' : '-down'; ?>"></i><?php endif; ?></a>
                        </th>
                        <th><a
                                href="?<?php echo http_build_query(array_merge($_GET, ['sort_by' => 'user_email', 'sort_order' => ($sort_by === 'user_email' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])); ?>">Người
                                tạo<?php if ($sort_by === 'user_email'): ?><i
                                        class="fas fa-sort<?php echo $sort_order === 'ASC' ? '-up' : '-down'; ?>"></i><?php endif; ?></a>
                        </th>
                        <th><a
                                href="?<?php echo http_build_query(array_merge($_GET, ['sort_by' => 'order_date', 'sort_order' => ($sort_by === 'order_date' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])); ?>">Ngày
                                tạo<?php if ($sort_by === 'order_date'): ?><i
                                        class="fas fa-sort<?php echo $sort_order === 'ASC' ? '-up' : '-down'; ?>"></i><?php endif; ?></a>
                        </th>
                        <th><a
                                href="?<?php echo http_build_query(array_merge($_GET, ['sort_by' => 'total_amount', 'sort_order' => ($sort_by === 'total_amount' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])); ?>">Tổng
                                tiền<?php if ($sort_by === 'total_amount'): ?><i
                                        class="fas fa-sort<?php echo $sort_order === 'ASC' ? '-up' : '-down'; ?>"></i><?php endif; ?></a>
                        </th>
                        <th><a
                                href="?<?php echo http_build_query(array_merge($_GET, ['sort_by' => 'status', 'sort_order' => ($sort_by === 'status' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])); ?>">Trạng
                                thái<?php if ($sort_by === 'status'): ?><i
                                        class="fas fa-sort<?php echo $sort_order === 'ASC' ? '-up' : '-down'; ?>"></i><?php endif; ?></a>
                        </th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($purchase_orders)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">Không tìm thấy hóa đơn nhập nào.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($purchase_orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['supplier_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['order_date'])); ?></td>
                                <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VNĐ</td>
                                <td>
                                    <form method="POST"
                                        action="manage_purchase_orders.php?<?php echo http_build_query($_GET); ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <select name="status" onchange="this.form.submit()" class="status-select">
                                            <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                            <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                            <option value="Received" <?php echo $order['status'] === 'Received' ? 'selected' : ''; ?>>Đã nhận</option>
                                            <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="action-buttons">
                                    <a href="view_purchase_order.php?id=<?php echo htmlspecialchars($order['id']); ?>"
                                        class="view"><i class="fas fa-eye"></i> Xem</a>
                                    <a href="print_purchase_invoice.php?id=<?php echo htmlspecialchars($order['id']); ?>"
                                        target="_blank" class="print"><i class="fas fa-print"></i> In</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($total_pages > 1): ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php
                        $query_params = array_merge($_GET, ['page' => $i]);
                        $link = '?' . http_build_query($query_params);
                        ?>
                        <?php if ($i == $page): ?>
                            <span class="current"><?php echo $i; ?></span>
                        <?php else: ?>
                            <a href="<?php echo $link; ?>"><?php echo $i; ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="data-table-container">
            <h3>Thống kê trạng thái hóa đơn nhập</h3>
            <div class="chart-container">
                <canvas id="purchaseOrderStatusChart"></canvas>
            </div>
        </section>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Notification handling
            const notificationArea = document.getElementById('notification-area');
            const successNotification = document.querySelector('.success-notification');
            if (successNotification) {
                setTimeout(() => {
                    successNotification.style.opacity = '0';
                    setTimeout(() => successNotification.remove(), 500);
                }, 3000);
            }

            // Sidebar toggle (assuming admin_header.php has sidebar elements)
            const sidebar = document.getElementById('adminSidebar'); // Assuming this ID exists in admin_header.php
            if (sidebar) {
                document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
                    button.addEventListener('click', () => {
                        sidebar.classList.toggle('collapsed');
                    });
                });
            }

            // Active sidebar link (assuming admin_header.php has nav links)
            const currentPath = window.location.pathname.split('/').pop();
            document.querySelectorAll('.sidebar nav a').forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref === currentPath || (currentPath === 'view_purchase_order.php' && linkHref === 'manage_purchase_orders.php')) {
                    link.classList.add('active');
                }
            });

            // Chart.js initialization
            const ctx = document.getElementById('purchaseOrderStatusChart').getContext('2d');
            const purchaseOrderStatusChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ["Chờ xử lý", "Đang xử lý", "Đã nhận", "Đã hủy"],
                    datasets: [{
                        label: "Số lượng hóa đơn nhập",
                        data: [<?php echo $status_counts['Pending']; ?>, <?php echo $status_counts['Processing']; ?>, <?php echo $status_counts['Received']; ?>, <?php echo $status_counts['Cancelled']; ?>],
                        backgroundColor: [
                            '#007bff', // Bootstrap Primary Blue for Pending
                            '#ffc107', // Bootstrap Warning Yellow for Processing
                            '#28a745', // Bootstrap Success Green for Received
                            '#dc3545'  // Bootstrap Danger Red for Cancelled
                        ],
                        borderColor: [
                            '#0056b3',
                            '#e0a800',
                            '#218838',
                            '#c82333'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "Số lượng",
                                color: 'var(--light-text-color)'
                            },
                            ticks: {
                                color: 'var(--light-text-color)',
                                precision: 0
                            },
                            grid: {
                                color: 'rgba(0,0,0,0.08)' /* Slightly darker grid lines */
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: "Trạng thái",
                                color: 'var(--light-text-color)'
                            },
                            ticks: {
                                color: 'var(--light-text-color)'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: "Số lượng hóa đơn nhập theo trạng thái",
                            color: 'var(--primary-color)', /* Use primary color for chart title */
                            font: {
                                size: 18,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.85)', /* Darker tooltip background */
                            bodyColor: '#fff',
                            titleColor: '#fff',
                            padding: 10,
                            displayColors: false
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>