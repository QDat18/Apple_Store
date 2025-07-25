<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$storage = isset($_POST['storage']) ? trim($_POST['storage']) : '';
$color = isset($_POST['color']) ? trim($_POST['color']) : '';

if ($product_id > 0 && $storage && $color) {
    $stmt = $conn->prepare("
        SELECT
            pv.id AS variant_id,
            pv.variant_price AS price,
            pv.stock_quantity AS stock
        FROM
            product_variants pv
        JOIN
            product_variant_attribute_links pval_storage ON pv.id = pval_storage.variant_id
        JOIN
            variant_attribute_values vav_storage ON pval_storage.attribute_value_id = vav_storage.id
        JOIN
            variant_attributes va_storage ON vav_storage.attribute_id = va_storage.id
        JOIN
            product_variant_attribute_links pval_color ON pv.id = pval_color.variant_id
        JOIN
            variant_attribute_values vav_color ON pval_color.attribute_value_id = vav_color.id
        JOIN
            variant_attributes va_color ON vav_color.attribute_id = va_color.id
        WHERE
            pv.product_id = ?
            AND va_storage.name = 'Dung lượng' AND vav_storage.value = ?
            AND va_color.name = 'Màu sắc' AND vav_color.value = ?
        LIMIT 1
    ");

    if (!$stmt) {
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("iss", $product_id, $storage, $color);
    $stmt->execute();
    $result = $stmt->get_result();
    $variant = $result->fetch_assoc();
    $stmt->close();

    if ($variant) {
        $price = $variant['price'];
        if (in_array($product_id, [1, 2])) {
            $price *= 0.9;
        }
        echo json_encode([
            'price' => $price,
            'stock' => $variant['stock'],
            'variant_id' => $variant['variant_id']
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['error' => 'Variant not found or out of stock.']);
    }
} else {
    echo json_encode(['error' => 'Invalid product_id, storage, or color.']);
}

$conn->close();
?>