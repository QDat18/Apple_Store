<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

if (!$conn) {
    error_log("Database connection not available in check_new_orders.php.");
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

try {
    $result = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending' AND order_date > NOW() - INTERVAL 24 HOUR");
    $new_orders = $result->fetch_row()[0];
    header('Content-Type: application/json');
    echo json_encode(['new_orders' => $new_orders]);
} catch (Exception $e) {
    error_log("Error in check_new_orders.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>