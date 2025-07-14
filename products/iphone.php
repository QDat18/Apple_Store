<?php
session_start();
require_once '../config/db.php';

// Lấy model từ query parameter
$model = isset($_GET['model']) ? trim($_GET['model']) : '';
$valid_models = ['16', '15', '14', '13', '12pro', 'se', 'compare'];
$category_id = 1; // iPhone

// Lấy sản phẩm iPhone
$products = [];
if ($model && in_array($model, $valid_models)) {
    if ($model === 'compare') {
        $stmt = $conn->prepare("SELECT id, name, price, description, image, stock 
                                FROM products 
                                WHERE category_id = ? AND stock > 0 
                                ORDER BY created_at DESC");
        $stmt->bind_param("i", $category_id);
    } else {
        $model_name = match ($model) {
            '16' => 'iPhone 16', '15' => 'iPhone 15', '14' => 'iPhone 14',
            '13' => 'iPhone 13', '12pro' => 'iPhone 12', 'se' => 'iPhone SE',
            default => ''
        };
        $stmt = $conn->prepare("SELECT id, name, price, description, image, stock 
                                FROM products 
                                WHERE category_id = ? AND name LIKE ? AND stock > 0 
                                ORDER BY created_at DESC");
        $like_model = "%$model_name%";
        $stmt->bind_param("is", $category_id, $like_model);
    }
} else {
    $stmt = $conn->prepare("SELECT id, name, price, description, image, stock 
                            FROM products 
                            WHERE category_id = ? AND stock > 0 
                            ORDER BY created_at DESC");
    $stmt->bind_param("i", $category_id);
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Lấy biến thể cho JSON
$products_json = json_encode(array_map(function ($product) use ($conn) {
    $stmt = $conn->prepare("SELECT storage, color, price FROM product_variants WHERE product_id = ? LIMIT 1");
    $stmt->bind_param("i", $product['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $variant = $result->fetch_assoc();
    $stmt->close();
    
    // Dùng giá từ products nếu variant không tồn tại
    $price = $variant ? $variant['price'] : $product['price'];
    $original_price = in_array($product['id'], [1, 2]) ? $price * 1.1 : $price;
    
    return [
        'id' => $product['id'],
        'name' => htmlspecialchars($product['name']),
        'price' => number_format($price, 2),
        'original_price' => number_format($original_price, 2),
        'description' => htmlspecialchars($product['description']),
        'image' => 'assets/images/' . htmlspecialchars($product['image']),
        'stock' => $product['stock'],
        'specifications' => [
            'Chip' => $product['id'] == 1 ? 'A18' : 'A15',
            'Camera' => '48MP',
            'Màn hình' => '6.1 inch'
        ],
        'promotions' => [
            'Giảm 5% khi thanh toán online',
            'Tặng ốp lưng miễn phí',
            'Bảo hành 12 tháng'
        ],
        'storage_options' => ['64GB', '128GB', '256GB'],
        'color_options' => [
            ['name' => 'Black', 'hex' => '#000000'],
            ['name' => 'White', 'hex' => '#FFFFFF'],
            ['name' => 'Blue', 'hex' => '#007AFF']
        ]
    ];
}, $products));

// Sản phẩm nổi bật cho carousel
$featured_products = array_slice($products, 0, 5);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iPhone - Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
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
            background: url('https://images.unsplash.com/photo-1607936854279-5e5a1a4bd4d8') center/cover no-repeat;
            opacity: 0.7;
        }
        .btn-primary, .btn-outline {
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover, .btn-outline:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hero-banner, .product-carousel, .product-grid {
            animation: fadeIn 0.5s ease-in;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="hero-banner">
        <div class="hero-content">
            <h1>iPhone Mới Nhất</h1>
            <p>Trải nghiệm công nghệ đỉnh cao với thiết kế tinh tế và hiệu năng vượt trội</p>
            <div class="hero-buttons">
                <a href="#productGrid" class="btn btn-primary">Xem ngay</a>
                <a href="iphone.php?model=compare" class="btn btn-outline">So sánh iPhone</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><img src="assets/logo/iphone-icon.png" alt="iPhone">Sản phẩm nổi bật</h2>
            <a href="iphone.php" class="view-all">Xem tất cả <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="carousel-container">
            <button class="nav-btn prev-btn" onclick="scrollCarousel('prev')"><i class="fas fa-chevron-left"></i></button>
            <div class="product-carousel" id="productCarousel">
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-card" data-product-id="<?= $product['id'] ?>">
                        <?php
                        // Lấy giá từ product_variants
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
                        <img src="assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="product-price">
                                $<?= number_format($display_price, 2) ?>
                                <?php if (in_array($product['id'], [1, 2]) && $variant): ?>
                                    <span class="original-price">$<?= number_format($variant['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-quick-view" onclick="openQuickView(<?= $product['id'] ?>)">Xem nhanh</button>
                                <button class="btn btn-details" onclick="addToCart(<?= $product['id'] ?>)"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="nav-btn next-btn" onclick="scrollCarousel('next')"><i class="fas fa-chevron-right"></i></button>
        </div>

        <div class="section-header">
            <h2 class="section-title"><img src="assets/logo/iphone-icon.png" alt="iPhone">Tất cả iPhone</h2>
        </div>
        <div class="filter">
            <a href="iphone.php" class="<?= empty($model) ? 'active' : '' ?>">Tất cả</a>
            <a href="iphone.php?model=16" class="<?= $model === '16' ? 'active' : '' ?>">iPhone 16</a>
            <a href="iphone.php?model=15" class="<?= $model === '15' ? 'active' : '' ?>">iPhone 15</a>
            <a href="iphone.php?model=14" class="<?= $model === '14' ? 'active' : '' ?>">iPhone 14</a>
            <a href="iphone.php?model=13" class="<?= $model === '13' ? 'active' : '' ?>">iPhone 13</a>
            <a href="iphone.php?model=12pro" class="<?= $model === '12pro' ? 'active' : '' ?>">iPhone 12</a>
            <a href="iphone.php?model=se" class="<?= $model === 'se' ? 'active' : '' ?>">iPhone SE</a>
        </div>
        <div class="product-grid" id="productGrid">
            <?php if (empty($products)): ?>
                <p>Không có sản phẩm iPhone nào.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card" data-product-id="<?= $product['id'] ?>">
                        <?php
                        // Lấy giá từ product_variants
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
                        <img src="assets/images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="option-group">
                                <div class="option-label">Bộ nhớ</div>
                                <div class="storage-options">
                                    <?php foreach (['64GB', '128GB', '256GB'] as $index => $storage): ?>
                                        <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" data-storage="<?= $storage ?>"><?= $storage ?></button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="option-group">
                                <div class="option-label">Màu sắc</div>
                                <div class="color-options">
                                    <?php foreach ([
                                        ['name' => 'Black', 'hex' => '#000000'],
                                        ['name' => 'White', 'hex' => '#FFFFFF'],
                                        ['name' => 'Blue', 'hex' => '#007AFF']
                                    ] as $index => $color): ?>
                                        <button class="option-btn <?= $index === 0 ? 'active' : '' ?>" data-color="<?= $color['name'] ?>" style="background-color: <?= $color['hex'] ?>;" title="<?= $color['name'] ?>"></button>
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
                                <button class="btn btn-quick-view" onclick="openQuickView(<?= $product['id'] ?>)">Xem nhanh</button>
                                <button class="btn btn-details" onclick="addToCart(<?= $product['id'] ?>)"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="promotion-banner">
            <h3>Ưu đãi đặc biệt</h3>
            <p>Nhận ngay voucher giảm giá 10% khi mua iPhone trong tuần này!</p>
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
        const products = <?= $products_json ?>;
        let selectedStorage = {};
        let selectedColor = {};

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
            const storage = selectedStorage[productId] || products.find(p => p.id == productId).storage_options[0];
            const color = selectedColor[productId] || products.find(p => p.id == productId).color_options[0].name;
            fetch('../cart/add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=1&storage=${storage}&color=${color}`
            }).then(response => response.text()).then(data => {
                if (data === 'success') {
                    alert(`Đã thêm sản phẩm ${products.find(p => p.id == productId).name} (${storage}, ${color}) vào giỏ hàng!`);
                } else {
                    alert('Có lỗi khi thêm vào giỏ hàng!');
                }
            });
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

        document.querySelectorAll('.option-btn').forEach(button => {
            button.addEventListener('click', () => {
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

                // Cập nhật giá động khi chọn bộ nhớ
                fetch('get_price.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `product_id=${productId}&storage=${selectedStorage[productId] || '64GB'}&color=${selectedColor[productId] || 'Black'}`
                }).then(response => response.json()).then(data => {
                    if (data.price) {
                        const priceElement = productCard.querySelector('.product-price');
                        const originalPrice = in_array([1, 2], productId) ? data.price * 1.1 : data.price;
                        priceElement.innerHTML = `$${data.price.toFixed(2)}${in_array([1, 2], productId) ? `<span class="original-price">$${originalPrice.toFixed(2)}</span>` : ''}`;
                    }
                });
            });
        });

        function in_array(array, value) {
            return array.includes(parseInt(value));
        }
    </script>
</body>
</html>