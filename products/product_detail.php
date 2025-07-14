<?php
session_start();
require_once '../config/db.php';

// Lấy product_id từ query parameter
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Map category names to IDs
$category_map = [
    'iphone' => 1,
    'ipad' => 2,
    'mac' => 3,
    'watch' => 4,
    'airpod' => 5,
    'accessory' => 6
];
$category_id_to_name = array_flip($category_map);

// Lấy thông tin sản phẩm
$product = null;
if ($product_id > 0) {
    $stmt = $conn->prepare("SELECT id, name, price, description, image, stock, category_id FROM products WHERE id = ? AND stock > 0");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}

// Lấy biến thể sản phẩm
$variants = [];
if ($product) {
    $stmt = $conn->prepare("SELECT storage, color, price FROM product_variants WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $variants[] = $row;
    }
    $stmt->close();
}

// Tạo dữ liệu JSON cho JavaScript
$category_name = isset($category_id_to_name[$product['category_id']]) ? $category_id_to_name[$product['category_id']] : 'unknown';
$product_json = $product ? json_encode([
    'id' => $product['id'],
    'name' => htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'),
    'price' => number_format($product['price'], 2),
    'original_price' => number_format(in_array($product['id'], [1, 2]) ? $product['price'] * 1.1 : $product['price'], 2),
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
], JSON_UNESCAPED_UNICODE) : '{}';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm - Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/product.css">
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }
        .product-image-container {
            flex: 1;
            min-width: 300px;
            text-align: center;
        }
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .product-info-container {
            flex: 1;
            min-width: 300px;
        }
        .product-title {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--color-text-dark);
        }
        .product-price {
            font-size: 1.5rem;
            color: var(--color-accent-blue);
            margin-bottom: 20px;
        }
        .original-price {
            font-size: 1.2rem;
            color: var(--color-text-muted);
            text-decoration: line-through;
            margin-left: 10px;
        }
        .discount-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: var(--color-accent-blue);
            color: var(--color-primary-bg);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .option-group {
            margin-bottom: 20px;
        }
        .option-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--color-text-dark);
        }
        .storage-options, .color-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .option-btn {
            padding: 8px 16px;
            border: 2px solid var(--color-border-light);
            border-radius: 5px;
            background: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .option-btn.active {
            border-color: var(--color-accent-blue);
            background: var(--color-accent-blue);
            color: var(--color-primary-bg);
        }
        .color-options .option-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            padding: 0;
            text-indent: -9999px;
            position: relative;
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
        .spec-group, .promotions {
            margin-bottom: 20px;
        }
        .spec-title, .promotions h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: var(--color-text-dark);
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
            color: var(--color-text-dark);
        }
        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-add-to-cart {
            padding: 12px 24px;
            background: var(--color-accent-blue);
            color: var(--color-primary-bg);
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-add-to-cart:hover {
            background: var(--color-accent-blue-dark);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .error-message {
            text-align: center;
            color: var(--color-text-dark);
            font-size: 1.2rem;
            margin: 50px 0;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <?php if (!$product): ?>
            <p class="error-message">Sản phẩm không tồn tại hoặc đã hết hàng.</p>
        <?php else: ?>
            <div class="product-detail-container">
                <div class="product-image-container">

                    <img src="../assets/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                </div>
                <div class="product-info-container">
                    <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                    <div class="product-price" id="productPrice">
                        $<?= number_format($product['price'], 2) ?>
                        <?php if (in_array($product['id'], [1, 2])): ?>
                            <span class="original-price">$<?= number_format($product['price'] * 1.1, 2) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="option-group">
                        <div class="option-label">Bộ nhớ</div>
                        <div class="storage-options">
                            <?php
                            $storage_options = in_array($category_name, ['watch', 'airpod']) ? ['N/A'] : ($category_name == 'mac' ? ['256GB', '512GB', '1TB'] : ['64GB', '128GB', '256GB']);
                            foreach ($storage_options as $index => $storage): ?>
                                <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" data-storage="<?= $storage ?>"><?= $storage ?></button>
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
                                <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" data-color="<?= $color['name'] ?>" style="background-color: <?= $color['hex'] ?>;" title="<?= $color['name'] ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="spec-group">
                        <h3 class="spec-title">Thông số kỹ thuật</h3>
                        <div class="spec-item">
                            <span class="spec-label">Mô tả</span>
                            <span class="spec-value"><?= htmlspecialchars($product['description']) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Chip</span>
                            <span class="spec-value"><?= in_array($category_name, ['iphone', 'ipad']) ? ($product['id'] == 1 ? 'A18' : 'A15') : ($category_name == 'mac' ? 'M1' : 'S8') ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Camera</span>
                            <span class="spec-value"><?= in_array($category_name, ['iphone', 'ipad']) ? '48MP' : ($category_name == 'mac' ? '720p' : 'N/A') ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Màn hình</span>
                            <span class="spec-value"><?= in_array($category_name, ['iphone']) ? '6.1 inch' : ($category_name == 'ipad' ? '10.2 inch' : ($category_name == 'mac' ? '13.3 inch' : '1.9 inch')) ?></span>
                        </div>
                    </div>
                    <div class="promotions">
                        <h3>Khuyến mãi</h3>
                        <ul class="promotion-list">
                            <?php
                            $promotions = [
                                'Giảm 5% khi thanh toán online',
                                in_array($category_name, ['iphone', 'ipad']) ? 'Tặng ốp lưng miễn phí' : ($category_name == 'mac' ? 'Tặng chuột Magic Mouse miễn phí' : 'Tặng dây đeo miễn phí'),
                                'Bảo hành 12 tháng'
                            ];
                            foreach ($promotions as $promo): ?>
                                <li><?= htmlspecialchars($promo) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="product-actions">
                        <button class="btn btn-add-to-cart" onclick="addToCart(<?= $product['id'] ?>)"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="../scripts/header.js"></script>
    <script src="../scripts/product.js"></script>
    <script>
        const product = <?= $product_json ?>;
        let selectedStorage = product.storage_options ? product.storage_options[0] : 'N/A';
        let selectedColor = product.color_options ? product.color_options[0].name : '';

        // Gắn event listeners cho các nút tùy chọn
        function attachOptionButtonListeners() {
            document.querySelectorAll('.option-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const type = button.dataset.storage ? 'storage' : 'color';
                    const value = button.dataset.storage || button.dataset.color;

                    document.querySelectorAll(`.${type}-options .option-btn`).forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    if (type === 'storage') {
                        selectedStorage = value;
                    } else {
                        selectedColor = value;
                    }

                    // Cập nhật giá dựa trên biến thể
                    fetch('../cart/get_price.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `product_id=${product.id}&storage=${selectedStorage}&color=${selectedColor}`
                    }).then(response => response.json()).then(data => {
                        if (data.price) {
                            const priceElement = document.getElementById('productPrice');
                            const isDiscounted = [1, 2].includes(parseInt(product.id));
                            const originalPriceDisplay = isDiscounted ? ` <span class="original-price">$${(data.price * 1.1).toFixed(2)}</span>` : '';
                            priceElement.innerHTML = `$${data.price.toFixed(2)}${originalPriceDisplay}`;
                        }
                    }).catch(error => console.error('Error fetching price:', error));
                });
            });
        }

        function addToCart(productId) {
            fetch('../cart/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=1&storage=${selectedStorage}&color=${selectedColor}`
            }).then(response => response.text()).then(data => {
                if (data === 'success') {
                    alert(`Đã thêm sản phẩm ${product.name} (${selectedStorage}, ${selectedColor}) vào giỏ hàng!`);
                    if (typeof updateCartCount === 'function') {
                        updateCartCount();
                    }
                } else {
                    alert('Có lỗi khi thêm vào giỏ hàng!');
                }
            }).catch(error => console.error('Error adding to cart:', error));
        }

        // Khởi tạo khi DOM tải xong
        document.addEventListener('DOMContentLoaded', () => {
            if (product.id) {
                attachOptionButtonListeners();
            }
        });
    </script>
</body>
</html>