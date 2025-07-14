<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$storage = isset($_POST['storage']) ? trim($_POST['storage']) : '';
$color = isset($_POST['color']) ? trim($_POST['color']) : '';

if ($product_id > 0 && $storage && $color) {
    $stmt = $conn->prepare("SELECT price FROM product_variants WHERE product_id = ? AND storage = ? AND color = ? LIMIT 1");
    $stmt->bind_param("iss", $product_id, $storage, $color);
    $stmt->execute();
    $result = $stmt->get_result();
    $variant = $result->fetch_assoc();
    $stmt->close();

    if ($variant) {
        $price = in_array($product_id, [1, 2]) ? $variant['price'] * 0.9 : $variant['price'];
        echo json_encode(['price' => $price], JSON_UNESCAPED_UNICODE);
    } else {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        $price = in_array($product_id, [1, 2]) ? $product['price'] * 0.9 : $product['price'];
        echo json_encode(['price' => $price], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(['error' => 'Invalid parameters'], JSON_UNESCAPED_UNICODE);
}
?>