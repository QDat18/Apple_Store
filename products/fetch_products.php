<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products_per_page = 8; // Number of products per page

// Map category names to IDs
$category_map = [
    'iphone' => 1,
    'ipad' => 2,
    'mac' => 3,
    'watch' => 4,
    'airpod' => 5,
    'accessory' => 6
];

// Reverse map for category_id to name
$category_id_to_name = array_flip($category_map);

$products = [];
$offset = ($page - 1) * $products_per_page;

// Build query
$query = "SELECT id, name, price, description, image, stock, category_id FROM products WHERE stock > 0";
$params = [];
$types = '';

if (!empty($category) && isset($category_map[$category])) {
    $query .= " AND category_id = ?";
    $params[] = $category_map[$category];
    $types .= 'i';
}

$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $products_per_page;
$params[] = $offset;
$types .= 'ii';

// Fetch products
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => 'Query preparation failed: ' . $conn->error]);
    exit;
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Query execution failed: ' . $stmt->error]);
    exit;
}
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Get total products for pagination
$count_query = "SELECT COUNT(*) as total FROM products WHERE stock > 0";
if (!empty($category) && isset($category_map[$category])) {
    $count_query .= " AND category_id = ?";
    $stmt = $conn->prepare($count_query);
    if (!$stmt) {
        echo json_encode(['error' => 'Count query preparation failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param('i', $category_map[$category]);
} else {
    $stmt = $conn->prepare($count_query);
    if (!$stmt) {
        echo json_encode(['error' => 'Count query preparation failed: ' . $conn->error]);
        exit;
    }
}
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Count query execution failed: ' . $stmt->error]);
    exit;
}
$count_result = $stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$stmt->close();

$total_pages = ceil($total_products / $products_per_page);

// Format products for JSON
$products_json = array_map(function ($product) use ($conn, $category_id_to_name) {
    $stmt = $conn->prepare("SELECT storage, color, price FROM product_variants WHERE product_id = ? LIMIT 1");
    if (!$stmt) {
        return ['error' => 'Variant query preparation failed: ' . $conn->error];
    }
    $stmt->bind_param("i", $product['id']);
    if (!$stmt->execute()) {
        return ['error' => 'Variant query execution failed: ' . $stmt->error];
    }
    $result = $stmt->get_result();
    $variant = $result->fetch_assoc();
    $stmt->close();

    $price = $variant ? $variant['price'] : $product['price'];
    $original_price = in_array($product['id'], [1, 2]) ? $price * 1.1 : $price;
    $category_name = isset($category_id_to_name[$product['category_id']]) ? $category_id_to_name[$product['category_id']] : 'unknown';

    return [
        'id' => $product['id'],
        'name' => htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'),
        'price' => number_format($price, 2),
        'original_price' => number_format($original_price, 2),
        'description' => htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'),
        'image' => '../assets/products/' . htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8'),
        'stock' => $product['stock'],
        'category' => htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8'),
        'specifications' => [
            'Chip' => in_array($category_name, ['iphone', 'ipad']) ? ($product['id'] == 1 ? 'A18' : 'A15') : ($category_name == 'mac' ? 'M1' : 'S8'),
            'Camera' => in_array($category_name, ['iphone', 'ipad']) ? '48MP' : ($category_name == 'mac' ? '720p' : 'N/A'),
            'Màn hình' => in_array($category_name, ['iphone']) ? '6.1 inch' : ($category_name == 'ipad' ? '10.2 inch' : ($category_name == 'mac' ? '13.3 inch' : '1.9 inch'))
        ],
        'promotions' => [
            'Giảm 5% khi thanh toán online',
            in_array($category_name, ['iphone', 'ipad']) ? 'Tặng ốp lưng miễn phí' : ($category_name == 'mac' ? 'Tặng chuột Magic Mouse miễn phí' : 'Tặng dây đeo miễn phí'),
            'Bảo hành 12 tháng'
        ],
        'storage_options' => in_array($category_name, ['watch', 'airpod']) ? ['N/A'] : ($category_name == 'mac' ? ['256GB', '512GB', '1TB'] : ['64GB', '128GB', '256GB']),
        'color_options' => in_array($category_name, ['watch']) ? [
            ['name' => 'Black', 'hex' => '#000000'],
            ['name' => 'Silver', 'hex' => '#C0C0C0'],
            ['name' => 'Starlight', 'hex' => '#F5F5F0']
        ] : ($category_name == 'mac' ? [
            ['name' => 'Space Gray', 'hex' => '#606060'],
            ['name' => 'Silver', 'hex' => '#C0C0C0'],
            ['name' => 'Gold', 'hex' => '#FFD700']
        ] : [
            ['name' => 'Black', 'hex' => '#000000'],
            ['name' => 'White', 'hex' => '#FFFFFF'],
            ['name' => 'Blue', 'hex' => '#007AFF']
        ])
    ];
}, $products);

// Check for errors in products_json
$errors = array_filter($products_json, function ($product) {
    return isset($product['error']);
});
if (!empty($errors)) {
    echo json_encode(['error' => $errors]);
    exit;
}

echo json_encode([
    'products' => $products_json,
    'total_pages' => $total_pages,
    'current_page' => $page
], JSON_UNESCAPED_UNICODE);