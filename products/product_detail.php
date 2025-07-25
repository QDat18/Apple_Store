<?php
session_start();
require_once '../config/db.php';
require '../includes/header.php';

// Kiểm tra đăng nhập để hiển thị nút "Thêm vào Wishlist"
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$category_map = [
    'IPhone Series' => 1,
    'IPad Series' => 3,
    'Macbook Series' => 2,
    'Apple Watch Series' => 4,
    'AirPods Series' => 5,
    'Phụ kiện' => 6
];
$category_id_to_name = array_flip($category_map);

$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT id, product_name AS name, description, product_image AS image, category_id FROM products WHERE id = ? AND status = 1");
    if (!$stmt) {
        error_log("Prepare product query failed: " . $conn->error);
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
        exit();
    }
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

$variants = [];
$storage_options = [];
$color_options = [];
if ($product) {
    $stmt = $conn->prepare("
        SELECT
            pv.id AS variant_id,
            pv.variant_price AS price,
            pv.stock_quantity AS stock,
            pv.variant_image,
            va.name AS attribute_name,
            vav.value AS attribute_value,
            vav.hex_code
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
        ORDER BY pv.id, va.name
    ");
    if (!$stmt) {
        error_log("Prepare variants query failed: " . $conn->error);
        http_response_code(500);
        echo json_encode(['error' => 'Internal server error']);
        exit();
    }
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $temp_variants = [];
    while ($row = $result->fetch_assoc()) {
        $variant_id = $row['variant_id'];
        if (!isset($temp_variants[$variant_id])) {
            $temp_variants[$variant_id] = [
                'variant_id' => $variant_id,
                'price' => $row['price'],
                'stock' => $row['stock'],
                'variant_image' => $row['variant_image'],
                'storage' => null,
                'color' => null,
                'color_hex' => null
            ];
        }
        $attr_name = $row['attribute_name'] !== null ? strtolower($row['attribute_name']) : '';
        if ($attr_name === 'dung lượng' || $attr_name === 'storage') {
            $temp_variants[$variant_id]['storage'] = $row['attribute_value'];
        }
        if ($attr_name === 'màu sắc' || $attr_name === 'color') {
            $temp_variants[$variant_id]['color'] = $row['attribute_value'];
            $temp_variants[$variant_id]['color_hex'] = $row['hex_code'];
        }
    }
    $variants = array_values($temp_variants);
    // Lấy storage_options và color_options từ variants
    $storage_options = array_values(array_unique(array_filter(array_column($variants, 'storage'))));
    $color_options = [];
    foreach ($variants as $v) {
        if ($v['color']) {
            $color_options[$v['color']] = $v['color_hex'];
        }
    }
    $color_options = array_map(function ($name, $hex) {
        return ['name' => $name, 'hex' => $hex];
    }, array_keys($color_options), $color_options);
    $stmt->close();
}

if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found or out of stock']);
    exit();
}

// Tạo CSRF token cho bảo mật
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Lấy danh sách ảnh gallery nếu có
$product_images = [];
if (!empty($product['product_images'])) {
    $imgs = json_decode($product['product_images'], true);
    if (is_array($imgs) && count($imgs) > 0)
        $product_images = $imgs;
}
if (empty($product_images) || !is_array($product_images)) {
    $product_images = [$product['image'] ?? 'default-product.png'];
}

// Lấy danh sách khuyến mãi (demo nếu chưa có bảng promotions)
$promotions = [
    'Tặng ốp lưng chính hãng trị giá 500.000đ',
    'Giảm thêm 5% khi thanh toán qua VNPAY',
    'Bảo hành 12 tháng chính hãng Apple',
];
// Nếu có bảng promotions, có thể lấy từ DB theo product_id
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name'] ?? '') ?> - Chi tiết sản phẩm</title>
    <link rel="stylesheet" href="../css/product.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #007AFF;
            /* Apple Blue */
            --secondary-color: #34C759;
            /* Apple Green */
            --accent-color: #FF9500;
            /* Apple Orange */
            --text-color: #1D1D1F;
            /* Dark Apple text */
            --light-gray: #F5F5F7;
            /* Lighter background */
            --medium-gray: #E8E8ED;
            /* Medium background */
            --border-color: #E5E5EA;
            /* Light border */
            --shadow-light: rgba(0, 0, 0, 0.08);
            /* Subtle shadow */
            --shadow-dark: rgba(0, 0, 0, 0.15);
            /* Stronger shadow */
            --red-color: #FF3B30;
            /* Apple Red (for discount/error) */
            --yellow-color: #FFCC00;
            /* Apple Yellow (for stars) */
        }

        body {
            font-family: 'SF Pro Display', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #F8F8F8;
            color: var(--text-color);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 1280px;
            margin: 24px auto 0 auto;
            padding: 0 20px;
        }

        .back-btn {
            background: var(--light-gray);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 1.05rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background 0.2s ease-in-out, transform 0.2s ease-in-out;
            font-weight: 500;
            color: var(--text-color);
        }

        .back-btn:hover {
            background: var(--medium-gray);
            transform: translateY(-2px);
        }

        .back-btn i {
            font-size: 1.1em;
        }

        .breadcrumb {
            font-size: 1rem;
            color: #8E8E93;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 15px;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb span {
            color: var(--text-color);
            font-weight: 600;
        }

        .product-detail-container {
            display: flex;
            gap: 50px;
            margin: 40px auto;
            max-width: 1280px;
            padding: 40px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 45px var(--shadow-light);
            align-items: flex-start;
        }

        .image-gallery {
            flex: 1;
            max-width: 580px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .main-image-container {
            position: relative;
            width: 100%;
            max-width: 550px;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        }

        .product-image-large {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease-in-out;
        }

        .product-image-large:hover {
            transform: scale(1.03);
        }

        .badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--red-color);
            color: #fff;
            padding: 8px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1.15rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            animation: bounceIn 0.6s forwards;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }

            70% {
                transform: scale(0.9);
            }

            100% {
                transform: scale(1);
            }
        }

        .zoom-btn,
        .view-360-btn {
            position: absolute;
            bottom: 20px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            color: var(--text-color);
            font-size: 1.2rem;
        }

        .zoom-btn {
            right: 20px;
        }

        .view-360-btn {
            left: 20px;
        }


        .zoom-btn:hover,
        .view-360-btn:hover {
            background: var(--medium-gray);
            transform: translateY(-3px) scale(1.05);
        }

        .thumbnails {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            max-width: 550px;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            border: 2px solid var(--border-color);
            transition: border 0.2s ease-in-out, transform 0.2s ease-in-out;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border: 2px solid var(--primary-color);
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 122, 255, 0.2);
        }

        .product-info {
            flex: 1;
            padding-left: 20px;
        }

        .product-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 20px;
        }

        .product-title {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--text-color);
            line-height: 1.2;
            flex: 1;
        }

        .share-btn {
            background: var(--light-gray);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 1.05rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s ease-in-out, transform 0.2s ease-in-out;
            font-weight: 500;
            color: var(--text-color);
        }

        .share-btn:hover {
            background: var(--medium-gray);
            transform: translateY(-2px);
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .rating-section span:first-child {
            color: var(--yellow-color);
            font-size: 1.6rem;
            letter-spacing: 2px;
        }

        .rating-section span:last-child {
            font-size: 1.1rem;
            color: #666;
            font-weight: 500;
        }

        .price-section {
            margin-bottom: 30px;
            padding-bottom: 25px;
            border-bottom: 1px dashed var(--border-color);
        }

        .price-main {
            font-size: 2.6rem;
            font-weight: 700;
            color: var(--red-color);
            display: flex;
            align-items: baseline;
            gap: 15px;
            margin-bottom: 8px;
        }

        .original-price {
            text-decoration: line-through;
            color: #8E8E93;
            font-size: 1.8rem;
            font-weight: 500;
        }

        .discount-info {
            margin-top: 5px;
            color: var(--accent-color);
            font-size: 1.25rem;
            font-weight: 600;
            display: block;
        }

        .stock-status {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .stock-info,
        .delivery-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        .stock-info i.fa-check-circle {
            color: var(--secondary-color);
            font-size: 1.2em;
        }

        .stock-info i.fa-times-circle {
            color: var(--red-color);
            font-size: 1.2em;
        }

        .delivery-info i {
            color: var(--primary-color);
            font-size: 1.2em;
        }

        .options-section {
            margin-bottom: 25px;
        }

        .option-group {
            margin-bottom: 20px;
        }

        .option-label {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-color);
        }

        .option-label i {
            color: var(--primary-color);
            font-size: 1.1em;
        }

        .option-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .option-btn {
            padding: 12px 22px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: var(--light-gray);
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .option-btn:hover {
            border-color: var(--primary-color);
            background: var(--medium-gray);
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 122, 255, 0.15);
        }

        .option-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            transform: translateY(-2px);
        }

        .color-option-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 3px solid var(--border-color);
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .color-option-btn:hover {
            transform: scale(1.1);
        }

        .color-option-btn.active {
            border: 4px solid var(--primary-color);
            box-shadow: 0 0 0 4px var(--medium-gray), 0 4px 12px rgba(0, 122, 255, 0.2);
            transform: scale(1.05);
        }

        .color-option-btn::after {
            content: attr(title);
            position: absolute;
            left: 50%;
            top: 115%;
            transform: translateX(-50%);
            background: #333;
            color: #fff;
            font-size: 0.85em;
            padding: 4px 10px;
            border-radius: 8px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease-in-out;
            z-index: 10;
        }

        .color-option-btn:hover::after {
            opacity: 1;
        }

        /* To make white colors have clearer borders */
        .color-option-btn[style*="#fff"],
        .color-option-btn[style*="#ffffff"],
        .color-option-btn[style*="#F0EAD6"] {
            box-shadow: 0 0 0 2px #ddd, 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .color-option-btn[style*="#fff"].active,
        .color-option-btn[style*="#ffffff"].active,
        .color-option-btn[style*="#F0EAD6"].active {
            box-shadow: 0 0 0 2px #ddd, 0 0 0 4px var(--medium-gray), 0 4px 12px rgba(0, 122, 255, 0.2);
        }

        .color-variant-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .color-variant-image.active {
            opacity: 1;
        }

        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
        }

        .qty-btn {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            background: var(--light-gray);
            font-size: 1.4rem;
            cursor: pointer;
            transition: background 0.2s ease-in-out, transform 0.2s ease-in-out;
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qty-btn:hover {
            background: var(--medium-gray);
            transform: translateY(-2px);
        }

        .qty-input {
            width: 70px;
            text-align: center;
            font-size: 1.25rem;
            border-radius: 10px;
            border: 1.5px solid var(--border-color);
            padding: 10px 0;
            color: var(--text-color);
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .add-to-cart-btn,
        .buy-now-btn,
        .add-to-wishlist-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 18px 35px;
            font-size: 1.35rem;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            display: flex;
            align-items: center;
            gap: 15px;
            font-weight: 600;
            width: 100%;
            justify-content: center;
            box-shadow: 0 6px 20px rgba(0, 122, 255, 0.25);
        }

        .add-to-cart-btn:hover,
        .buy-now-btn:hover,
        .add-to-wishlist-btn:hover {
            background: #0066CC;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 122, 255, 0.35);
        }

        .add-to-cart-btn:disabled,
        .buy-now-btn:disabled,
        .add-to-wishlist-btn:disabled {
            background: #C7C7CC;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .buy-now-btn {
            background: var(--accent-color);
            box-shadow: 0 6px 20px rgba(255, 149, 0, 0.25);
        }

        .buy-now-btn:hover {
            background: #E08C00;
            box-shadow: 0 8px 25px rgba(255, 149, 0, 0.35);
        }

        .promotions {
            margin-top: 35px;
            padding-top: 30px;
            border-top: 1px dashed var(--border-color);
        }

        .promotions h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-color);
        }

        .promotions h3 i {
            color: var(--accent-color);
            font-size: 1.1em;
        }

        .promotion-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .promotion-list li {
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: #444;
            line-height: 1.5;
            font-weight: 500;
        }

        .promotion-list li i {
            color: var(--secondary-color);
            margin-top: 4px;
            font-size: 1.15em;
            flex-shrink: 0;
        }

        /* Product Tabs Styling */
        .product-tabs {
            width: 100%;
            margin: 50px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 45px var(--shadow-light);
            overflow: hidden;
        }

        .product-tabs .tab-nav {
            display: flex;
            border-bottom: 2px solid var(--border-color);
            background: var(--light-gray);
            border-radius: 20px 20px 0 0;
        }

        .product-tabs .tab-btn {
            flex: 1;
            padding: 22px 0;
            font-size: 1.25rem;
            font-weight: 700;
            border: none;
            background: transparent;
            color: #8E8E93;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            outline: none;
            position: relative;
            text-align: center;
            border-radius: 20px 20px 0 0;
        }

        .product-tabs .tab-btn.active {
            background: #fff;
            color: var(--primary-color);
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
            border-bottom: 3px solid var(--primary-color);
            z-index: 2;
        }

        .product-tabs .tab-btn:not(.active):hover {
            background: var(--medium-gray);
            color: var(--primary-color);
        }

        .product-tabs .tab-content {
            background: #fff;
            border-radius: 0 0 20px 20px;
            padding: 40px;
            min-height: 350px;
            width: 100%;
            box-sizing: border-box;
        }

        .product-tabs .tab-pane {
            display: none;
            animation: fadeIn 0.4s ease-out forwards;
            width: 100%;
        }

        .product-tabs .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Description table styling */
        .description-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 20px;
            font-size: 1.1rem;
            color: #333;
        }

        .description-table th,
        .description-table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .description-table th {
            background-color: var(--light-gray);
            font-weight: 600;
            color: var(--text-color);
            width: 30%;
        }

        .description-table tr:last-child th,
        .description-table tr:last-child td {
            border-bottom: none;
        }

        .description-table tbody tr:nth-child(even) {
            background-color: #FDFDFD;
        }

        .description-table tbody tr:hover {
            background-color: var(--medium-gray);
            transition: background-color 0.2s ease-in-out;
        }

        /* Review Section Styling */
        .reviews-section {
            padding: 20px 0;
        }

        .reviews-summary {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .overall-rating {
            font-size: 3.5rem;
            font-weight: 700;
            color: var(--text-color);
            line-height: 1;
        }

        .overall-stars {
            color: var(--yellow-color);
            font-size: 1.8rem;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }

        .total-reviews {
            font-size: 1.1rem;
            color: #666;
        }

        .rating-breakdown {
            flex-grow: 1;
        }

        .rating-bar-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .rating-bar-group span {
            font-size: 1rem;
            font-weight: 500;
            color: #555;
            width: 20px; /* For star count */
        }

        .rating-bar-group .bar-container {
            flex-grow: 1;
            height: 10px;
            background: var(--light-gray);
            border-radius: 5px;
            overflow: hidden;
        }

        .rating-bar-group .bar-fill {
            height: 100%;
            background: var(--primary-color);
            border-radius: 5px;
        }

        .review-list {
            margin-top: 30px;
        }

        .review-item {
            background: var(--light-gray);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .reviewer-info {
            font-weight: 600;
            color: var(--text-color);
            font-size: 1.1rem;
        }

        .review-date {
            font-size: 0.95rem;
            color: #888;
        }

        .review-stars {
            color: var(--yellow-color);
            font-size: 1.1rem;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }

        .review-content {
            font-size: 1.05rem;
            color: #333;
            line-height: 1.6;
        }

        .write-review-btn {
            background: var(--secondary-color);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            font-size: 1.15rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
            margin-top: 20px;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(52, 199, 89, 0.2);
        }

        .write-review-btn:hover {
            background: #28a745;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(52, 199, 89, 0.3);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            align-items: center;
            justify-content: center;
            animation: fadeInBg 0.3s forwards;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            animation: slideIn 0.3s forwards;
        }

        @keyframes fadeInBg {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }


        .close-button {
            color: #aaa;
            float: right;
            font-size: 30px;
            font-weight: bold;
            position: absolute;
            top: 15px;
            right: 25px;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: #333;
            text-decoration: none;
        }

        .modal-content h2 {
            margin-top: 0;
            color: var(--primary-color);
            font-size: 2rem;
            margin-bottom: 25px;
            text-align: center;
        }

        .modal-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 10px;
        }

        /* Zoom modal specific styles */
        #zoom-modal .modal-content {
            max-width: 900px;
            background: transparent;
            box-shadow: none;
            padding: 0;
        }

        #zoom-modal img {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        #zoom-modal .close-button {
            color: #fff;
            text-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
        }


        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .product-detail-container {
                flex-direction: column;
                gap: 30px;
                padding: 30px;
            }

            .image-gallery,
            .product-info {
                max-width: 100%;
                padding: 0;
            }

            .product-title {
                font-size: 2.5rem;
            }

            .price-main {
                font-size: 2.2rem;
            }

            .original-price {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }

            .product-detail-container {
                padding: 20px;
                margin: 20px auto;
            }

            .product-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .product-title {
                font-size: 2rem;
            }

            .share-btn {
                width: 100%;
                justify-content: center;
            }

            .stock-status {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .product-tabs .tab-nav {
                flex-direction: column;
                border-bottom: none;
            }

            .product-tabs .tab-btn {
                border-bottom: 1px solid var(--border-color);
                border-radius: 0;
            }

            .product-tabs .tab-btn.active {
                box-shadow: none;
                border-bottom: 3px solid var(--primary-color);
            }

            .product-tabs .tab-content {
                padding: 25px;
            }

            .description-table th,
            .description-table td {
                padding: 12px;
                font-size: 0.95rem;
            }

            .reviews-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .overall-rating {
                font-size: 3rem;
            }

            .overall-stars {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .product-detail-container {
                border-radius: 10px;
            }

            .main-image-container {
                border-radius: 10px;
            }

            .badge {
                padding: 6px 12px;
                font-size: 1rem;
            }

            .zoom-btn,
            .view-360-btn {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }

            .thumbnails {
                gap: 8px;
            }

            .thumbnail {
                width: 60px;
                height: 60px;
            }

            .product-title {
                font-size: 1.8rem;
            }

            .price-main {
                font-size: 2rem;
            }

            .original-price {
                font-size: 1.4rem;
            }

            .option-btn {
                padding: 10px 18px;
                font-size: 1rem;
            }

            .color-option-btn {
                width: 40px;
                height: 40px;
            }

            .qty-btn {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }

            .qty-input {
                width: 60px;
                font-size: 1.1rem;
            }

            .add-to-cart-btn,
            .buy-now-btn,
            .add-to-wishlist-btn {
                padding: 15px 25px;
                font-size: 1.1rem;
            }

            .promotions h3 {
                font-size: 1.15rem;
            }

            .promotion-list li {
                font-size: 1rem;
            }

            .product-tabs .tab-btn {
                font-size: 1.1rem;
                padding: 18px 0;
            }

            .product-tabs .tab-content {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <?php include '../components/header.php'; // Điều hướng và header chung ?>

    <div class="container">
        <div class="breadcrumb">
            <a href="/Apple_Shop/products.php"><i class="fas fa-home"></i> Trang chủ</a>
            <i class="fas fa-chevron-right"></i>
            <a href="/Apple_Shop/products.php?category_id=<?= htmlspecialchars($product['category_id']) ?>">
                <?= htmlspecialchars($category_id_to_name[$product['category_id']] ?? 'Sản phẩm') ?>
            </a>
            <i class="fas fa-chevron-right"></i>
            <span><?= htmlspecialchars($product['name'] ?? 'Sản phẩm không tồn tại') ?></span>
        </div>

        <?php if (!$product): ?>
            <div class="product-not-found">
                <h1>Sản phẩm không tìm thấy</h1>
                <p>Xin lỗi, sản phẩm bạn đang tìm kiếm không tồn tại hoặc đã bị gỡ bỏ.</p>
                <a href="/Apple_Shop/products.php" class="back-btn"><i class="fas fa-arrow-left"></i> Quay lại trang
                    sản phẩm</a>
            </div>
        <?php else: ?>
            <div class="product-detail-container">
                <div class="image-gallery">
                    <div class="main-image-container">
                        <img src="<?= htmlspecialchars('/Apple_Shop/' . ($product['image'] ?? 'default-product.png')) ?>"
                            alt="<?= htmlspecialchars($product['name'] ?? '') ?>" class="product-image-large" id="main-product-image">
                        <?php if (isset($product['badge_text'])): // Thêm điều kiện nếu có badge_text ?>
                            <span class="badge"><?= htmlspecialchars($product['badge_text']) ?></span>
                        <?php endif; ?>
                        <button class="zoom-btn" id="zoom-main-image"><i class="fas fa-search-plus"></i></button>
                        </div>
                    <?php if (!empty($product_images) && count($product_images) > 1): ?>
                        <div class="thumbnails">
                            <?php foreach ($product_images as $index => $img): ?>
                                <img src="<?= htmlspecialchars('/Apple_Shop/' . $img) ?>"
                                    alt="Thumbnail <?= $index + 1 ?>" class="thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                    data-image="<?= htmlspecialchars('/Apple_Shop/' . $img) ?>">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <div class="product-header">
                        <h1 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?></h1>
                        <button class="share-btn"><i class="fas fa-share-alt"></i> Chia sẻ</button>
                    </div>

                    <div class="rating-section">
                        <span class="stars">★★★★★</span>
                        <span>(123 đánh giá)</span>
                    </div>

                    <div class="price-section">
                        <div class="price-main">
                            <span id="display-price"><?= number_format($variants[0]['price'] ?? 0, 0, ',', '.') ?> VNĐ</span>
                            <?php if (isset($product['original_price'])): ?>
                                <span class="original-price"
                                    id="display-original-price"><?= number_format($product['original_price'], 0, ',', '.') ?>
                                    VNĐ</span>
                            <?php endif; ?>
                        </div>
                        <?php if (isset($product['discount_percent'])): ?>
                            <span class="discount-info">Tiết kiệm <?= htmlspecialchars($product['discount_percent']) ?>%</span>
                        <?php endif; ?>
                        <div class="stock-status">
                            <span class="stock-info" id="stock-status-display">
                                <i class="fas fa-check-circle"></i> Còn hàng
                            </span>
                            <span class="delivery-info">
                                <i class="fas fa-shipping-fast"></i> Giao hàng toàn quốc
                            </span>
                        </div>
                    </div>

                    <div class="options-section">
                        <?php if (!empty($storage_options)): ?>
                            <div class="option-group">
                                <label class="option-label"><i class="fas fa-hdd"></i> Dung lượng:</label>
                                <div class="option-buttons" id="storage-options">
                                    <?php foreach ($storage_options as $storage): ?>
                                        <button class="option-btn" data-storage="<?= htmlspecialchars($storage) ?>">
                                            <?= htmlspecialchars($storage) ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($color_options)): ?>
                            <div class="option-group">
                                <label class="option-label"><i class="fas fa-palette"></i> Màu sắc:</label>
                                <div class="option-buttons" id="color-options">
                                    <?php foreach ($color_options as $color): ?>
                                        <button class="color-option-btn" data-color="<?= htmlspecialchars($color['name']) ?>"
                                            data-hex="<?= htmlspecialchars($color['hex'] ?? '') ?>"
                                            title="<?= htmlspecialchars($color['name']) ?>"
                                            style="background-color: <?= htmlspecialchars($color['hex'] ?? '') ?>;"></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="quantity-selector">
                        <label class="option-label" style="margin-bottom: 0;"><i class="fas fa-sort-numeric-up"></i>
                            Số lượng:</label>
                        <button class="qty-btn" id="qty-minus">-</button>
                        <input type="text" id="qty-input" class="qty-input" value="1" min="1" readonly>
                        <button class="qty-btn" id="qty-plus">+</button>
                    </div>

                    <div class="action-buttons">
                        <button class="add-to-cart-btn" id="add-to-cart-btn" disabled><i class="fas fa-cart-plus"></i> Thêm vào
                            giỏ hàng</button>
                        <button class="buy-now-btn" id="buy-now-btn" disabled><i class="fas fa-money-bill-wave"></i> Mua ngay</button>
                        <?php if ($is_logged_in): ?>
                            <button class="add-to-wishlist-btn" id="add-to-wishlist-btn" disabled><i class="fas fa-heart"></i> Thêm vào
                                yêu thích</button>
                        <?php endif; ?>
                    </div>

                    <div class="promotions">
                        <h3><i class="fas fa-gift"></i> Ưu đãi & Khuyến mãi</h3>
                        <ul class="promotion-list">
                            <?php foreach ($promotions as $promo): ?>
                                <li><i class="fa-solid fa-check fa-spin fa-spin-reverse"></i><?= htmlspecialchars($promo) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="product-tabs container">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="description">Mô tả sản phẩm</button>
                    <button class="tab-btn" data-tab="reviews">Đánh giá (123)</button>
                    <button class="tab-btn" data-tab="specs">Thông số kỹ thuật</button>
                </div>

                <div class="tab-content">
                    <div id="description" class="tab-pane active">
                        <h3>Giới thiệu về sản phẩm</h3>
                        <p><?= nl2br(htmlspecialchars($product['description'] ?? 'Đang cập nhật...')) ?></p>
                    </div>

                    <div id="reviews" class="tab-pane">
                        <div class="reviews-section">
                            <div class="reviews-summary">
                                <div>
                                    <div class="overall-rating">4.5</div>
                                    <div class="overall-stars">★★★★★</div>
                                    <div class="total-reviews">(123 đánh giá)</div>
                                </div>
                                <div class="rating-breakdown">
                                    <div class="rating-bar-group">
                                        <span>5★</span>
                                        <div class="bar-container">
                                            <div class="bar-fill" style="width: 80%;"></div>
                                        </div>
                                    </div>
                                    <div class="rating-bar-group">
                                        <span>4★</span>
                                        <div class="bar-container">
                                            <div class="bar-fill" style="width: 15%;"></div>
                                        </div>
                                    </div>
                                    <div class="rating-bar-group">
                                        <span>3★</span>
                                        <div class="bar-container">
                                            <div class="bar-fill" style="width: 3%;"></div>
                                        </div>
                                    </div>
                                    <div class="rating-bar-group">
                                        <span>2★</span>
                                        <div class="bar-container">
                                            <div class="bar-fill" style="width: 1%;"></div>
                                        </div>
                                    </div>
                                    <div class="rating-bar-group">
                                        <span>1★</span>
                                        <div class="bar-container">
                                            <div class="bar-fill" style="width: 1%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="write-review-btn">Viết đánh giá của bạn</button>
                            <div class="review-list">
                                <div class="review-item">
                                    <div class="review-header">
                                        <span class="reviewer-info">Nguyễn Văn A</span>
                                        <span class="review-date">20/07/2024</span>
                                    </div>
                                    <div class="review-stars">★★★★★</div>
                                    <p class="review-content">Sản phẩm rất tốt, đúng mô tả. Giao hàng nhanh, đóng gói cẩn
                                        thận. Rất hài lòng!</p>
                                </div>
                                <div class="review-item">
                                    <div class="review-header">
                                        <span class="reviewer-info">Trần Thị B</span>
                                        <span class="review-date">18/07/2024</span>
                                    </div>
                                    <div class="review-stars">★★★★☆</div>
                                    <p class="review-content">Máy dùng ổn định, pin trâu. Chỉ có điều màu sắc hơi khác so
                                        với hình ảnh một chút.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="specs" class="tab-pane">
                        <h3>Thông số kỹ thuật chi tiết</h3>
                        <table class="description-table">
                            <tbody>
                                <tr>
                                    <th>Màn hình</th>
                                    <td>Super Retina XDR OLED, 6.7 inch, 120Hz</td>
                                </tr>
                                <tr>
                                    <th>Chip</th>
                                    <td>Apple A17 Pro Bionic</td>
                                </tr>
                                <tr>
                                    <th>RAM</th>
                                    <td>8GB</td>
                                </tr>
                                <tr>
                                    <th>Bộ nhớ trong</th>
                                    <td>256GB / 512GB / 1TB</td>
                                </tr>
                                <tr>
                                    <th>Camera sau</th>
                                    <td>Chính 48MP, Siêu rộng 12MP, Tele 12MP</td>
                                </tr>
                                <tr>
                                    <th>Camera trước</th>
                                    <td>12MP</td>
                                </tr>
                                <tr>
                                    <th>Pin</th>
                                    <td>Li-Ion (Hỗ trợ sạc nhanh 27W)</td>
                                </tr>
                                <tr>
                                    <th>Hệ điều hành</th>
                                    <td>iOS 17</td>
                                </tr>
                                <tr>
                                    <th>Kết nối</th>
                                    <td>5G, Wi-Fi 6E, Bluetooth 5.3, USB-C</td>
                                </tr>
                                <tr>
                                    <th>Tính năng khác</th>
                                    <td>Face ID, Chống nước IP68, Dynamic Island</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php
        // PHP cho phần sản phẩm liên quan
        $related_products = [];
        if ($product && isset($product['category_id']) && isset($product['id'])) {
            $current_category_id = $product['category_id'];
            $current_product_id = $product['id'];

            // Hàm định dạng giá, nếu chưa có
            if (!function_exists('formatPrice')) {
                function formatPrice($price) {
                    if (!is_numeric($price)) return '0đ';
                    return number_format($price, 0, ',', '.') . ' VNĐ';
                }
            }

            $stmt = $conn->prepare("
                SELECT id, product_name AS name, product_image AS image, price
                FROM products
                WHERE category_id = ? AND id != ? AND status = 1
                ORDER BY RAND()
                LIMIT 8
            ");

            if ($stmt) {
                $stmt->bind_param("ii", $current_category_id, $current_product_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $row['price_formatted'] = formatPrice($row['price']);
                    $row['image_path'] = htmlspecialchars('/Apple_Shop/' . $row['image']);
                    $related_products[] = $row;
                }
                $stmt->close();
            } else {
                error_log("Prepare related products query failed: " . $conn->error);
            }
        }
        ?>

        <section class="related-products container">
            <h2>Sản phẩm liên quan</h2>
            <?php if (!empty($related_products)): ?>
                <div class="product-grid-horizontal">
                    <?php foreach ($related_products as $rp): ?>
                        <div class="product-item-horizontal">
                            <a href="/Apple_Shop/products/product_detail.php?id=<?= htmlspecialchars($rp['id']) ?>">
                                <img src="<?= $rp['image_path'] ?>" alt="<?= htmlspecialchars($rp['name']) ?>" class="product-image-horizontal">
                            </a>
                            <h3><a href="/Apple_Shop/products/product_detail.php?id=<?= htmlspecialchars($rp['id']) ?>"><?= htmlspecialchars($rp['name']) ?></a></h3>
                            <p class="price"><?= $rp['price_formatted'] ?></p>
                            <a href="/Apple_Shop/products/product_detail.php?id=<?= htmlspecialchars($rp['id']) ?>" class="view-detail-btn-horizontal">Xem chi tiết</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #666;">Không có sản phẩm liên quan nào được tìm thấy.</p>
            <?php endif; ?>
        </section>

    </div>

    <div id="zoom-modal" class="modal">
        <span class="close-button">&times;</span>
        <div class="modal-content">
            <img id="modal-image" src="" alt="Zoomed Product Image">
        </div>
    </div>


    <?php include '../includes/footer.php'; ?>
    <script src="../scripts/header.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mainProductImage = document.getElementById('main-product-image');
            const thumbnails = document.querySelectorAll('.thumbnail');
            const zoomModal = document.getElementById('zoom-modal');
            const modalImage = document.getElementById('modal-image');
            const closeButton = zoomModal.querySelector('.close-button');
            const zoomButton = document.getElementById('zoom-main-image');

            // Image gallery functionality
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function () {
                    mainProductImage.src = this.dataset.image;
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Zoom functionality
            zoomButton.addEventListener('click', function () {
                modalImage.src = mainProductImage.src;
                zoomModal.classList.add('show');
            });

            closeButton.addEventListener('click', function () {
                zoomModal.classList.remove('show');
            });

            zoomModal.addEventListener('click', function (e) {
                if (e.target === zoomModal) {
                    zoomModal.classList.remove('show');
                }
            });

            // Quantity selector
            const qtyInput = document.getElementById('qty-input');
            const qtyMinusBtn = document.getElementById('qty-minus');
            const qtyPlusBtn = document.getElementById('qty-plus');

            qtyMinusBtn.addEventListener('click', function () {
                let currentQty = parseInt(qtyInput.value);
                if (currentQty > 1) {
                    qtyInput.value = currentQty - 1;
                }
            });

            qtyPlusBtn.addEventListener('click', function () {
                let currentQty = parseInt(qtyInput.value);
                // You can add a max stock check here if needed
                qtyInput.value = currentQty + 1;
            });

            // Product variant selection
            const variants = <?= json_encode($variants) ?>;
            const storageOptions = document.querySelectorAll('#storage-options .option-btn');
            const colorOptions = document.querySelectorAll('#color-options .color-option-btn');
            const displayPrice = document.getElementById('display-price');
            const displayOriginalPrice = document.getElementById('display-original-price');
            const stockStatusDisplay = document.getElementById('stock-status-display');
            const addToCartBtn = document.getElementById('add-to-cart-btn');
            const buyNowBtn = document.getElementById('buy-now-btn');
            const addToWishlistBtn = document.getElementById('add-to-wishlist-btn');

            let selectedStorage = null;
            let selectedColor = null;
            let selectedVariant = null;

            function formatPrice(price) {
                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
            }

            function updateAvailableOptions() {
                const availableStorageForColor = new Set();
                const availableColorForStorage = new Set();

                variants.forEach(variant => {
                    const isStorageMatch = !selectedStorage || variant.storage === selectedStorage;
                    const isColorMatch = !selectedColor || variant.color === selectedColor;

                    if (variant.stock > 0) {
                        if (isStorageMatch && variant.color) {
                            availableColorForStorage.add(variant.color);
                        }
                        if (isColorMatch && variant.storage) {
                            availableStorageForColor.add(variant.storage);
                        }
                    }
                });

                storageOptions.forEach(btn => {
                    const storage = btn.dataset.storage;
                    const isAvailable = availableStorageForColor.has(storage) || !selectedColor;
                    btn.disabled = !isAvailable;
                    if (!isAvailable && btn.classList.contains('active')) {
                        btn.classList.remove('active');
                        selectedStorage = null;
                    }
                });

                colorOptions.forEach(btn => {
                    const color = btn.dataset.color;
                    const isAvailable = availableColorForStorage.has(color) || !selectedStorage;
                    btn.disabled = !isAvailable;
                    if (!isAvailable && btn.classList.contains('active')) {
                        btn.classList.remove('active');
                        selectedColor = null;
                        mainProductImage.src = '<?= htmlspecialchars('/Apple_Shop/' . ($product['image'] ?? 'default-product.png')) ?>';
                    }
                });
            }

            function updateProductDisplay() {
                selectedVariant = null;
                let foundExactMatch = false;

                variants.forEach(variant => {
                    const storageMatch = !selectedStorage || variant.storage === selectedStorage;
                    const colorMatch = !selectedColor || variant.color === selectedColor;

                    if (storageMatch && colorMatch) {
                        selectedVariant = variant;
                        foundExactMatch = true;
                        if (variant.variant_image) {
                            mainProductImage.src = '<?= htmlspecialchars('/Apple_Shop/') ?>' + variant.variant_image;
                        } else {
                            mainProductImage.src = '<?= htmlspecialchars('/Apple_Shop/' . ($product['image'] ?? 'default-product.png')) ?>';
                        }
                        return; // Found exact match, stop
                    }
                });
                
                // Fallback for image if no exact variant match, but color is selected
                if (!foundExactMatch && selectedColor) {
                    const colorVariant = variants.find(v => v.color === selectedColor && v.variant_image);
                    if (colorVariant) {
                        mainProductImage.src = '<?= htmlspecialchars('/Apple_Shop/') ?>' + colorVariant.variant_image;
                    } else {
                        mainProductImage.src = '<?= htmlspecialchars('/Apple_Shop/' . ($product['image'] ?? 'default-product.png')) ?>';
                    }
                } else if (!foundExactMatch && !selectedColor && !selectedStorage) {
                     mainProductImage.src = '<?= htmlspecialchars('/Apple_Shop/' . ($product['image'] ?? 'default-product.png')) ?>';
                }


                if (selectedVariant && selectedVariant.stock > 0) {
                    displayPrice.textContent = formatPrice(selectedVariant.price);
                    stockStatusDisplay.innerHTML = '<i class="fas fa-check-circle"></i> Còn hàng (' + selectedVariant.stock + ' sản phẩm)';
                    stockStatusDisplay.style.color = 'var(--text-color)';
                    addToCartBtn.disabled = false;
                    buyNowBtn.disabled = false;
                    addToWishlistBtn.disabled = false;
                } else {
                    displayPrice.textContent = formatPrice(variants[0]?.price ?? 0); // Show default price if no variant or out of stock
                    stockStatusDisplay.innerHTML = '<i class="fas fa-times-circle"></i> Hết hàng';
                    stockStatusDisplay.style.color = 'var(--red-color)';
                    addToCartBtn.disabled = true;
                    buyNowBtn.disabled = true;
                    addToWishlistBtn.disabled = true;
                }
            }


            storageOptions.forEach(button => {
                button.addEventListener('click', function () {
                    storageOptions.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    selectedStorage = this.dataset.storage;
                    updateProductDisplay();
                    updateAvailableOptions();
                });
            });

            colorOptions.forEach(button => {
                button.addEventListener('click', function () {
                    colorOptions.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    selectedColor = this.dataset.color;
                    updateProductDisplay();
                    updateAvailableOptions();
                });
            });

            // Initial update
            updateAvailableOptions();
            updateProductDisplay(); // Set initial state of buttons/price/stock

            // Tabs functionality
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', function () {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    document.getElementById(this.dataset.tab).classList.add('active');
                });
            });

            // Add to cart functionality
            addToCartBtn.addEventListener('click', async function () {
                if (!selectedVariant) {
                    alert('Vui lòng chọn đầy đủ biến thể sản phẩm.');
                    return;
                }
                const quantity = parseInt(qtyInput.value);
                if (quantity <= 0) {
                    alert('Số lượng sản phẩm phải lớn hơn 0.');
                    return;
                }

                if (quantity > selectedVariant.stock) {
                    alert('Số lượng trong kho không đủ. Chỉ còn ' + selectedVariant.stock + ' sản phẩm.');
                    return;
                }

                try {
                    const response = await fetch('../cart/add_to_cart.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `variant_id=${selectedVariant.variant_id}&quantity=${quantity}&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>`
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert(data.message);
                        // Cập nhật số lượng giỏ hàng trên header nếu có hàm updateCartCount
                        if (typeof updateCartCount === 'function') {
                            updateCartCount();
                        }
                    } else {
                        alert('Có lỗi khi thêm vào giỏ hàng: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error adding to cart:', error);
                    alert('Có lỗi khi thêm vào giỏ hàng.');
                }
            });

            async function addToWishlist(productId, variantId) {
                if (!variantId) {
                    alert('Vui lòng chọn biến thể sản phẩm trước khi thêm vào danh sách yêu thích.');
                    return;
                }

                try {
                    const response = await fetch('../add_to_wishlist.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `variant_id=${variantId}&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>`
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('Có lỗi khi thêm vào danh sách yêu thích: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error adding to wishlist:', error);
                    alert('Có lỗi khi thêm vào danh sách yêu thích.');
                }
            }

            addToWishlistBtn.addEventListener('click', function () {
                if (!selectedVariant) {
                    alert('Vui lòng chọn đầy đủ biến thể sản phẩm trước khi thêm vào danh sách yêu thích.');
                    return;
                }
                addToWishlist(<?= htmlspecialchars($product_id) ?>, selectedVariant.variant_id);
            });
        });
    </script>
    <style>
        /* CSS cho phần sản phẩm liên quan */
        .related-products {
            margin-top: 60px;
            padding: 30px 20px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .related-products h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--text-color);
            text-align: center;
            margin-bottom: 40px;
        }

        .product-grid-horizontal {
            display: flex; /* Sử dụng flexbox */
            flex-wrap: nowrap; /* Ngăn không cho các item xuống dòng */
            overflow-x: auto; /* Cho phép cuộn ngang */
            gap: 30px; /* Khoảng cách giữa các item */
            padding-bottom: 15px; /* Thêm padding để cuộn không bị dính vào cạnh */
            -webkit-overflow-scrolling: touch; /* Tăng trải nghiệm cuộn trên thiết bị di động */
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #888 #f1f1f1; /* Firefox */
        }

        /* Tùy chỉnh thanh cuộn cho Webkit (Chrome, Safari) */
        .product-grid-horizontal::-webkit-scrollbar {
            height: 8px;
        }

        .product-grid-horizontal::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        .product-grid-horizontal::-webkit-scrollbar-track {
            background-color: #f1f1f1;
        }

        .product-item-horizontal {
            flex: 0 0 250px; /* Chiều rộng cố định cho mỗi sản phẩm để chúng không bị co lại */
            background: var(--light-gray);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .product-item-horizontal:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        .product-item-horizontal .product-image-horizontal {
            max-width: 100%;
            height: 180px; /* Chiều cao cố định cho hình ảnh */
            object-fit: contain; /* Đảm bảo hình ảnh vừa vặn mà không bị cắt */
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .product-item-horizontal h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 10px;
            line-height: 1.4;
            height: 3.6em; /* Giới hạn 2 dòng tiêu đề */
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-item-horizontal h3 a {
            text-decoration: none;
            color: inherit;
            transition: color 0.2s ease-in-out;
        }

        .product-item-horizontal h3 a:hover {
            color: var(--primary-color);
        }

        .product-item-horizontal .price {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--red-color);
            margin-bottom: 15px;
        }

        .product-item-horizontal .view-detail-btn-horizontal {
            background: var(--primary-color);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s ease-in-out, transform 0.2s ease-in-out;
        }

        .product-item-horizontal .view-detail-btn-horizontal:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .product-grid-horizontal {
                gap: 20px;
            }
            .product-item-horizontal {
                flex: 0 0 200px; /* Điều chỉnh kích thước nhỏ hơn trên màn hình nhỏ */
                padding: 15px;
            }
            .related-products h2 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 480px) {
            .product-item-horizontal {
                flex: 0 0 180px; /* Kích thước nhỏ hơn nữa trên di động */
            }
        }
    </style>
</body>

</html>
<?php $conn->close(); ?>