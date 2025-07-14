<?php
session_start();
require_once '../config/db.php';

// Lấy danh mục từ query parameter
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$products_per_page = 8;

// Map category names to their corresponding IDs
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

// Lấy danh sách sản phẩm ban đầu
$products = [];
$query = "SELECT id, name, price, description, image, stock, category_id FROM products WHERE stock > 0";
$params = [];
$types = '';

if (!empty($category) && isset($category_map[$category])) {
    $query .= " AND category_id = ?";
    $params[] = $category_map[$category];
    $types .= 'i';
}

$offset = ($page - 1) * $products_per_page;
$query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $products_per_page;
$params[] = $offset;
$types .= 'ii';

$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Query preparation failed: ' . $conn->error);
}
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Lấy tổng số sản phẩm để tính tổng số trang
$count_query = "SELECT COUNT(*) as total FROM products WHERE stock > 0";
if (!empty($category) && isset($category_map[$category])) {
    $count_query .= " AND category_id = ?";
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

// Lấy biến thể cho JSON
$products_json = json_encode(array_map(function ($product) use ($conn, $category_id_to_name) {
    $stmt = $conn->prepare("SELECT storage, color, price FROM product_variants WHERE product_id = ? LIMIT 1");
    $stmt->bind_param("i", $product['id']);
    $stmt->execute();
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
}, $products), JSON_UNESCAPED_UNICODE);

// Sản phẩm nổi bật cho carousel
$featured_products = array_slice($products, 0, 5);

// Thiết lập các tham số cho pagination.php
$base_url = 'products.php';
$query_params = array_filter(['category' => $category]);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm - Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/product.css">
    <style>
        .filter {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 30px 0;
            flex-wrap: wrap;
        }
        .filter a {
            padding: 12px 24px;
            text-decoration: none;
            color: var(--color-text-dark);
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 10px;
            border: 2px solid var(--color-border-light);
            transition: all 0.3s ease;
        }
        .filter a:hover {
            background: var(--color-accent-blue);
            color: var(--color-primary-bg);
            border-color: var(--color-accent-blue);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .filter a.active {
            background: var(--color-accent-blue);
            color: var(--color-primary-bg);
            border-color: var(--color-accent-blue);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        .color-options .option-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            padding: 0;
            text-indent: -9999px;
            position: relative;
            border: 2px solid var(--color-border-light);
        }
        .color-options .option-btn.active::after {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border: 2px solid var(--color-accent-blue);
            border-radius: 50%;
        }
        .hero-banner::before {
            background: url('https://products.unsplash.com/photo-1607936854279-5e5a1a4bd4d8') center/cover no-repeat;
            opacity: 0.7;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }
        .pagination a {
            padding: 8px 16px;
            text-decoration: none;
            color: var(--color-text-dark);
            border: 1px solid var(--color-border-light);
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .pagination a.active, .pagination a:hover {
            background: var(--color-accent-blue);
            color: var(--color-primary-bg);
            border-color: var(--color-accent-blue);
        }
        .pagination .pagination-ellipsis {
            padding: 8px 16px;
            color: var(--color-text-dark);
        }
        .pagination .prev-next-btn {
            font-weight: bold;
        }
        .product-card {
            cursor: pointer;
            position: relative;
        }
        .product-card:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .product-name {
            transition: color 0.3s ease;
        }
        .product-name:hover {
            color: var(--color-accent-blue);
        }
        .product-actions {
            pointer-events: auto;
        }
        .product-actions button {
            pointer-events: auto;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: var(--color-primary-bg);
            border-radius: 10px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 20px;
            position: relative;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .modal-body {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .modal-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .modal-specs {
            flex: 1;
            min-width: 250px;
        }
        .spec-group, .promotions {
            margin-bottom: 20px;
        }
        .spec-title, .promotions h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .spec-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--color-border-light);
        }
        .spec-label {
            font-weight: 600;
        }
        .spec-value {
            color: var(--color-text-muted);
        }
        .promotion-list {
            list-style: disc;
            padding-left: 20px;
        }
        .promotion-list li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="hero-banner">
        <div class="hero-content">
            <h1>Sản Phẩm Apple</h1>
            <p>Khám phá các sản phẩm công nghệ tiên tiến từ Apple</p>
            <div class="hero-buttons">
                <a href="#productGrid" class="btn btn-primary">Xem ngay</a>
                <a href="products.php" class="btn btn-outline">Xem tất cả</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><img src="../assets/logo/apple-icon.png" alt="Apple">Sản phẩm nổi bật</h2>
            <a href="products.php" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="carousel-container">
            <button class="nav-btn prev-btn" onclick="scrollCarousel('prev')"><i class="fas fa-chevron-left"></i></button>
            <div class="product-carousel" id="productCarousel">
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card" data-product-id="<?= $product['id'] ?>" onclick="window.location.href='product_detail.php?id=<?= $product['id'] ?>'">
                        <?php
                        $stmt = $conn->prepare("SELECT price FROM product_variants WHERE product_id = ? LIMIT 1");
                        $stmt->bind_param("i", $product['id']);
                        $stmt->execute();
                        $variant = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                        $display_price = $variant ? (in_array($product['id'], [1, 2]) ? $variant['price'] * 0.9 : $variant['price']) : $product['price'];
                        ?>
                        <?php if (in_array($product['id'], [1, 2])): ?>
                            <span class="discount-badge">Giảm 10%</span>
                        <?php endif; ?>
                        <img src="../assets/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="product-price">
                                $<?= number_format($display_price, 2) ?>
                                <?php if (in_array($product['id'], [1, 2]) && $variant): ?>
                                    <span class="original-price">$<?= number_format($variant['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-quick-view" onclick="event.stopPropagation(); openQuickView(<?= $product['id'] ?>)">Xem nhanh</button>
                                <button class="btn btn-details" onclick="event.stopPropagation(); addToCart(<?= $product['id'] ?>)"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="nav-btn next-btn" onclick="scrollCarousel('next')"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="section-header">
            <h2 class="section-title"><img src="../assets/logo/apple-icon.png" alt="Apple">Tất cả sản phẩm</h2>
        </div>
        <div class="filter">
            <a href="#" class="filter-btn <?= empty($category) ? 'active' : '' ?>" data-category="">Tất cả</a>
            <a href="#" class="filter-btn <?= $category === 'iphone' ? 'active' : '' ?>" data-category="iphone">iPhone</a>
            <a href="#" class="filter-btn <?= $category === 'ipad' ? 'active' : '' ?>" data-category="ipad">iPad</a>
            <a href="#" class="filter-btn <?= $category === 'mac' ? 'active' : '' ?>" data-category="mac">Mac</a>
            <a href="#" class="filter-btn <?= $category === 'watch' ? 'active' : '' ?>" data-category="watch">Apple Watch</a>
            <a href="#" class="filter-btn <?= $category === 'airpod' ? 'active' : '' ?>" data-category="airpod">AirPods</a>
            <a href="#" class="filter-btn <?= $category === 'accessory' ? 'active' : '' ?>" data-category="accessory">Phụ kiện</a>
        </div>
        <div class="product-grid" id="productGrid">
            <?php if (empty($products)): ?>
                <p>Không có sản phẩm nào.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-product-id="<?= $product['id'] ?>" onclick="window.location.href='product_detail.php?id=<?= $product['id'] ?>'">
                        <?php
                        $stmt = $conn->prepare("SELECT price FROM product_variants WHERE product_id = ? LIMIT 1");
                        $stmt->bind_param("i", $product['id']);
                        $stmt->execute();
                        $variant = $stmt->get_result()->fetch_assoc();
                        $stmt->close();
                        $display_price = $variant ? (in_array($product['id'], [1, 2]) ? $variant['price'] * 0.9 : $variant['price']) : $product['price'];
                        $category_name = isset($category_id_to_name[$product['category_id']]) ? $category_id_to_name[$product['category_id']] : 'unknown';
                        ?>
                        <?php if (in_array($product['id'], [1, 2])): ?>
                            <span class="discount-badge">Giảm 10%</span>
                        <?php endif; ?>
                        <img src="../assets/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="option-group">
                                <div class="option-label">Bộ nhớ</div>
                                <div class="storage-options">
                                    <?php
                                    $storage_options = in_array($category_name, ['watch', 'airpod']) ? ['N/A'] : ($category_name == 'mac' ? ['256GB', '512GB', '1TB'] : ['64GB', '128GB', '256GB']);
                                    foreach ($storage_options as $index => $storage): ?>
                                        <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" data-storage="<?= $storage ?>" onclick="event.stopPropagation();"><?= $storage ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="option-group">
                                <div class="option-label">Màu sắc</div>
                                <div class="color-options">
                                    <?php
                                    $color_options = in_array($category_name, ['watch']) ? [
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
                                    ]);
                                    foreach ($color_options as $index => $color): ?>
                                        <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" data-color="<?= $color['name'] ?>" style="background-color: <?= $color['hex'] ?>;" title="<?= $color['name'] ?>" onclick="event.stopPropagation();"></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="product-price">
                                $<?= number_format($display_price, 2) ?>
                                <?php if (in_array($product['id'], [1, 2]) && $variant): ?>
                                    <span class="original-price">$<?= number_format($variant['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-quick-view" onclick="event.stopPropagation(); openQuickView(<?= $product['id'] ?>)">Xem nhanh</button>
                                <button class="btn btn-details" onclick="event.stopPropagation(); addToCart(<?= $product['id'] ?>)"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php
        // Include pagination
        $current_page = $page;
        include '../includes/pagination.php';
        ?>

        <div class="promotion-banner">
            <h3>Ưu đãi đặc biệt</h3>
            <p>Nhận ngay voucher giảm giá 10% khi mua sản phẩm trong tuần này!</p>
            <a href="#" class="btn btn-promo">Nhận ưu đãi</a>
        </div>
    </div>

    <div id="quickViewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalProductName"></h2>
                <button class="close-btn" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body">
                <img id="modalProductImage" class="modal-image" src="" alt="">
                <div class="modal-specs">
                    <div class="spec-group">
                        <h3 class="spec-title">Thông số kỹ thuật</h3>
                        <div class="spec-item">
                            <span class="spec-label">Giá</span>
                            <span class="spec-value" id="modalPrice"></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Mô tả</span>
                            <span class="spec-value" id="modalDescription"></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Chip</span>
                            <span class="spec-value" id="modalChip"></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Camera</span>
                            <span class="spec-value" id="modalCamera"></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Màn hình</span>
                            <span class="spec-value" id="modalScreen"></span>
                        </div>
                    </div>
                    <div class="promotions">
                        <h3>Khuyến mãi</h3>
                        <ul class="promotion-list" id="promotionList"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="../scripts/header.js"></script>
    <script src="../scripts/product.js"></script>
    <script>
        // products_json chỉ chứa dữ liệu ban đầu, được cập nhật sau mỗi lần fetch
        let products = <?= $products_json ?>;
        let selectedStorage = {};
        let selectedColor = {};
        let currentPage = <?= $page ?>;
        let totalPages = <?= $total_pages ?>;

        // Hàm để tạo thẻ sản phẩm HTML
        function createProductCard(product) {
            const isDiscounted = [1, 2].includes(parseInt(product.id));
            const originalPriceHtml = (isDiscounted && product.original_price > product.price) ?
                `<span class="original-price">$${product.original_price}</span>` : '';

            let storageOptionsHtml = '';
            product.storage_options.forEach((storage, index) => {
                storageOptionsHtml += `<button class="option-btn ${index === 0 ? 'active' : ''}" data-storage="${storage}" onclick="event.stopPropagation();">${storage}</button>`;
            });

            let colorOptionsHtml = '';
            product.color_options.forEach((color, index) => {
                colorOptionsHtml += `<button class="option-btn ${index === 0 ? 'active' : ''}" data-color="${color.name}" style="background-color: ${color.hex};" title="${color.name}" onclick="event.stopPropagation();"></button>`;
            });

            return `
                <div class="product-card" data-product-id="${product.id}" onclick="window.location.href='product_detail.php?id=${product.id}'">
                    ${isDiscounted ? '<span class="discount-badge">Giảm 10%</span>' : ''}
                    <img src="${product.image}" alt="${product.name}" class="product-image">
                    <div class="product-info">
                        <h3 class="product-name">${product.name}</h3>
                        <div class="option-group">
                            <div class="option-label">Bộ nhớ</div>
                            <div class="storage-options">
                                ${storageOptionsHtml}
                            </div>
                        </div>
                        <div class="option-group">
                            <div class="option-label">Màu sắc</div>
                            <div class="color-options">
                                ${colorOptionsHtml}
                            </div>
                        </div>
                        <div class="product-price">
                            $${product.price}
                            ${originalPriceHtml}
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-quick-view" onclick="event.stopPropagation(); openQuickView(${product.id})">Xem nhanh</button>
                            <button class="btn btn-details" onclick="event.stopPropagation(); addToCart(${product.id})"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Hàm để tải sản phẩm bằng AJAX
        async function loadProducts(category = '', page = 1) {
            const productGrid = document.getElementById('productGrid');
            productGrid.innerHTML = '<p>Đang tải sản phẩm...</p>';

            try {
                const response = await fetch(`fetch_products.php?category=${category}&page=${page}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                products = data.products;
                currentPage = data.current_page;
                totalPages = data.total_pages;

                productGrid.innerHTML = '';
                if (products.length === 0) {
                    productGrid.innerHTML = '<p>Không có sản phẩm nào trong danh mục này.</p>';
                } else {
                    products.forEach(product => {
                        productGrid.innerHTML += createProductCard(product);
                    });
                    attachOptionButtonListeners();
                }

                // Cập nhật phân trang
                updatePagination(category, page);
            } catch (error) {
                console.error('Error fetching products:', error);
                productGrid.innerHTML = '<p>Có lỗi khi tải sản phẩm. Vui lòng thử lại sau.</p>';
            }
        }

        // Hàm để cập nhật phân trang
        function updatePagination(category, page) {
            const paginationContainer = document.createElement('div');
            paginationContainer.className = 'pagination';
            let paginationHtml = '';

            if (currentPage > 1) {
                paginationHtml += `<a href="#" class="page-link prev-next-btn" data-page="${currentPage - 1}">« Trước</a>`;
            }

            const numLinks = 5;
            let startPage = Math.max(1, currentPage - Math.floor(numLinks / 2));
            let endPage = Math.min(totalPages, currentPage + Math.ceil(numLinks / 2) - 1);

            if (endPage - startPage + 1 < numLinks) {
                startPage = Math.max(1, endPage - numLinks + 1);
            }

            if (startPage > 1) {
                paginationHtml += `<a href="#" class="page-link" data-page="1">1</a>`;
                if (startPage > 2) {
                    paginationHtml += `<span class="pagination-ellipsis">...</span>`;
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `<a href="#" class="page-link ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</a>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHtml += `<span class="pagination-ellipsis">...</span>`;
                }
                paginationHtml += `<a href="#" class="page-link" data-page="${totalPages}">${totalPages}</a>`;
            }

            if (currentPage < totalPages) {
                paginationHtml += `<a href="#" class="page-link prev-next-btn" data-page="${currentPage + 1}">Tiếp »</a>`;
            }

            paginationContainer.innerHTML = paginationHtml;
            const existingPagination = document.querySelector('.pagination');
            if (existingPagination) {
                existingPagination.replaceWith(paginationContainer);
            } else {
                document.getElementById('productGrid').insertAdjacentElement('afterend', paginationContainer);
            }

            // Gắn sự kiện cho các nút phân trang
            document.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.dataset.page);
                    const currentCategory = document.querySelector('.filter-btn.active')?.dataset.category || '';
                    loadProducts(currentCategory, page);
                });
            });
        }

        // Gắn event listeners cho các nút tùy chọn
        function attachOptionButtonListeners() {
            document.querySelectorAll('.product-grid .option-btn').forEach(button => {
                button.removeEventListener('click', handleOptionButtonClick);
                button.addEventListener('click', handleOptionButtonClick);
            });
        }

        function handleOptionButtonClick(event) {
            event.stopPropagation();
            const button = this;
            const productCard = button.closest('.product-card');
            const productId = productCard.dataset.productId;
            const type = button.dataset.storage ? 'storage' : 'color';
            const value = button.dataset.storage || button.dataset.color;

            productCard.querySelectorAll(`.${type}-options .option-btn`).forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            if (type === 'storage') {
                selectedStorage[productId] = value;
            } else {
                selectedColor[productId] = value;
            }

            const currentStorage = selectedStorage[productId] || products.find(p => p.id == productId).storage_options[0];
            const currentColor = selectedColor[productId] || products.find(p => p.id == productId).color_options[0].name;

            fetch('../cart/get_price.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&storage=${currentStorage}&color=${currentColor}`
            }).then(response => response.json()).then(data => {
                if (data.price) {
                    const priceElement = productCard.querySelector('.product-price');
                    const product = products.find(p => p.id == productId);
                    const originalPriceDisplay = (product && [1, 2].includes(parseInt(productId))) ? ` <span class="original-price">$${(data.price * 1.1).toFixed(2)}</span>` : '';
                    priceElement.innerHTML = `$${data.price.toFixed(2)}${originalPriceDisplay}`;
                }
            }).catch(error => console.error('Error fetching price:', error));
        }

        function openQuickView(productId) {
            const product = products.find(p => p.id == productId);
            if (product) {
                document.getElementById('modalProductName').textContent = product.name;
                document.getElementById('modalProductImage').src = product.image;
                document.getElementById('modalPrice').textContent = `$${product.price}${product.original_price > product.price ? ` (Giá gốc: $${product.original_price})` : ''}`;
                document.getElementById('modalDescription').textContent = product.description;
                document.getElementById('modalChip').textContent = product.specifications.Chip;
                document.getElementById('modalCamera').textContent = product.specifications.Camera;
                document.getElementById('modalScreen').textContent = product.specifications['Màn hình'];
                const promotionList = document.getElementById('promotionList');
                promotionList.innerHTML = '';
                product.promotions.forEach(promo => {
                    const li = document.createElement('li');
                    li.textContent = promo;
                    promotionList.appendChild(li);
                });
                document.getElementById('quickViewModal').style.display = 'block';
            }
        }

        function closeModal() {
            document.getElementById('quickViewModal').style.display = 'none';
        }

        function addToCart(productId) {
            const productData = products.find(p => p.id == productId);
            const storage = selectedStorage[productId] || productData.storage_options[0];
            const color = selectedColor[productId] || productData.color_options[0].name;

            fetch('../cart/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=1&storage=${storage}&color=${color}`
            }).then(response => response.text()).then(data => {
                if (data === 'success') {
                    alert(`Đã thêm sản phẩm ${productData.name} (${storage}, ${color}) vào giỏ hàng!`);
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                } else {
                    alert('Có lỗi khi thêm vào giỏ hàng!');
                }
            }).catch(error => console.error('Error adding to cart:', error));
        }

        function scrollCarousel(direction) {
            const carousel = document.getElementById('productCarousel');
            const scrollAmount = 300;
            if (direction === 'prev') {
                carousel.scrollLeft -= scrollAmount;
            } else {
                carousel.scrollLeft += scrollAmount;
            }
        }

        function in_array(array, value) {
            return array.includes(parseInt(value));
        }

        // Gắn event listener cho các nút lọc
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const category = this.dataset.category;
                loadProducts(category, 1); // Reset về trang 1 khi thay đổi danh mục
            });
        });

        // Khởi tạo tùy chọn đã chọn
        function initializeSelectedOptions() {
            document.querySelectorAll('.product-card').forEach(card => {
                const productId = card.dataset.productId;
                selectedStorage[productId] = card.querySelector('.storage-options .option-btn.active')?.dataset.storage || products.find(p => p.id == productId)?.storage_options[0];
                selectedColor[productId] = card.querySelector('.color-options .option-btn.active')?.dataset.color || products.find(p => p.id == productId)?.color_options[0]?.name;
            });
        }

        // Gọi khi DOM đã tải
        document.addEventListener('DOMContentLoaded', () => {
            initializeSelectedOptions();
            attachOptionButtonListeners();
            loadProducts('<?= $category ?>', <?= $page ?>); // Tải sản phẩm ban đầu với category và page từ URL
        });
    </script>
</body>
</html>