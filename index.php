<?php
session_start();
require_once 'config/db.php';

// Kiểm tra vai trò và chuyển hướng nếu là admin
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header("Location: admin/index.php");
    exit;
}

// Log truy cập trang
$user_id = $_SESSION['user_id'] ?? null;
$log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'visit_index', ?)");
$details = "User visited customer index page";
$log_stmt->bind_param("is", $user_id, $details);
$log_stmt->execute();
$log_stmt->close();

// Tìm kiếm sản phẩm
$search = trim($_GET['search'] ?? '');
$products = [];
if ($search) {
    $stmt = $conn->prepare("SELECT id, name, price, description, image, stock FROM products WHERE name LIKE ? AND stock > 0");
    $search_term = "%{$search}%";
    $stmt->bind_param("s", $search_term);
} else {
    $stmt = $conn->prepare("SELECT id, name, price, description, image, stock FROM products WHERE stock > 0 LIMIT 8");
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$stmt->close();

// Lấy dữ liệu tin tức (blog) cho section Blog
$news_per_page = 2;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $news_per_page;
$total_news = $conn->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
$total_pages = ceil($total_news / $news_per_page);
$news_stmt = $conn->prepare("SELECT id, title, content, created_date, image_url FROM news ORDER BY created_date DESC LIMIT ? OFFSET ?");
$news_stmt->bind_param("ii", $news_per_page, $offset);
$news_stmt->execute();
$news_result = $news_stmt->get_result();
$news_items = $news_result->fetch_all(MYSQLI_ASSOC);
$news_stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ | Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/user.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/footer.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Slider Section -->
        <section class="hero-slider section-padding">
            <div class="slides-container">
                <div class="slide active" style="background-image: url('assets/images/hero1.jpg');">
                    <div class="hero-content">
                        <h1>Đỉnh cao công nghệ</h1>
                        <p>Kiệt tác tương lai trong tay bạn</p>
                        <div class="hero-buttons">
                            <a href="products.php?category=iphone" class="btn btn-primary">Sở hữu ngay!</a>
                        </div>
                    </div>
                </div>
                <div class="slide" style="background-image: url('assets/images/hero2.jpg');">
                    <div class="hero-content">
                        <h1>Ưu đãi hấp dẫn</h1>
                        <p>Khám phá các sản phẩm giảm giá hôm nay</p>
                        <div class="hero-buttons">
                            <a href="promotion.php" class="btn btn-primary">Xem khuyến mãi</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="slider-controls">
                <button class="prev-slide"><i class="fas fa-chevron-left"></i></button>
                <div class="slide-indicators"></div>
                <button class="next-slide"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>

        <!-- Product Categories Section -->
        <section class="product-categories-section section-padding">
            <h2 class="section-title">Khám phá các Danh mục</h2>
            <div class="container">
                <div class="product-grid">
                    <div class="category-card">
                        <img src="assets/images/iphone.jpg" alt="iPhone" class="category-img">
                        <h5>iPhone</h5>
                        <a href="products.php?category=iphone" class="btn btn-outline">Xem ngay</a>
                    </div>
                    <div class="category-card">
                        <img src="assets/images/mac.jpg" alt="Mac" class="category-img">
                        <h5>Mac</h5>
                        <a href="products.php?category=mac" class="btn btn-outline">Xem ngay</a>
                    </div>
                    <div class="category-card">
                        <img src="assets/images/ipad.jpg" alt="iPad" class="category-img">
                        <h5>iPad</h5>
                        <a href="products.php?category=ipad" class="btn btn-outline">Xem ngay</a>
                    </div>
                    <div class="category-card">
                        <img src="assets/images/watch.jpg" alt="Watch" class="category-img">
                        <h5>Watch</h5>
                        <a href="products.php?category=watch" class="btn btn-outline">Xem ngay</a>
                    </div>
                    <div class="category-card">
                        <img src="assets/images/airpods.jpg" alt="AirPods" class="category-img">
                        <h5>AirPods</h5>
                        <a href="products.php?category=accessories" class="btn btn-outline">Xem ngay</a>
                    </div>
                    <div class="category-card">
                        <img src="assets/images/accessories.jpg" alt="Phụ kiện" class="category-img">
                        <h5>Phụ kiện</h5>
                        <a href="products.php?category=accessories" class="btn btn-outline">Xem ngay</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Flash Sale Section -->
        <section class="flash-sale-section section-padding">
            <h2 class="section-title">Flash Sale - Giảm giá sốc</h2>
            <div class="flash-sale-timer">
                <span>Kết thúc sau:</span> <span id="countdown">00:00:00</span>
            </div>
            <div class="flash-sale-filter">
                <button class="active" data-filter="all">Tất cả</button>
                <button data-filter="iphone">iPhone</button>
                <button data-filter="ipad">iPad</button>
                <button data-filter="mac">Mac</button>
                <button data-filter="accessories">Phụ kiện</button>
            </div>
            <div class="flash-sale-products-container">
                <div class="product-grid">
                    <?php foreach ($products as $index => $product): ?>
                        <?php if ($index < 4): // Giới hạn 4 sản phẩm cho flash sale ?>
                            <div class="flash-sale-product" data-category="<?= htmlspecialchars(strtolower($product['category'] ?? $product['name'])) ?>" style="--product-index: <?= $index; ?>">
                                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <div class="flash-sale-product-content">
                                    <h5><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="price">$<?= number_format($product['price'], 2) ?></p>
                                    <p class="stock"><?= $product['stock'] > 0 ? "Còn {$product['stock']} sản phẩm" : "Hết hàng" ?></p>
                                    <?php if ($product['stock'] > 0): ?>
                                        <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="btn">Thêm vào giỏ</a>
                                    <?php endif; ?>
                                    <span class="discount-badge">-15%</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flash-sale-navigation">
                <button id="prev-flash-sale" disabled><i class="fas fa-chevron-left"></i></button>
                <span class="page-indicator">1/1</span>
                <button id="next-flash-sale"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="featured-products-section section-padding">
            <h2 class="section-title">Sản phẩm nổi bật</h2>
            <div class="featured-products-filter">
                <button class="active" data-filter="all">Tất cả</button>
                <button data-filter="iphone">iPhone</button>
                <button data-filter="ipad">iPad</button>
                <button data-filter="mac">Mac</button>
                <button data-filter="accessories">Phụ kiện</button>
            </div>
            <div class="product-grid">
                <?php foreach ($products as $index => $product): ?>
                    <div class="featured-product" data-category="<?= htmlspecialchars(strtolower($product['category'] ?? $product['name'])) ?>" style="--product-index: <?= $index; ?>">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="featured-product-content">
                            <h5><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="price">$<?= number_format($product['price'], 2) ?></p>
                            <?php if ($product['stock'] > 0): ?>
                                <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="btn">Thêm vào giỏ</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="featured-products-navigation">
                <button id="prev-featured" disabled><i class="fas fa-chevron-left"></i></button>
                <span class="page-indicator">1/1</span>
                <button id="next-featured"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>

        <!-- Product Showcase Section -->
        <section class="product-showcase section-padding">
            <div class="product-image">
                <img src="assets/images/iphone15.jpg" alt="iPhone 15" class="product-img">
                <div class="image-overlay">New Arrival</div>
            </div>
            <div class="product-info">
                <h1 class="product-title">iPhone 15 Series</h1>
                <h2 class="product-subtitle">Kiệt tác tương lai</h2>
                <ul class="feature-list">
                    <li><span class="feature-icon">⚡️</span> Chip A17 Pro</li>
                    <li><span class="feature-icon">📸</span> Camera 5x Zoom</li>
                    <li><span class="feature-icon">🔌</span> USB-C Siêu Tốc</li>
                    <li><span class="feature-icon">🖥️</span> Super Retina XDR</li>
                    <li><span class="feature-icon">🔋</span> Pin 15 giờ</li>
                </ul>
                <p class="price">Chỉ từ: <span class="price-highlight">34.990.000₫</span></p>
                <a href="products.php?category=iphone" class="buy-now-btn">Sở hữu ngay!</a>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="why-choose-us section-padding">
            <h2 class="section-title">Tại sao chọn Anh Em Rọt Store?</h2>
            <div class="features-grid">
                <div class="feature-item" style="--delay: 0.1s">
                    <i class="fas fa-check-circle feature-icon"></i>
                    <h4>Chính hãng 100%</h4>
                    <p>Sản phẩm Apple nguyên seal, bảo hành toàn cầu, chất lượng đỉnh cao.</p>
                </div>
                <div class="feature-item" style="--delay: 0.2s">
                    <i class="fas fa-truck feature-icon"></i>
                    <h4>Giao hàng siêu tốc</h4>
                    <p>Đưa sản phẩm đến tay bạn trong 24h, miễn phí lắp đặt.</p>
                </div>
                <div class="feature-item" style="--delay: 0.3s">
                    <i class="fas fa-tags feature-icon"></i>
                    <h4>Giá siêu hời</h4>
                    <p>Giá tốt nhất thị trường, ưu đãi độc quyền mỗi ngày.</p>
                </div>
                <div class="feature-item" style="--delay: 0.4s">
                    <i class="fas fa-headset feature-icon"></i>
                    <h4>Hỗ trợ 24/7</h4>
                    <p>Chuyên gia luôn sẵn sàng, giải đáp mọi lúc, mọi nơi.</p>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section section-padding">
            <h2 class="section-title">Khách hàng nói gì?</h2>
            <div class="testimonial-carousel">
                <div class="carousel-inner">
                    <div class="testimonial-card">
                        <div class="rating">★★★★★</div>
                        <p>"Anh Em Rọt Store mang đến trải nghiệm mua sắm đỉnh cao! Giao hàng nhanh, sản phẩm xịn, tư vấn siêu nhiệt tình!"</p>
                        <div class="customer-info">
                            <img src="assets/images/customer1.jpg" alt="Nguyễn Văn A">
                            <div>
                                <p class="name">Nguyễn Văn A</p>
                                <p class="title">Chủ doanh nghiệp</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="rating">★★★★★</div>
                        <p>"MacBook Pro từ Anh Em Rọt Store quá hoàn hảo! Dịch vụ chăm sóc khách hàng khiến tôi muốn quay lại mãi."</p>
                        <div class="customer-info">
                            <img src="assets/images/customer2.jpg" alt="Lê Thị B">
                            <div>
                                <p class="name">Lê Thị B</p>
                                <p class="title">Thiết kế đồ họa</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial-card">
                        <div class="rating">★★★★★</div>
                        <p>"Giá tốt, sản phẩm chính hãng, bảo hành uy tín. Anh Em Rọt Store là nơi đáng tin cậy nhất!"</p>
                        <div class="customer-info">
                            <img src="assets/images/customer3.jpg" alt="Phạm Minh C">
                            <div>
                                <p class="name">Phạm Minh C</p>
                                <p class="title">Sinh viên</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-prev"><i class="fas fa-chevron-left"></i></button>
            <button class="carousel-next"><i class="fas fa-chevron-right"></i></button>
        </section>

        <!-- Blog Section -->
        <section class="blog-section section-padding">
            <h2 class="section-title">Tin tức mới nhất</h2>
            <div class="blog-grid" id="blog-grid">
                <?php foreach ($news_items as $news): ?>
                    <div class="blog-post-card">
                        <img src="<?= htmlspecialchars($news['image_url']) ?>" alt="<?= htmlspecialchars($news['title']) ?>">
                        <div class="blog-content">
                            <div class="post-meta"><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars(date('d/m/Y', strtotime($news['created_date']))) ?></div>
                            <h4><?= htmlspecialchars($news['title']) ?></h4>
                            <p><?= htmlspecialchars(substr($news['content'], 0, 100)) . '...' ?></p>
                            <a href="news_detail.php?id=<?= $news['id'] ?>" class="read-more">Đọc thêm</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="pagination">
                <button id="prev-page" <?= $page <= 1 ? 'disabled' : '' ?>>❮ Trước</button>
                <span id="page-indicator"><?= $page ?></span>
                <button id="next-page" <?= $page >= $total_pages ? 'disabled' : '' ?>>Tiếp ❯</button>
            </div>
            <div class="view-all-container">
                <a href="news.php" class="view-all-btn">Xem tất cả</a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <button class="back-to-top"><i class="fas fa-arrow-up"></i></button>
    <script src="scripts/main.js"></script>
</body>
</html>