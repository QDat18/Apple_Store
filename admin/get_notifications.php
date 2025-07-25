<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied', 'count' => 0]);
    exit;
}

if (!$conn) {
    error_log("Database connection not available in get_notifications.php.");
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed.', 'count' => 0]);
    exit;
}

$notifications_to_display = [];
$total_new_notifications = 0;

try {
    // Function to insert notification into DB
    function insertNotification($conn, $title, $message, $type = 'info') {
        $stmt = $conn->prepare("INSERT INTO notifications (title, message, type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $message, $type);
        $stmt->execute();
        $stmt->close();
    }

    // Check new pending orders in the last 24 hours
    $result = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending' AND order_date > NOW() - INTERVAL 24 HOUR");
    $new_orders_count = $result->fetch_row()[0] ?? 0;

    if ($new_orders_count > 0) {
        $message = "Có {$new_orders_count} đơn hàng mới đang chờ xử lý.";
        insertNotification($conn, "Đơn hàng mới", $message, 'info');
        $notifications_to_display[] = $message;
        $total_new_notifications += $new_orders_count;
    }

    // Check new purchase orders (inventory) in the last 24 hours
    $table_exists = $conn->query("SHOW TABLES LIKE 'purchase_orders'")->num_rows > 0;
    if ($table_exists) {
        $result = $conn->query("SELECT COUNT(*) FROM purchase_orders WHERE status = 'Completed' AND order_date > NOW() - INTERVAL 24 HOUR");
        $new_inventory_count = $result->fetch_row()[0] ?? 0;

        if ($new_inventory_count > 0) {
            $message = "Có {$new_inventory_count} bản ghi nhập kho mới.";
            insertNotification($conn, "Nhập kho mới", $message, 'info');
            $notifications_to_display[] = $message;
            $total_new_notifications += $new_inventory_count;
        }
    } else {
        error_log("Table 'purchase_orders' does not exist in get_notifications.php.");
    }

    // Check new users in the last 24 hours
    $result = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer' AND created_at > NOW() - INTERVAL 24 HOUR");
    $new_users_count = $result->fetch_row()[0] ?? 0;

    if ($new_users_count > 0) {
        $message = "Có {$new_users_count} người dùng mới đăng ký.";
        insertNotification($conn, "Người dùng mới", $message, 'info');
        $notifications_to_display[] = $message;
        $total_new_notifications += $new_users_count;
    }

    // Get unread notifications count
    $result = $conn->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
    $unread_notifications_count = $result->fetch_row()[0] ?? 0;

    echo json_encode([
        'notifications' => $notifications_to_display,
        'count' => $unread_notifications_count
    ]);

} catch (Exception $e) {
    error_log("Error in get_notifications.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Có lỗi xảy ra khi lấy thông báo: ' . $e->getMessage(), 'count' => 0]);
}
?>