<?php
session_start();
include('../config/db.php');

header('Content-Type: application/json');

$response = ['status' => 'success', 'items' => []];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id) {
    $stmt = $conn->prepare("
        SELECT p.product_name AS name, pv.variant_price AS price, pv.stock_quantity, pv.variant_image AS image,
               vav_storage.value AS storage, vav_color.value AS color, c.quantity, p.category_id
        FROM cart c
        JOIN product_variants pv ON c.variant_id = pv.id
        JOIN products p ON pv.product_id = p.id
        LEFT JOIN product_variant_attribute_links pval_storage ON pv.id = pval_storage.variant_id
        LEFT JOIN variant_attribute_values vav_storage ON pval_storage.attribute_value_id = vav_storage.id
        LEFT JOIN variant_attributes va_storage ON vav_storage.attribute_id = va_storage.id AND va_storage.name = 'Dung lượng'
        LEFT JOIN product_variant_attribute_links pval_color ON pv.id = pval_color.variant_id
        LEFT JOIN variant_attribute_values vav_color ON pval_color.attribute_value_id = vav_color.id
        LEFT JOIN variant_attributes va_color ON vav_color.attribute_id = va_color.id AND va_color.name = 'Màu sắc'
        WHERE c.user_id = ?
    ");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn: ' . $conn->error], JSON_UNESCAPED_UNICODE);
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $price = in_array($row['category_id'], [1, 2]) ? $row['price'] * 0.9 : $row['price'];
        $response['items'][] = [
            'name' => $row['name'],
            'image' => $row['image'],
            'storage' => $row['storage'] ?? 'N/A',
            'color' => $row['color'] ?? 'N/A',
            'quantity' => $row['quantity'],
            'price' => number_format($price, 2)
        ];
    }
    $stmt->close();
} else {
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $variant_id => $item) {
            $stmt = $conn->prepare("
                SELECT p.product_name AS name, pv.variant_price AS price, pv.variant_image AS image,
                       vav_storage.value AS storage, vav_color.value AS color, p.category_id
                FROM product_variants pv
                JOIN products p ON pv.product_id = p.id
                LEFT JOIN product_variant_attribute_links pval_storage ON pv.id = pval_storage.variant_id
                LEFT JOIN variant_attribute_values vav_storage ON pval_storage.attribute_value_id = vav_storage.id
                LEFT JOIN variant_attributes va_storage ON vav_storage.attribute_id = va_storage.id AND va_storage.name = 'Dung lượng'
                LEFT JOIN product_variant_attribute_links pval_color ON pv.id = pval_color.variant_id
                LEFT JOIN variant_attribute_values vav_color ON pval_color.attribute_value_id = vav_color.id
                LEFT JOIN variant_attributes va_color ON vav_color.attribute_id = va_color.id AND va_color.name = 'Màu sắc'
                WHERE pv.id = ?
            ");
            if (!$stmt) {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi truy vấn: ' . $conn->error], JSON_UNESCAPED_UNICODE);
                exit();
            }
            $stmt->bind_param("i", $variant_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($row) {
                $price = in_array($row['category_id'], [1, 2]) ? $row['price'] * 0.9 : $row['price'];
                $response['items'][] = [
                    'name' => $row['name'],
                    'image' => $row['image'],
                    'storage' => $row['storage'] ?? 'N/A',
                    'color' => $row['color'] ?? 'N/A',
                    'quantity' => $item['quantity'],
                    'price' => number_format($price, 2)
                ];
            }
            $stmt->close();
        }
    }
}

$conn->close();
echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>