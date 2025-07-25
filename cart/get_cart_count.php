<?php
session_start();
include('../config/db.php');

header('Content-Type: application/json');

$cart_count = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_id = ?");
    if (!$stmt) {
        echo json_encode(['error' => 'Lỗi truy vấn: ' . $conn->error]);
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $cart_count = $row['total_quantity'] !== null ? (int)$row['total_quantity'] : 0;
} else {
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += $item['quantity'];
        }
    }
}

echo json_encode(['cart_count' => $cart_count], JSON_UNESCAPED_UNICODE);
?>