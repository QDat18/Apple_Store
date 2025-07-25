<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products_per_page = 8;

$category_map = [
    'iphone' => 1,
    'ipad' => 2,
    'mac' => 3,
    'watch' => 4,
    'airpod' => 5,
    'accessory' => 6
];

$products = [];
$offset = ($page - 1) * $products_per_page;

$query = "
    SELECT
        p.id AS product_id,
        p.product_name AS name,
        p.description,
        p.product_image AS image,
        p.category_id,
        MIN(pv.variant_price) AS min_price,
        SUM(pv.stock_quantity) AS total_stock_quantity
    FROM
        products p
    JOIN
        product_variants pv ON p.id = pv.product_id
    WHERE
        pv.status = 1 AND p.status = 1
";
$params = [];
$types = '';

if (!empty($category) && isset($category_map[$category])) {
    $query .= " AND p.category_id = ?";
    $params[] = $category_map[$category];
    $types .= 'i';
}

$query .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$params[] = $products_per_page;
$types .= 'ii';
$params[] = $offset;

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit();
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $product_id = $row['product_id'];

    $variant_options_query = "
        SELECT DISTINCT
            va.name AS attribute_name,
            vav.value AS attribute_value,
            vav.hex_code
        FROM
            product_variants pv
        JOIN
            product_variant_attribute_links pval ON pv.id = pval.variant_id
        JOIN
            variant_attribute_values vav ON pval.attribute_value_id = vav.id
        JOIN
            variant_attributes va ON vav.attribute_id = va.id
        WHERE
            pv.product_id = ? AND pv.status = 1
        ORDER BY va.name, vav.value
    ";
    $stmt_options = $conn->prepare($variant_options_query);
    if (!$stmt_options) {
        error_log('Prepare variant options query failed: ' . $conn->error);
        continue;
    }
    $stmt_options->bind_param("i", $product_id);
    $stmt_options->execute();
    $options_result = $stmt_options->get_result();

    $storage_options = [];
    $color_options = [];

    while ($opt_row = $options_result->fetch_assoc()) {
        if (strtolower($opt_row['attribute_name']) === 'dung lượng') {
            if (!in_array($opt_row['attribute_value'], $storage_options)) {
                $storage_options[] = $opt_row['attribute_value'];
            }
        } elseif (strtolower($opt_row['attribute_name']) === 'màu sắc') {
            $color_options_item = ['name' => $opt_row['attribute_value'], 'hex' => $opt_row['hex_code']];
            if (!in_array($color_options_item, $color_options)) {
                $color_options[] = $color_options_item;
            }
        }
    }
    $stmt_options->close();

    $default_variant_query = "
        SELECT
            pv.id AS variant_id,
            pv.variant_price AS price,
            pv.stock_quantity AS stock,
            pv.variant_image,
            GROUP_CONCAT(DISTINCT vav.value ORDER BY va.name ASC SEPARATOR ', ') AS attributes_display
        FROM
            product_variants pv
        LEFT JOIN
            product_variant_attribute_links pval ON pv.id = pval.variant_id
        LEFT JOIN
            variant_attribute_values vav ON pval.attribute_value_id = vav.id
        LEFT JOIN
            variant_attributes va ON vav.attribute_id = va.id
        WHERE
            pv.product_id = ? AND pv.status = 1
        GROUP BY pv.id
        ORDER BY pv.variant_price ASC, pv.stock_quantity DESC
        LIMIT 1
    ";
    $stmt_default_variant = $conn->prepare($default_variant_query);
    if (!$stmt_default_variant) {
        error_log('Prepare default variant query failed: ' . $conn->error);
        continue;
    }
    $stmt_default_variant->bind_param("i", $product_id);
    $stmt_default_variant->execute();
    $default_variant_result = $stmt_default_variant->get_result();
    $default_variant = $default_variant_result->fetch_assoc();
    $stmt_default_variant->close();

    $price = $default_variant['price'] ?? $row['min_price'];
    if (in_array($row['category_id'], [1, 2])) {
        $price *= 0.9;
    }

    $products[] = [
        'id' => $row['product_id'],
        'name' => $row['name'],
        'description' => $row['description'],
        'image' => $default_variant['variant_image'] ?? $row['image'],
        'price' => $price,
        'original_price' => $default_variant['price'] ?? $row['min_price'],
        'stock' => $default_variant['stock'] ?? $row['total_stock_quantity'],
        'category_id' => $row['category_id'],
        'variant_id' => $default_variant['variant_id'] ?? null,
        'promotions' => [
            'Giảm 5% khi thanh toán online',
            in_array($row['category_id'], [1, 2]) ? 'Tặng ốp lưng miễn phí' : ($row['category_id'] == 3 ? 'Tặng chuột Magic Mouse miễn phí' : 'Tặng dây đeo miễn phí'),
            'Bảo hành 12 tháng'
        ],
        'storage_options' => $storage_options,
        'color_options' => $color_options,
        'selected_storage' => $storage_options[0] ?? '',
        'selected_color' => $color_options[0]['name'] ?? ''
    ];
}

$count_query = "SELECT COUNT(DISTINCT p.id) as total FROM products p JOIN product_variants pv ON p.id = pv.product_id WHERE pv.status = 1 AND p.status = 1";
if (!empty($category) && isset($category_map[$category])) {
    $count_query .= " AND p.category_id = ?";
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param('i', $category_map[$category]);
} else {
    $stmt = $conn->prepare($count_query);
}
$stmt->execute();
$count_result = $stmt->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$stmt->close();

$total_pages = ceil($total_products / $products_per_page);

$conn->close();

echo json_encode([
    'products' => $products,
    'current_page' => $page,
    'total_pages' => $total_pages
], JSON_UNESCAPED_UNICODE);
?>