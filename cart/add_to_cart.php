<?php
session_start();
require_once '../config/db.php';

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$storage = isset($_POST['storage']) ? trim($_POST['storage']) : '';
$color = isset($_POST['color']) ? trim($_POST['color']) : '';

if ($product_id > 0 && $quantity > 0 && $storage && $color) {
    // Kiểm tra sản phẩm tồn tại và còn hàng
    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ? AND stock >= ?");
    $stmt->bind_param("ii", $product_id, $quantity);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    if ($product) {
        // Lưu vào session giỏ hàng
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $cart_item = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'storage' => $storage,
            'color' => $color
        ];
        $_SESSION['cart'][] = $cart_item;
        echo 'success';
    } else {
        echo 'error: Product out of stock or not found';
    }
} else {
    echo 'error: Invalid parameters';
}
?>