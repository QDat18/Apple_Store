<?php
session_start();
require_once '../config/db.php';

// Tạo CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Kiểm tra đăng nhập
$is_logged_in = isset($_SESSION['user_id']);

// Helper functions 
function buildFilterUrl($params = [])
{
    $current = $_GET;
    $new = array_merge($current, $params);
    $script_name = basename($_SERVER['PHP_SELF']);
    return $script_name . '?' . http_build_query($new);
}

function formatPrice($price)
{
    if (!is_numeric($price))
        return '0đ';
    return number_format($price, 0, ',', '.') . 'đ';
}

// Initial setup
$category_id = 6;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Categories mapping
$category_map = [
    1 => 'iPhone',
    2 => 'Mac',
    3 => 'iPad',
    4 => 'Apple Watch',
    5 => 'AirPods',
    6 => 'Phụ kiện'
];

// Define color options for filter checkboxes
$color_options = [
    'Starlight' => '#F0EAD6', // Often used for silver/light gold appearance
    'Midnight' => '#1D1D1F',  // Deep dark blue/black, replaces generic black
    'Silver' => '#E3E3E3',
    'Space Gray' => '#5C5C5C', // A common darker gray
    'Gold' => '#FAD7A1',      // A warmer gold
    'Rose Gold' => '#F7CAC9', // Specific rose gold tone
    'Pacific Blue' => '#2C4B70',
    'Sierra Blue' => '#9BB5CE',
    'Blue' => '#007AFF',      // A vibrant, generic Apple blue (iOS blue)
    'Alpine Green' => '#505F4E',
    'Green' => '#4CD964',     // A brighter, general Apple green (iOS green)
    '(PRODUCT)RED' => '#FF3B30', // Official PRODUCT(RED)
    'Purple' => '#5856D6',    // A common Apple purple (iOS purple)
    'Deep Purple' => '#4D2D5E', // Specific to some iPhone Pro models
    'Pink' => '#FFD3E0',      // A general Apple pink
    'Graphite' => '#5C5B57', // Darker gray, often for Pro models
    'Yellow' => '#FFCC00',
    'Orange' => '#FF9500',
];
// Define available storage options (if not dynamic from DB)
$storage_options = ['128GB', '256GB', '512GB', '1TB'];

// Define price ranges for filter checkboxes
$price_ranges = [
    '0-5000000' => 'Dưới 5.000.000đ',
    '5000000-10000000' => '5.000.000đ - 10.000.000đ',
    '10000000-15000000' => '10.000.000đ - 15.000.000đ',
    '15000000-20000000' => '15.000.000đ - 20.000.000đ',
    '20000000-25000000' => '20.000.000đ - 25.000.000đ',
    '25000000-30000000' => '25.000.000đ - 30.000.000đ',
    '30000000-max' => 'Trên 30.000.000đ',
];

// Get filters from URL
$filter_storage = isset($_GET['storage']) ? (array) $_GET['storage'] : [];
$filter_color = isset($_GET['color']) ? (array) $_GET['color'] : [];
$filter_sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_desc'; // Default sort by price desc
$selected_price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';

$filter_min_price = 0;
$filter_max_price = PHP_INT_MAX;

// Process price range filter first
if (!empty($selected_price_range)) {
    list($min_val, $max_val) = explode('-', $selected_price_range);
    $filter_min_price = (int) $min_val;
    if ($max_val !== 'max') {
        $filter_max_price = (int) $max_val;
    }
}


// Build SQL conditions
$where_conditions = [];
$params = [];
$types = '';

// Base conditions
$where_conditions[] = "p.status = 1";
$where_conditions[] = "(pv.status = 1 OR pv.status IS NULL)";

if ($category_id > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
}

// Handle search (if applicable, though not in the form yet)
if (!empty($search)) {
    $where_conditions[] = "p.product_name LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

// Now add price conditions based on processed filter_min_price and filter_max_price
if ($filter_min_price > 0) {
    $where_conditions[] = "pv.variant_price >= ?";
    $params[] = $filter_min_price;
    $types .= 'i';
}

if ($filter_max_price < PHP_INT_MAX) {
    $where_conditions[] = "pv.variant_price <= ?";
    $params[] = $filter_max_price;
    $types .= 'i';
}

// Handle storage filter
if (!empty($filter_storage)) {
    $placeholders = str_repeat('?,', count($filter_storage) - 1) . '?';
    $where_conditions[] = "EXISTS (
        SELECT 1 FROM product_variant_attribute_links pval
        JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
        WHERE pval.variant_id = pv.id
        AND vav.attribute_id = 2
        AND vav.value IN ($placeholders)
    )";
    foreach ($filter_storage as $storage) {
        $params[] = $storage;
        $types .= 's';
    }
}

// Handle color filter
if (!empty($filter_color)) {
    $placeholders = str_repeat('?,', count($filter_color) - 1) . '?';
    $where_conditions[] = "EXISTS (
        SELECT 1 FROM product_variant_attribute_links pval
        JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id 
        WHERE pval.variant_id = pv.id
        AND vav.attribute_id = 1
        AND vav.value IN ($placeholders)
    )";
    foreach ($filter_color as $color) {
        $params[] = $color;
        $types .= 's';
    }
}

// Build the complete WHERE clause
$where_clause = implode(" AND ", $where_conditions);

// Count total products
$count_sql = "SELECT COUNT(DISTINCT p.id) as total 
              FROM products p
              LEFT JOIN product_variants pv ON p.id = pv.product_id 
              WHERE $where_clause";

$stmt = $conn->prepare($count_sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_products = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Get products with all attributes
$products_sql = "SELECT p.*, 
                MIN(pv.variant_price) as min_price,
                MAX(pv.variant_price) as max_price,
                GROUP_CONCAT(DISTINCT vav_storage.value ORDER BY vav_storage.value ASC) as storage_values,
                GROUP_CONCAT(DISTINCT vav_color.value ORDER BY vav_color.value ASC) as color_values,
                GROUP_CONCAT(DISTINCT vav_color.hex_code ORDER BY vav_color.value ASC) as hex_code
                FROM products p
                LEFT JOIN product_variants pv ON p.id = pv.product_id
                LEFT JOIN product_variant_attribute_links pval_storage ON pval_storage.variant_id = pv.id
                LEFT JOIN variant_attribute_values vav_storage ON vav_storage.id = pval_storage.attribute_value_id AND vav_storage.attribute_id = 2
                LEFT JOIN product_variant_attribute_links pval_color ON pval_color.variant_id = pv.id 
                LEFT JOIN variant_attribute_values vav_color ON vav_color.id = pval_color.attribute_value_id AND vav_color.attribute_id = 1
                WHERE $where_clause
                GROUP BY p.id";

// Add sorting
switch ($filter_sort) {
    case 'price_asc':
        $products_sql .= " ORDER BY min_price ASC";
        break;
    case 'price_desc':
        $products_sql .= " ORDER BY min_price DESC";
        break;
    case 'name_asc':
        $products_sql .= " ORDER BY p.product_name ASC";
        break;
    case 'name_desc':
        $products_sql .= " ORDER BY p.product_name DESC";
        break;
    default:
        $products_sql .= " ORDER BY min_price DESC"; // Default sort
}

$products_sql .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($products_sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    // Format data
    $row['storage_values'] = array_filter(explode(',', $row['storage_values'] ?? ''));
    $row['color_values'] = array_filter(explode(',', $row['color_values'] ?? ''));
    $row['hex_code'] = array_filter(explode(',', $row['hex_code'] ?? ''));

    // Calculate promotional price
    $row['price'] = $row['min_price'];
    $row['original_price'] = $row['min_price'];
    if (in_array($row['category_id'], [1, 2])) {
        $row['price'] = $row['min_price'] * 0.9;
    }

    $products[] = $row;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phụ kiện Apple - Apple Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/footer.css">
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f5f5f7;
            color: #1d1d1f;
        }

        .main-content {
            display: flex;
            flex-direction: column;
        }

        /* Container */
        .products-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 2.5rem;
        }

        /* Sidebar Styles */
        .sidebar {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 2rem;
            height: fit-content;
        }

        .filter-section {
            margin-bottom: 2rem;
            border-bottom: 1px solid #e5e5e5;
            padding-bottom: 1.5rem;
        }

        .filter-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1d1d1f;
        }

        .filter-options {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .filter-options a {
            color: #424245;
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .filter-options a:hover,
        .filter-options a.active {
            background: #f5f5f7;
            color: #0066cc;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: #424245;
        }

        .checkbox-label input[type="checkbox"],
        .checkbox-label input[type="radio"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border: 1px solid #d2d2d7;
            border-radius: 4px;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            flex-shrink: 0;
            /* Prevent checkbox from shrinking */
        }

        .checkbox-label input[type="checkbox"]:checked,
        .checkbox-label input[type="radio"]:checked {
            background-color: #0066cc;
            border-color: #0066cc;
        }

        .checkbox-label input[type="checkbox"]:checked::before {
            content: "\f00c";
            /* FontAwesome check icon */
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            color: #fff;
            font-size: 12px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .checkbox-label input[type="radio"] {
            border-radius: 50%;
            /* Make radio buttons round */
        }

        .checkbox-label input[type="radio"]:checked::before {
            content: "";
            width: 10px;
            height: 10px;
            background-color: #fff;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .color-preview {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid #d2d2d7;
            box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            /* Prevent color dot from shrinking */
        }

        .price-inputs {
            /* This class is no longer used but kept for context if needed */
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .price-input {
            /* This class is no longer used but kept for context if needed */
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.2s ease;
        }

        .price-input:focus {
            /* This class is no longer used but kept for context if needed */
            outline: none;
            border-color: #0066cc;
        }

        .sort-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            font-size: 0.9rem;
            background: #fff;
            cursor: pointer;
        }

        .filter-apply {
            width: 100%;
            padding: 0.75rem;
            background: #0066cc;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .filter-apply:hover {
            background: #004499;
        }

        /* Active Filters Styles */
        .active-filters-container {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: none;
            /* Hidden by default, shown by JS if filters active */
        }

        .active-filters-container h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1d1d1f;
        }

        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .active-filter {
            background: #e5e5e5;
            padding: 0.6rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #1d1d1f;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remove-filter {
            cursor: pointer;
            color: #86868b;
            font-weight: 600;
            margin-left: 0.25rem;
            font-size: 1.1em;
            transition: color 0.2s ease;
        }

        .remove-filter:hover {
            color: #1d1d1f;
        }

        .clear-all-filters {
            display: none;
            /* Hidden by default, shown by JS if filters active */
            padding: 0.6rem 1rem;
            background: #f5f5f7;
            color: #0066cc;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .clear-all-filters:hover {
            background: #0066cc;
            color: #fff;
        }


        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }


        .product-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            width: 100%;
            height: 260px;
            object-fit: contain;
            background: #fff;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-info {
            padding: 1.5rem;
            background: linear-gradient(0deg, #fff 0%, rgba(255, 255, 255, 0.9) 100%);
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #1d1d1f;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-prices {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .product-price {
            color: #0066cc;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .original-price {
            color: #86868b;
            text-decoration: line-through;
            font-size: 1rem;
        }

        .product-options {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .option-badge {
            background: #f5f5f7;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.9rem;
            color: #424245;
        }

        color-options {
            /* This class name seems incorrect, likely meant to be .product-colors */
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .product-colors {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .color-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #d2d2d7;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .color-dot:hover {
            transform: scale(1.2);
        }

        .view-detail {
            display: block;
            text-align: center;
            padding: 0.75rem;
            background: #f5f5f7;
            color: #0066cc;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .view-detail:hover {
            background: #0066cc;
            color: #fff;
        }

        /* Pagination */
        .pagination {
            margin: 3rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }


        .page-link {
            padding: 0.75rem 1rem;
            border: 1px solid #d2d2d7;
            border-radius: 8px;
            color: #1d1d1f;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background: #f5f5f7;
            border-color: #0066cc;
            color: #0066cc;
        }

        .page-link.active {
            background: #0066cc;
            color: #fff;
            border-color: #0066cc;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .products-container {
                grid-template-columns: 240px 1fr;
                padding: 0 1rem;
            }
        }

        @media (max-width: 768px) {
            .products-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
                margin-bottom: 2rem;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }

        /* Loading Animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            /* Changed to none initially */
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading::after {
            content: "";
            width: 40px;
            height: 40px;
            border: 4px solid #f5f5f7;
            border-top: 4px solid #0066cc;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* No Products Message */
        .no-products {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            background: #fff;
            border-radius: 16px;
            color: #86868b;
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="loading"></div>
    <main class="products-container">
        <aside class="sidebar">
            <form action="" method="GET" id="filter-form">
                <input type="hidden" name="category_id" value="<?= $category_id ?>">

                <div class="filter-section">
                    <h3 class="filter-title">Dung lượng</h3>
                    <div class="checkbox-group">
                        <?php foreach ($storage_options as $storage): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="storage[]" value="<?= htmlspecialchars($storage) ?>"
                                    <?= in_array($storage, $filter_storage) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($storage) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title">Màu sắc</h3>
                    <div class="checkbox-group">
                        <?php foreach ($color_options as $color_name => $hex): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="color[]" value="<?= htmlspecialchars($color_name) ?>"
                                    <?= in_array($color_name, $filter_color) ? 'checked' : '' ?>>
                                <span class="color-preview" style="background-color: <?= htmlspecialchars($hex) ?>"></span>
                                <span><?= htmlspecialchars($color_name) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title">Khoảng giá</h3>
                    <div class="filter-options">
                        <?php foreach ($price_ranges as $range_value => $range_label): ?>
                            <label class="checkbox-label">
                                <input type="radio" name="price_range" value="<?= htmlspecialchars($range_value) ?>"
                                    <?= $selected_price_range === $range_value ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($range_label) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-section">
                    <h3 class="filter-title">Sắp xếp</h3>
                    <select name="sort" class="sort-select">
                        <option value="price_desc" <?= $filter_sort == 'price_desc' ? 'selected' : '' ?>>
                            Giá giảm dần
                        </option>
                        <option value="price_asc" <?= $filter_sort == 'price_asc' ? 'selected' : '' ?>>
                            Giá tăng dần
                        </option>
                        <option value="name_asc" <?= $filter_sort == 'name_asc' ? 'selected' : '' ?>>
                            Tên A-Z
                        </option>
                        <option value="name_desc" <?= $filter_sort == 'name_desc' ? 'selected' : '' ?>>
                            Tên Z-A
                        </option>
                    </select>
                </div>

                <button type="submit" class="filter-apply">Áp dụng</button>
            </form>
        </aside>

        <div class="main-content">
            <div class="active-filters-container">
                <h3>Bộ lọc hiện tại:</h3>
                <div class="active-filters">
                </div>
                <?php /* The clear all button is now managed by JavaScript */ ?>
                <a href="accessories.php" class="clear-all-filters">Xóa tất cả bộ lọc</a>
            </div>

            <div class="products-grid">
                <?php if (empty($products)): ?>
                    <p class="no-products">Không tìm thấy sản phẩm nào</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($product['product_image'] ?? '') ?>"
                                alt="<?= htmlspecialchars($product['product_name'] ?? '') ?>" class="product-image">

                            <div class="product-info">
                                <h3 class="product-name">
                                    <?= htmlspecialchars($product['product_name'] ?? '') ?>
                                </h3>

                                <div class="product-prices">
                                    <span class="product-price">
                                        <?= formatPrice($product['price'] ?? 0) ?>
                                    </span>
                                    <?php if (isset($product['price'], $product['original_price']) && $product['price'] < $product['original_price']): ?>
                                        <span class="original-price">
                                            <?= formatPrice($product['original_price']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($product['storage_values'])): ?>
                                    <div class="product-options">
                                        <?php foreach ($product['storage_values'] as $storage): ?>
                                            <span class="option-badge">
                                                <?= htmlspecialchars($storage ?? '') ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($product['color_values']) && !empty($product['hex_code'])): ?>
                                    <div class="product-colors">
                                        <?php
                                        // These values should already be arrays from the earlier processing
                                        // No need to explode or array_filter again here.
                                        $color_values = $product['color_values'];
                                        $hex_codes = $product['hex_code'];

                                        // Ensure both arrays have the same length before combining
                                        $length = min(count($color_values), count($hex_codes));

                                        // Use array_slice only if lengths are different to prevent errors with array_combine
                                        if (count($color_values) !== $length || count($hex_codes) !== $length) {
                                            $color_values = array_slice($color_values, 0, $length);
                                            $hex_codes = array_slice($hex_codes, 0, $length);
                                        }

                                        // Now safely combine arrays. If color names are unique, this is fine.
                                        $colors = [];
                                        if ($length > 0) {
                                            $colors = array_combine($color_values, $hex_codes);
                                        }

                                        foreach ($colors as $color_name => $hex_code): ?>
                                            <div class="color-dot" style="background-color: <?= htmlspecialchars($hex_code) ?>;"
                                                title="<?= htmlspecialchars($color_name) ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <a href="product_detail.php?id=<?= (int) ($product['id'] ?? 0) ?>" class="view-detail">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="<?= buildFilterUrl(['page' => $page - 1]) ?>" class="page-link">&laquo;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="<?= buildFilterUrl(['page' => $i]) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="<?= buildFilterUrl(['page' => $page + 1]) ?>" class="page-link">&raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
    <script src="../scripts/header.js"></script>

    <script>
        // PHP variables made available to JavaScript
        const categoryMapJs = <?= json_encode($category_map) ?>;
        const priceRangesJs = <?= json_encode($price_ranges) ?>; // Make price ranges available

        function formatPrice(number) {
            const num = parseFloat(number);
            if (isNaN(num)) return '0đ';
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }

        // Handle filter form submission
        document.querySelector('.sidebar form').addEventListener('submit', function (e) {
            e.preventDefault();
            document.querySelector('.loading').style.display = 'flex';

            const formData = new FormData(this);
            const params = new URLSearchParams();

            for (const pair of formData.entries()) {
                const [key, value] = pair;
                if (value) {
                    params.append(key, value);
                }
            }
            window.location.href = 'products.php?' + params.toString();
        });

        // Handle image error and lazy loading
        document.querySelectorAll('.product-image').forEach(img => {
            img.loading = 'lazy';
            img.onerror = function () {
                this.src = '../assets/products/default-product.png';
            }
        });

        // Smooth scroll to top for pagination links
        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(() => {
                    window.location.href = href;
                }, 500);
            });
        });

        // Automatically submit form on checkbox/select change
        document.querySelectorAll('input[type="checkbox"], input[type="radio"], select[name="sort"]').forEach(input => {
            input.addEventListener('change', () => {
                document.getElementById('filter-form').submit();
            });
        });

        // Show active filters and allow removal
        function updateActiveFilters() {
            const activeFiltersContainer = document.querySelector('.active-filters');
            const clearAllButton = document.querySelector('.clear-all-filters');
            const params = new URLSearchParams(window.location.search);
            const filters = [];
            let hasActiveFilters = false;

            // Category filter
            const currentCategoryId = params.get('category_id');
            // Only add category filter if it's not '0' (All products)
            if (currentCategoryId && currentCategoryId !== '0' && categoryMapJs[currentCategoryId]) {
                filters.push({
                    key: 'category_id',
                    value: currentCategoryId,
                    display: `Danh mục: ${categoryMapJs[currentCategoryId]}`
                });
                hasActiveFilters = true;
            }

            // Storage filter
            params.getAll('storage[]').forEach(storage => {
                filters.push({ key: 'storage[]', value: storage, display: `Dung lượng: ${storage}` });
                hasActiveFilters = true;
            });

            // Color filter
            params.getAll('color[]').forEach(color => {
                filters.push({ key: 'color[]', value: color, display: `Màu sắc: ${color}` });
                hasActiveFilters = true;
            });

            // Price range filter
            const selectedPriceRange = params.get('price_range');
            if (selectedPriceRange && priceRangesJs[selectedPriceRange]) {
                filters.push({
                    key: 'price_range',
                    value: selectedPriceRange,
                    display: `Giá: ${priceRangesJs[selectedPriceRange]}`
                });
                hasActiveFilters = true;
            }

            activeFiltersContainer.innerHTML = ''; // Clear existing filters

            if (filters.length > 0) {
                filters.forEach(filter => {
                    const filterSpan = document.createElement('span');
                    filterSpan.classList.add('active-filter');
                    filterSpan.innerHTML = `
                        ${filter.display}
                        <span class="remove-filter" data-key="${filter.key}" data-value="${filter.value}">&times;</span>
                    `;
                    activeFiltersContainer.appendChild(filterSpan);
                });

                // Add event listener for removing individual filters
                activeFiltersContainer.querySelectorAll('.remove-filter').forEach(removeBtn => {
                    removeBtn.addEventListener('click', function () {
                        const keyToRemove = this.dataset.key;
                        const valueToRemove = this.dataset.value;
                        const currentParams = new URLSearchParams(window.location.search);

                        if (keyToRemove === 'storage[]' || keyToRemove === 'color[]') {
                            const values = currentParams.getAll(keyToRemove);
                            const newValues = values.filter(val => val !== valueToRemove);
                            currentParams.delete(keyToRemove);
                            newValues.forEach(val => currentParams.append(keyToRemove, val));
                        } else if (keyToRemove === 'category_id') {
                            currentParams.set('category_id', '0'); // Reset category to 'Tất cả sản phẩm'
                        } else if (keyToRemove === 'price_range') {
                            currentParams.delete('price_range'); // Remove the single price_range parameter
                        } else {
                            currentParams.delete(keyToRemove);
                        }

                        window.location.href = 'accessories.php?' + currentParams.toString();
                    });
                });

                // Show the active filters container and clear all button
                document.querySelector('.active-filters-container').style.display = 'block';
                if (clearAllButton) {
                    clearAllButton.style.display = 'inline-block';
                }

            } else {
                // If no filters are active, hide the container
                document.querySelector('.active-filters-container').style.display = 'none';
                if (clearAllButton) {
                    clearAllButton.style.display = 'none';
                }
            }
        }

        // Call updateActiveFilters on page load
        document.addEventListener('DOMContentLoaded', updateActiveFilters);

        // Optional: If you want to show loading spinner on any link click (not just form submit)
        document.querySelectorAll('a').forEach(link => {
            if (link.href.includes('accessories.php')) {
                link.addEventListener('click', () => {
                    document.querySelector('.loading').style.display = 'flex';
                });
            }
        });
    </script>
</body>

</html>

<?php $conn->close(); ?>