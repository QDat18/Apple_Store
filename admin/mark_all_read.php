<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Error marking all notifications as read: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>