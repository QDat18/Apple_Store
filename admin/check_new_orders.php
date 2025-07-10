<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$new_orders = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending' AND created_at > NOW() - INTERVAL 24 HOUR")->fetch_row()[0];
header('Content-Type: application/json');
echo json_encode(['new_orders' => $new_orders]);
?>