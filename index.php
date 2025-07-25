<?php
session_start();
require_once __DIR__ . '/config/db.php';

// Redirect admin users
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/index.php");
    exit;
}

// Lấy danh sách categories
function getCategories($conn)
{
    $categories = [];
    $query = "SELECT id, name FROM categories WHERE is_active = 1 ORDER BY id ASC";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

// Lấy tất cả sản phẩm cho gallery (kèm options)
function getAllProducts($conn)
{
    $products = [];
    $query = "SELECT p.id, p.product_name, p.price, p.description, p.product_image, pv.variant_price, pv.variant_image, p.category_id, pv.id as variant_id
              FROM products p 
              LEFT JOIN product_variants pv ON pv.product_id = p.id AND pv.status = 1
              WHERE p.status = 1 AND pv.id IS NOT NULL
              ORDER BY p.created_at DESC";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $row['image_url'] = !empty($row['variant_image']) ? $row['variant_image'] : (!empty($row['product_image']) ? $row['product_image'] : 'assets/images/placeholder.jpg');
        // Lấy options màu/dung lượng
        $row['storage_options'] = [];
        $row['color_options'] = [];
        $opt_query = "SELECT va.name as attr_name, vav.value as attr_value, vav.hex_code
            FROM product_variant_attribute_links pval
            JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
            JOIN variant_attributes va ON vav.attribute_id = va.id
            WHERE pval.variant_id = ?";
        $stmt = $conn->prepare($opt_query);
        $stmt->bind_param("i", $row['variant_id']);
        $stmt->execute();
        $opt_result = $stmt->get_result();
        while ($opt = $opt_result->fetch_assoc()) {
            if (strtolower($opt['attr_name']) === 'dung lượng') {
                $row['storage_options'][] = $opt['attr_value'];
            } elseif (strtolower($opt['attr_name']) === 'màu sắc') {
                $row['color_options'][] = ['name' => $opt['attr_value'], 'hex' => $opt['hex_code']];
            }
        }
        $stmt->close();
        $products[] = $row;
    }
    return $products;
}

// Lấy 1 sản phẩm mới nhất cho mỗi category
function getCategoryFeaturedProducts($conn)
{
    $featured = [];
    $categories = getCategories($conn);
    foreach ($categories as $cat) {
        $stmt = $conn->prepare("SELECT p.id, p.product_name, p.price, p.description, p.product_image, pv.variant_price, pv.variant_image, p.category_id, pv.id as variant_id
            FROM products p
            LEFT JOIN product_variants pv ON pv.product_id = p.id AND pv.status = 1
            WHERE p.status = 1 AND p.category_id = ? AND pv.id IS NOT NULL
            ORDER BY p.created_at DESC LIMIT 1");
        $stmt->bind_param("i", $cat['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $row['image_url'] = !empty($row['variant_image']) ? $row['variant_image'] : (!empty($row['product_image']) ? $row['product_image'] : 'assets/images/placeholder.jpg');
            $row['category_name'] = $cat['name'];
            // Lấy options màu/dung lượng
            $row['storage_options'] = [];
            $row['color_options'] = [];
            $opt_query = "SELECT va.name as attr_name, vav.value as attr_value, vav.hex_code
                FROM product_variant_attribute_links pval
                JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
                JOIN variant_attributes va ON vav.attribute_id = va.id
                WHERE pval.variant_id = ?";
            $stmt2 = $conn->prepare($opt_query);
            $stmt2->bind_param("i", $row['variant_id']);
            $stmt2->execute();
            $opt_result = $stmt2->get_result();
            while ($opt = $opt_result->fetch_assoc()) {
                if (strtolower($opt['attr_name']) === 'dung lượng') {
                    $row['storage_options'][] = $opt['attr_value'];
                } elseif (strtolower($opt['attr_name']) === 'màu sắc') {
                    $row['color_options'][] = ['name' => $opt['attr_value'], 'hex' => $opt['hex_code']];
                }
            }
            $stmt2->close();
            $featured[] = $row;
        }
        $stmt->close();
    }
    return $featured;
}

// Lấy random sản phẩm cho flash sale
function getRandomFlashSaleProducts($conn, $limit = 6)
{
    $all = getAllProducts($conn);
    shuffle($all);
    return array_slice($all, 0, $limit);
}

$categories = array_filter(getCategories($conn), function ($cat) {
    return $cat['id'] >= 1 && $cat['id'] <= 6;
});
$categoryFeatured = getCategoryFeaturedProducts($conn);
$flashSaleProducts = getRandomFlashSaleProducts($conn, 6);
$allProducts = getAllProducts($conn);
$showrooms = [
    [
        'name' => 'Apple Store Hà Nội',
        'address' => 'Tầng 1, Vincom Center, 191 Bà Triệu, Hà Nội',
        'image' => 'assets/images/showroom.jpg'
    ],
    [
        'name' => 'Apple Store Hồ Chí Minh',
        'address' => 'Tầng 2, Saigon Centre, 65 Lê Lợi, Q.1, TP.HCM',
        'image' => 'assets/images/showroom.jpg'
    ],
    [
        'name' => 'Apple Store Đà Nẵng',
        'address' => 'Tầng 3, Vincom Plaza, 910A Ngô Quyền, Đà Nẵng',
        'image' => 'assets/images/showroom.jpg'
    ],
    [
        'name' => 'Apple Store Hải Phòng',
        'address' => 'Tầng 1, Vincom Plaza, 4 Lạch Tray, Hải Phòng',
        'image' => 'assets/images/showroom.jpg'
    ]
];


$siteSettings = [
    'site_name' => 'Apple Shop Gallery',
    'hero_title' => 'Khám phá bộ sưu tập Apple',
    'hero_subtitle' => 'Sản phẩm chính hãng, giá tốt, cập nhật mới nhất!'
];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteSettings['site_name']) ?></title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/css/splide.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-color: #007aff;
            /* Apple Blue */
            --secondary-color: #ff3b30;
            /* Apple Red */
            --accent-color: #ff9500;
            /* Orange */
            --text-dark: #333;
            --text-light: #f8f8f8;
            --bg-light: #f5f8fa;
            --card-bg: #ffffff;
            --border-color: #e0e0e0;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'SF Pro Display', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Banner */
        .hero-banner {
            position: relative;
            height: 500px;
            /* Adjusted height */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: var(--text-light);
            text-align: center;
        }

        .hero-banner video {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: 0;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }

        .hero-banner::before {
            /* Overlay for better text readability */
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.45);
            /* Darker overlay */
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            padding: 20px;
            text-shadow: 0 2px 5px var(--shadow-medium);
        }

        .hero-content h1 {
            font-size: 3.8rem;
            /* Larger font size */
            margin-bottom: 15px;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .hero-content p {
            font-size: 1.6rem;
            /* Larger font size */
            margin-bottom: 30px;
            font-weight: 300;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-content .btn {
            display: inline-block;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 14px 40px;
            /* More padding */
            border-radius: 50px;
            /* Pill shape */
            text-decoration: none;
            font-weight: 600;
            font-size: 1.15rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 6px 20px rgba(0, 122, 255, 0.3);
        }

        .hero-content .btn:hover {
            background-color: #005bb5;
            /* Darker blue on hover */
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 122, 255, 0.4);
        }

        /* Showroom Section */
        .showroom-section {
            padding: 60px 0;
            background-color: #f0f2f5;
            /* Light gray background */
            border-top: 1px solid var(--border-color);
        }

        .splide__track {
            margin: 0 auto;
            padding: 0 20px;
        }

        .splide__slide {
            padding: 15px;
            /* Khoảng cách giữa các card trong slide */
            box-sizing: border-box;
        }

        .showroom-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 5px 20px var(--shadow-light);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
            text-align: center;
            height: 100%;
            /* Đảm bảo chiều cao đồng đều cho các card trong slide */
            display: flex;
            flex-direction: column;
        }

        .showroom-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px var(--shadow-medium);
        }

        .showroom-card img {
            width: 100%;
            height: 220px;
            /* Slightly taller for better display */
            object-fit: cover;
            border-bottom: 1px solid var(--border-color);
            border-radius: 16px 16px 0 0;
            flex-shrink: 0;
            /* Ngăn ảnh co lại */
        }

        .showroom-info {
            padding: 25px;
            flex-grow: 1;
            /* Cho phép info box chiếm hết không gian còn lại */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Đặt nội dung cách đều */
        }

        .showroom-info .showroom-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text-dark);
        }

        .showroom-info .showroom-address {
            font-size: 1.05rem;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .showroom-info .btn-direction {
            display: inline-block;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 12px 30px;
            border-radius: 50px;
            /* Pill shape */
            text-decoration: none;
            font-weight: 600;
            font-size: 1.05rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 6px 20px rgba(0, 122, 255, 0.3);
            margin-top: auto;
            /* Đẩy nút xuống dưới cùng */
        }

        .showroom-info .btn-direction:hover {
            background-color: #005bb5;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 122, 255, 0.4);
        }

        /* Splide custom arrows/dots (optional styling) */
        .splide__arrow {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            opacity: 0.8;
            transition: background 0.3s ease, opacity 0.3s ease;
        }

        .splide__arrow:hover {
            background: var(--primary-color);
            opacity: 1;
        }

        .splide__arrow svg {
            fill: #fff;
        }

        .splide__pagination__page.is-active {
            background: var(--primary-color);
        }

        /* Section Titles */
        .section-title {
            font-size: 2.8rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 50px;
            color: var(--text-dark);
            position: relative;
            padding-bottom: 15px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background-color: var(--primary-color);
            border-radius: 2px;
        }

        /* Category Gallery Section */
        .category-gallery-section {
            padding: 60px 0;
            background-color: #ffffff;
            /* White background */
            border-top: 1px solid var(--border-color);
        }

        .category-gallery-title {
            font-size: 2.2rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 40px;
            color: var(--text-dark);
        }

        .category-gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            /* Increased gap */
        }

        .category-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 5px 20px var(--shadow-light);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .category-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 30px var(--shadow-medium);
        }

        .category-card img {
            width: 100%;
            height: 220px;
            object-fit: contain;
            background: #f8f8f8;
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
            /* Added padding */
        }

        .category-info {
            padding: 25px;
            text-align: center;
            flex-grow: 1;
            /* Allows content to take available space */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .category-info .cat-name {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .category-info .cat-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 15px;
        }

        .category-info .cat-desc {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 20px;
            min-height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .category-info .btn-detail {
            display: inline-block;
            background-color: var(--primary-color);
            color: var(--text-light);
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            box-shadow: 0 3px 10px rgba(0, 122, 255, 0.2);
        }

        .category-info .btn-detail:hover {
            background-color: #005bb5;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
        }

        /* Gallery Sections (Flash Sale & All Products) */
        .gallery-section {
            padding: 60px 0;
            background-color: var(--bg-light);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .gallery-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 5px 20px var(--shadow-light);
            overflow: hidden;
            text-align: center;
            transition: box-shadow 0.3s ease, transform 0.3s ease;
            position: relative;
            opacity: 0;
            transform: translateY(20px);
            border: 1px solid var(--border-color);
            padding-bottom: 15px;
            /* Added padding for actions */
        }

        .gallery-card.visible {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .gallery-card:hover {
            box-shadow: 0 10px 30px var(--shadow-medium);
            transform: translateY(-8px);
            border-color: var(--primary-color);
            /* Highlight border on hover */
        }

        .gallery-card img {
            width: 100%;
            height: 240px;
            /* Slightly taller */
            object-fit: contain;
            background: #fdfdfd;
            transition: transform 0.4s ease;
            border-bottom: 1px solid var(--border-color);
            padding: 15px;
            /* Padding for image */
            border-radius: 16px 16px 0 0;
        }

        .gallery-card:hover img {
            transform: scale(1.05);
            /* Less aggressive zoom */
        }

        .gallery-card .name {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 15px 15px 5px;
            color: var(--text-dark);
            min-height: 30px;
        }

        .gallery-card .price {
            color: var(--secondary-color);
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .gallery-card .desc {
            font-size: 0.95rem;
            color: #666;
            margin: 0 15px 15px;
            min-height: 40px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .gallery-card .option-section {
            padding: 0 15px;
            margin-bottom: 15px;
        }

        .gallery-card .option-section>div {
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 500;
            color: #555;
            text-align: left;
        }

        .option-btn {
            margin: 0 4px 8px 0;
            padding: 7px 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background: #f0f0f0;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, border 0.2s ease;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-dark);
        }

        .option-btn.active,
        .option-btn:hover {
            background: var(--primary-color);
            color: var(--text-light);
            border-color: var(--primary-color);
        }

        .color-dot {
            display: inline-block;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            border: 2px solid var(--border-color);
            margin: 0 4px 8px 0;
            vertical-align: middle;
            cursor: pointer;
            transition: box-shadow 0.2s ease, border 0.2s ease;
        }

        .color-dot.active,
        .color-dot:hover {
            box-shadow: 0 0 0 3px var(--primary-color);
            border-color: var(--primary-color);
        }

        .gallery-card .actions {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 15px;
            padding: 0 15px;
        }

        .gallery-card .actions button {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .gallery-card .actions .add-cart {
            background-color: var(--primary-color);
            color: var(--text-light);
            box-shadow: 0 3px 10px rgba(0, 122, 255, 0.2);
        }

        .gallery-card .actions .add-cart:hover {
            background-color: #005bb5;
            box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
        }

        .gallery-card .actions .add-wishlist {
            background: #fff;
            color: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            box-shadow: 0 3px 10px rgba(255, 59, 48, 0.1);
        }

        .gallery-card .actions .add-wishlist:hover {
            background: var(--secondary-color);
            color: #fff;
            box-shadow: 0 5px 15px rgba(255, 59, 48, 0.2);
        }

        .gallery-card .btn-detail {
            display: block;
            margin: 15px auto 0;
            padding: 10px 20px;
            background-color: #f0f0f0;
            color: var(--text-dark);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.3s ease, color 0.3s ease;
            width: fit-content;
        }

        .gallery-card .btn-detail:hover {
            background-color: var(--primary-color);
            color: var(--text-light);
        }

        .badge-sale {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--secondary-color);
            color: #fff;
            font-size: 0.95rem;
            padding: 6px 15px;
            border-radius: 6px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(255, 59, 48, 0.3);
            letter-spacing: 0.5px;
            z-index: 1;
        }

        .quick-view {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #fff;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, border 0.2s ease;
            z-index: 3;
            font-size: 1.2rem;
            box-shadow: 0 2px 8px rgba(0, 122, 255, 0.2);
        }

        .quick-view:hover {
            background: var(--primary-color);
            color: #fff;
        }

        /* Filter Bar */
        .filter-bar {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .filter-btn {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 10px 25px;
            border-radius: 25px;
            /* More rounded */
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease, color 0.3s ease, border 0.3s ease, box-shadow 0.3s ease;
            font-size: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary-color);
            color: var(--text-light);
            border-color: var(--primary-color);
            box-shadow: 0 4px 15px rgba(0, 122, 255, 0.3);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            gap: 10px;
        }

        .pagination button {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.3s ease, color 0.3s ease, border 0.3s ease;
        }

        .pagination button.active,
        .pagination button:hover {
            background: var(--primary-color);
            color: var(--text-light);
            border-color: var(--primary-color);
        }

        /* Modal Quick View */
        .modal-quickview {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-quickview.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-quickview-content {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 16px;
            max-width: 550px;
            /* Wider modal */
            width: 90%;
            position: relative;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal-quickview.active .modal-quickview-content {
            transform: translateY(0);
        }

        .modal-quickview-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 2.2rem;
            color: #888;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .modal-quickview-close:hover {
            color: var(--secondary-color);
        }

        #modalQuickViewBody {
            text-align: center;
        }

        #modalQuickViewBody img {
            max-width: 80%;
            height: 200px;
            object-fit: contain;
            background: #fdfdfd;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        #modalQuickViewBody>div {
            margin-bottom: 12px;
        }

        #modalQuickViewBody .option-btn,
        #modalQuickViewBody .color-dot {
            margin-top: 5px;
        }

        #modalQuickViewBody .add-cart,
        #modalQuickViewBody .add-wishlist {
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1.05rem;
            font-weight: 700;
            margin-top: 15px;
        }

        /* Toast Notifications */
        .toast-container {
            position: fixed;
            top: 30px;
            right: 30px;
            z-index: 2000;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .toast {
            background: var(--primary-color);
            color: var(--text-light);
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0, 122, 255, 0.3);
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            font-size: 1rem;
            font-weight: 600;
            min-width: 250px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.error {
            background: var(--secondary-color);
            box-shadow: 0 5px 15px rgba(255, 59, 48, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 3rem;
            }

            .hero-content p {
                font-size: 1.3rem;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .category-gallery-grid,
            .gallery-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .hero-banner {
                height: 400px;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content p {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 1.8rem;
                margin-bottom: 30px;
            }

            .filter-bar {
                gap: 10px;
                margin-bottom: 25px;
            }

            .filter-btn {
                padding: 8px 18px;
                font-size: 0.95rem;
            }

            .gallery-card img {
                height: 200px;
            }

            .gallery-card .name {
                font-size: 1.15rem;
            }

            .gallery-card .price {
                font-size: 1.1rem;
            }

            .gallery-card .actions button {
                padding: 8px 15px;
                font-size: 0.9rem;
            }

            .quick-view {
                width: 36px;
                height: 36px;
                font-size: 1.1rem;
                top: 10px;
                right: 10px;
            }

            .badge-sale {
                font-size: 0.85rem;
                padding: 5px 12px;
                top: 10px;
                left: 10px;
            }

            .modal-quickview-content {
                padding: 20px;
            }

            .modal-quickview-close {
                font-size: 1.8rem;
                top: 10px;
                right: 15px;
            }
        }

        @media (max-width: 480px) {
            .hero-banner {
                height: 300px;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-content p {
                font-size: 0.9rem;
                margin-bottom: 20px;
            }

            .hero-content .btn {
                padding: 10px 25px;
                font-size: 1rem;
            }

            .section-title {
                font-size: 1.6rem;
            }

            .category-gallery-title {
                font-size: 1.8rem;
            }

            .category-gallery-grid,
            .gallery-grid {
                grid-template-columns: 1fr;
                /* Single column on very small screens */
            }

            .toast {
                right: 15px;
                top: 15px;
                padding: 12px 20px;
                font-size: 0.9rem;
                min-width: unset;
                width: calc(100% - 60px);
                /* Adjust for padding on small screens */
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <main>
        <section class="hero-banner">
            <video src="assets/products/iphone.mp4" autoplay muted loop playsinline></video>
            <div class="hero-content">
                <h1><?= htmlspecialchars($siteSettings['hero_title']) ?></h1>
                <p><?= htmlspecialchars($siteSettings['hero_subtitle']) ?></p>
                <a href="#gallery" class="btn">Khám phá ngay</a>
            </div>
        </section>

        <section class="category-gallery-section">
            <div class="container">
                <div class="category-gallery-title">Sản phẩm nổi bật từ các danh mục</div>
                <div class="category-gallery-grid">
                    <?php foreach ($categoryFeatured as $item): ?>
                        <div class="category-card">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>"
                                alt="<?= htmlspecialchars($item['product_name']) ?>">
                            <div class="category-info">
                                <div class="cat-name"><?= htmlspecialchars($item['category_name']) ?>:
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </div>
                                <div class="cat-price">
                                    <?= number_format($item['variant_price'] ?? $item['price'], 0, ',', '.') ?>₫
                                </div>
                                <div class="cat-desc"><?= htmlspecialchars(mb_substr($item['description'], 0, 80)) ?>...
                                </div>
                                <a href="product_detail.php?id=<?= $item['id'] ?>" class="btn-detail">Xem chi tiết</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="gallery-section" id="flashsale">
            <div class="container">
                <div class="section-title">Ưu đãi Flash Sale</div>
                <div class="filter-bar">
                    <button class="filter-btn active" data-category="all">Tất cả</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="filter-btn"
                            data-category="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="gallery-grid" id="flashsale-grid"></div>
                <div class="pagination" id="flashsale-pagination"></div>
            </div>
        </section>



        <section class="gallery-section" id="gallery">
            <div class="container">
                <div class="section-title">Tất cả sản phẩm</div>
                <div class="filter-bar">
                    <button class="filter-btn active" data-category="all">Tất cả</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="filter-btn"
                            data-category="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></button>
                    <?php endforeach; ?>
                </div>
                <div class="gallery-grid" id="gallery-grid"></div>
                <div class="pagination" id="gallery-pagination"></div>
            </div>
        </section>

        <div class="modal-quickview" id="modalQuickView">
            <div class="modal-quickview-content" id="modalQuickViewContent">
                <button class="modal-quickview-close" id="modalQuickViewClose">&times;</button>
                <div id="modalQuickViewBody"></div>
            </div>
        </div>
        <div class="toast-container" id="toast-container"></div>

        <section class="showroom-section">
            <div class="container">
                <div class="section-title">Hệ thống Showroom của chúng tôi</div>
                <?php if (!empty($showrooms)): ?>
                    <div class="splide" id="showroom-carousel" role="group" aria-label="Showrooms">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <?php foreach ($showrooms as $showroom): ?>
                                    <li class="splide__slide">
                                        <div class="showroom-card">
                                            <img src="<?= htmlspecialchars($showroom['image'] ?? 'assets/images/showroom-placeholder.jpg') ?>"
                                                alt="<?= htmlspecialchars($showroom['name']) ?>">
                                            <div class="showroom-info">
                                                <div class="showroom-name"><?= htmlspecialchars($showroom['name']) ?></div>
                                                <div class="showroom-address"><i class="fas fa-map-marker-alt"></i>
                                                    <?= htmlspecialchars($showroom['address']) ?></div>
                                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($showroom['address']) ?>"
                                                    target="_blank" class="btn-direction"><i class="fas fa-directions"></i> Chỉ
                                                    đường</a>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="text-align: center; width: 100%;">Hiện chưa có thông tin showroom nào để hiển thị.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.0.7/dist/js/splide.min.js"></script>
    <script src="scripts/header.js"></script>
    <script>
        // Toast
        function showToast(msg, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'toast ' + type; // Add type class for styling
            toast.innerHTML = `<i class="fas ${type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${msg}`; // Icon
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 10);
            setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300) }, 3000); // Longer display time
        }

        // Hiệu ứng động khi xuất hiện
        function revealCards(gridSelector) {
            const cards = document.querySelectorAll(gridSelector + ' .gallery-card:not(.visible)'); // Only select non-visible cards
            cards.forEach((card, i) => {
                setTimeout(() => card.classList.add('visible'), 80 * i);
            });
        }

        // Dữ liệu
        const flashSaleProducts = <?= json_encode($flashSaleProducts) ?>;
        const allProducts = <?= json_encode($allProducts) ?>;
        const PAGE_SIZE = 8;

        // Filter
        let flashSaleFilter = 'all';
        let galleryFilter = 'all';

        function filterProducts(products, categoryId) {
            if (categoryId === 'all') return products;
            return products.filter(p => p.category_id == categoryId);
        }

        // Render gallery với chọn màu/dung lượng, thêm vào giỏ/wishlist
        function renderGallery(products, gridId, pagId, page, isFlashSale = false, filter = 'all') {
            const grid = document.getElementById(gridId);
            const pag = document.getElementById(pagId);
            const filtered = filterProducts(products, filter);
            grid.innerHTML = '';
            pag.innerHTML = '';
            const total = filtered.length;
            const totalPages = Math.ceil(total / PAGE_SIZE);
            const start = (page - 1) * PAGE_SIZE;
            const end = Math.min(start + PAGE_SIZE, total);

            // Slice products for the current page
            const productsToRender = filtered.slice(start, end);

            productsToRender.forEach(p => {
                let storageHtml = '';
                if (p.storage_options && p.storage_options.length > 0) {
                    storageHtml = `<div class="option-section"> <div>Dung lượng:</div> ${p.storage_options.map((s, idx) => `<button class='option-btn storage-btn${idx === 0 ? ' active' : ''}' data-id='${p.id}' data-type='storage' data-value='${s}'>${s}</button>`).join('')}</div>`;
                }
                let colorHtml = '';
                if (p.color_options && p.color_options.length > 0) {
                    colorHtml = `<div class="option-section"> <div>Màu sắc:</div> ${p.color_options.map((c, idx) => `<span class='color-dot${idx === 0 ? ' active' : ''}' data-id='${p.id}' data-type='color' data-value='${c.name}' style='background:${c.hex || '#ccc'}' title='${c.name}'></span>`).join('')}</div>`;
                }
                grid.innerHTML += `
                <div class="gallery-card" data-id="${p.id}" data-variant-id="${p.variant_id}">
                    ${isFlashSale ? '<span class=\'badge-sale\'>Flash Sale</span>' : ''}
                    <img src="${p.image_url}" alt="${p.product_name}">
                    <div class="name">${p.product_name}</div>
                    <div class="price">${(p.variant_price ?? p.price).toLocaleString('vi-VN')}₫</div>
                    <div class="desc">${p.description ? p.description.substring(0, 60) + '...' : 'Mô tả sản phẩm...'}</div>
                    ${storageHtml}
                    ${colorHtml}
                    <div class="actions">
                        <button class="add-cart"><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                        <button class="add-wishlist"><i class="fas fa-heart"></i></button>
                    </div>
                    <a href="products/product_detail.php?id=${p.id}" class="btn-detail">Xem chi tiết</a>
                    <button class="quick-view" data-id="${p.id}" title="Xem nhanh"><i class="fas fa-eye"></i></button>
                </div>
            `;
            });

            // Ensure revealCards is called after elements are added
            requestAnimationFrame(() => revealCards('#' + gridId));

            // Pagination
            if (totalPages > 1) {
                for (let i = 1; i <= totalPages; i++) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    if (i === page) btn.classList.add('active');
                    btn.onclick = () => renderGallery(products, gridId, pagId, i, isFlashSale, filter);
                    pag.appendChild(btn);
                }
            }
            // Gắn sự kiện quick view
            grid.querySelectorAll('.quick-view').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const prod = products.find(p => p.id == id);
                    if (prod) showQuickView(prod);
                });
            });
            // Gắn sự kiện chọn option
            grid.querySelectorAll('.storage-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const card = this.closest('.gallery-card');
                    card.querySelectorAll('.storage-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            grid.querySelectorAll('.color-dot').forEach(dot => {
                dot.addEventListener('click', function () {
                    const card = this.closest('.gallery-card');
                    card.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            // Gắn sự kiện thêm vào giỏ/wishlist
            grid.querySelectorAll('.add-cart').forEach(btn => {
                btn.addEventListener('click', function () {
                    const card = this.closest('.gallery-card');
                    const id = card.dataset.id;
                    const variantId = card.dataset.variantId; // Lấy variant_id từ card
                    const storage = card.querySelector('.storage-btn.active')?.dataset.value || '';
                    const color = card.querySelector('.color-dot.active')?.dataset.value || '';
                    addToCart(id, variantId, storage, color);
                });
            });
            grid.querySelectorAll('.add-wishlist').forEach(btn => {
                btn.addEventListener('click', function () {
                    const card = this.closest('.gallery-card');
                    const id = card.dataset.id;
                    const variantId = card.dataset.variantId; // Lấy variant_id từ card
                    addToWishlist(id, variantId);
                });
            });
        }

        // Modal Quick View
        function showQuickView(product) {
            const modal = document.getElementById('modalQuickView');
            const body = document.getElementById('modalQuickViewBody');
            let storageHtml = '';
            if (product.storage_options && product.storage_options.length > 0) {
                storageHtml = `<div class="option-section"> <div>Dung lượng:</div> ${product.storage_options.map((s, idx) => `<button class='option-btn storage-btn${idx === 0 ? ' active' : ''}' data-id='${product.id}' data-type='storage' data-value='${s}'>${s}</button>`).join('')}</div>`;
            }
            let colorHtml = '';
            if (product.color_options && product.color_options.length > 0) {
                colorHtml = `<div class="option-section"> <div>Màu sắc:</div> ${product.color_options.map((c, idx) => `<span class='color-dot${idx === 0 ? ' active' : ''}' data-id='${product.id}' data-type='color' data-value='${c.name}' style='background:${c.hex || '#ccc'}' title='${c.name}'></span>`).join('')}</div>`;
            }
            body.innerHTML = `
            <img src="${product.image_url}" alt="${product.product_name}">
            <div style="font-size:1.4rem;font-weight:700;margin-bottom:8px;color:var(--text-dark);">${product.product_name}</div>
            <div style="color:var(--secondary-color);font-size:1.3rem;font-weight:600;margin-bottom:15px;">${(product.variant_price ?? product.price).toLocaleString('vi-VN')}₫</div>
            <div style="font-size:1rem;color:#555;margin-bottom:20px;min-height:40px;">${product.description ? product.description.substring(0, 120) + '...' : 'Mô tả chi tiết sản phẩm...'}</div>
            ${storageHtml}
            ${colorHtml}
            <div style='margin-top:20px;display:flex;gap:12px;justify-content:center;'>
                <button class='add-cart'><i class="fas fa-shopping-cart"></i> Thêm vào giỏ</button>
                <button class='add-wishlist'><i class="fas fa-heart"></i></button>
            </div>
            <a href="products/product_detail.php?id=${product.id}" class="btn-detail" style="margin-top:20px;display:block;width:fit-content;margin-left:auto;margin-right:auto;">Xem chi tiết sản phẩm</a>
        `;
            modal.classList.add('active');
            // Gắn lại sự kiện cho các nút trong modal
            body.querySelectorAll('.storage-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    body.querySelectorAll('.storage-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            body.querySelectorAll('.color-dot').forEach(dot => {
                dot.addEventListener('click', function () {
                    body.querySelectorAll('.color-dot').forEach(d => d.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            body.querySelector('.add-cart').onclick = () => showToast('Đã thêm vào giỏ hàng!');
            body.querySelector('.add-wishlist').onclick = () => showToast('Đã thêm vào wishlist!');
        }
        document.getElementById('modalQuickViewClose').onclick = () => {
            document.getElementById('modalQuickView').classList.remove('active');
        };
        document.getElementById('modalQuickView').onclick = (e) => {
            if (e.target === e.currentTarget) document.getElementById('modalQuickView').classList.remove('active');
        };

        // Filter bar event
        document.querySelectorAll('#flashsale .filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#flashsale .filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                flashSaleFilter = this.dataset.category;
                renderGallery(flashSaleProducts, 'flashsale-grid', 'flashsale-pagination', 1, true, flashSaleFilter);
            });
        });
        document.querySelectorAll('#gallery .filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('#gallery .filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                galleryFilter = this.dataset.category;
                renderGallery(allProducts, 'gallery-grid', 'gallery-pagination', 1, false, galleryFilter);
            });
        });

        // Khởi tạo gallery và flash sale
        renderGallery(flashSaleProducts, 'flashsale-grid', 'flashsale-pagination', 1, true, flashSaleFilter);
        renderGallery(allProducts, 'gallery-grid', 'gallery-pagination', 1, false, galleryFilter);

        // Thêm vào giỏ hàng (AJAX thật)
        async function addToCart(productId, variantId, storage, color) {
            try {
                const res = await fetch('cart/add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${encodeURIComponent(productId)}&variant_id=${encodeURIComponent(variantId)}&storage=${encodeURIComponent(storage)}&color=${encodeURIComponent(color)}&quantity=1`
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Đã thêm vào giỏ hàng!');
                    // Assuming updateCartCount is defined in header.js or elsewhere
                    if (typeof updateCartCount === 'function') updateCartCount();
                } else {
                    showToast(data.message || 'Lỗi khi thêm vào giỏ hàng!', 'error');
                }
            } catch (e) { showToast('Lỗi kết nối server!', 'error'); }
        }
        // Thêm vào wishlist (AJAX thật)
        async function addToWishlist(productId, variantId) {
            try {
                const res = await fetch('add_to_wishlist.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${encodeURIComponent(productId)}&variant_id=${encodeURIComponent(variantId)}`
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Đã thêm vào wishlist!');
                } else {
                    showToast(data.message || 'Lỗi khi thêm vào wishlist!', 'error');
                }
            } catch (e) { showToast('Lỗi kết nối server!', 'error'); }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const showroomSplideElement = document.getElementById('showroom-carousel');
            if (showroomSplideElement) {
                new Splide(showroomSplideElement, {
                    type: 'loop', // Vòng lặp vô hạn
                    perPage: 3,   // Hiển thị 3 card trên mỗi trang
                    gap: '2rem',  // Khoảng cách giữa các card
                    autoplay: true, // Tự động chạy
                    interval: 3000, // Thời gian chuyển slide (3 giây)
                    pauseOnHover: true, // Dừng khi di chuột qua
                    arrows: true, // Hiển thị mũi tên điều hướng
                    pagination: true, // Hiển thị chấm điều hướng
                    breakpoints: {
                        1200: { // Trên màn hình rộng 1200px
                            perPage: 3,
                        },
                        992: {  // Trên màn hình rộng 992px
                            perPage: 2,
                        },
                        768: {  // Trên màn hình rộng 768px
                            perPage: 1,
                            gap: '1rem',
                        },
                        480: {  // Trên màn hình rộng 480px (ví dụ điện thoại)
                            perPage: 1,
                            gap: '1rem',
                        }
                    }
                }).mount();
            }
        });
    </script>
</body>

</html>