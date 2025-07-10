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
    <script href=""></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero slider section (updated to match index.html structure) -->
        <section class="hero-slider">
            <div class="slides-container">
                <!-- Add slides dynamically if needed; for now, static like index.html -->
                <div class="slide active" style="background-image: url('assets/images/hero1.jpg');">
                    <div class="hero-content">
                        <h1>Đỉnh cao công nghệ</h1>
                        <p>Kiệt tác tương lai trong tay bạn</p>
                        <div class="hero-buttons">
                            <a href="products.php?category=iphone" class="btn btn-primary">Sở hữu ngay!</a>
                        </div>
                    </div>
                </div>
                <!-- Add more slides as needed -->
            </div>
            <div class="slider-controls">
                <button class="prev-slide"><i class="fas fa-chevron-left"></i></button>
                <div class="slide-indicators">
                    <!-- Indicators will be added dynamically -->
                </div>
                <button class="next-slide"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>

        <!-- Categories section -->
        <section class="categories">
            <h2>Khám phá các Danh mục</h2>
            <div class="categories__grid">
                <div class="category-card">
                    <img src="assets/images/iphone.jpg" alt="iPhone">
                    <h3>iPhone</h3>
                    <a href="products.php?category=iphone" class="category-link">Xem ngay</a>
                </div>
                <div class="category-card">
                    <img src="assets/images/mac.jpg" alt="Mac">
                    <h3>Mac</h3>
                    <a href="products.php?category=mac" class="category-link">Xem ngay</a>
                </div>
                <div class="category-card">
                    <img src="assets/images/ipad.jpg" alt="iPad">
                    <h3>iPad</h3>
                    <a href="products.php?category=ipad" class="category-link">Xem ngay</a>
                </div>
                <div class="category-card">
                    <img src="assets/images/watch.jpg" alt="Watch">
                    <h3>Watch</h3>
                    <a href="products.php?category=watch" class="category-link">Xem ngay</a>
                </div>
                <div class="category-card">
                    <img src="assets/images/airpods.jpg" alt="AirPods">
                    <h3>AirPods</h3>
                    <a href="products.php?category=accessories" class="category-link">Xem ngay</a>
                </div>
                <div class="category-card">
                    <img src="assets/images/accessories.jpg" alt="Phụ kiện">
                    <h3>Phụ kiện</h3>
                    <a href="products.php?category=accessories" class="category-link">Xem ngay</a>
                </div>
            </div>
        </section>

        <!-- Flash Sale section -->
        <section class="flash-sale">
            <h2>Flash Sale - Giảm giá sốc</h2>
            <div class="flash-sale__timer">
                <p>Kết thúc sau: <span id="timer">00:00:00</span></p>
            </div>
            <div class="flash-sale__products">
                <?php foreach ($products as $index => $product): ?>
                    <?php if ($index < 4): // Giới hạn 4 sản phẩm cho flash sale ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="price">$<?= number_format($product['price'], 2) ?></p>
                            <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="product-button">Thêm vào giỏ</a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Featured Products section -->
        <section class="featured-products">
            <h2>Sản phẩm nổi bật</h2>
            <div class="filter-tabs">
                <a href="products.php" class="tab active">Tất cả</a>
                <a href="products.php?category=iphone" class="tab">iPhone</a>
                <a href="products.php?category=ipad" class="tab">iPad</a>
                <a href="products.php?category=mac" class="tab">MacBook</a>
                <a href="products.php?category=accessories" class="tab">AirPods</a>
                <a href="products.php?category=accessories" class="tab">Phụ kiện</a>
            </div>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p class="price">$<?= number_format($product['price'], 2) ?></p>
                        <p class="stock"><?= $product['stock'] > 0 ? "Còn {$product['stock']} sản phẩm" : "Hết hàng" ?></p>
                        <?php if ($product['stock'] > 0): ?>
                            <a href="cart.php?action=add&id=<?= $product['id'] ?>" class="product-button">Thêm vào giỏ</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Product Showcase section (added to match index.html) -->
        <section class="product-showcase">
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
                <button class="buy-now-btn">Sở hữu ngay!</button>
            </div>
        </section>

        <!-- Why Choose Us section -->
        <section class="why-choose-us">
            <h2>Tại sao chọn Anh Em Rọt Store?</h2>
            <div class="why-choose-us__grid">
                <div class="reason-card">
                    <img src="assets/images/authentic.png" alt="Chính hãng">
                    <h3>Chính hãng 100%</h3>
                    <p>Sản phẩm Apple nguyên seal, bảo hành toàn cầu, chất lượng đỉnh cao.</p>
                </div>
                <div class="reason-card">
                    <img src="assets/images/delivery.png" alt="Giao hàng">
                    <h3>Giao hàng siêu tốc</h3>
                    <p>Đưa sản phẩm đến tay bạn trong 24h, miễn phí lắp đặt.</p>
                </div>
                <div class="reason-card">
                    <img src="assets/images/price.png" alt="Giá tốt">
                    <h3>Giá siêu hời</h3>
                    <p>Giá tốt nhất thị trường, ưu đãi độc quyền mỗi ngày.</p>
                </div>
                <div class="reason-card">
                    <img src="assets/images/support.png" alt="Hỗ trợ">
                    <h3>Hỗ trợ 24/7</h3>
                    <p>Chuyên gia luôn sẵn sàng, giải đáp mọi lúc, mọi nơi.</p>
                </div>
            </div>
        </section>

        <!-- Testimonials section -->
        <section class="testimonials">
            <h2>Khách hàng nói gì?</h2>
            <div class="testimonials__carousel">
                <div class="testimonial-card">
                    <div class="rating">★★★★★</div>
                    <p>"Anh Em Rọt Store mang đến trải nghiệm mua sắm đỉnh cao! Giao hàng nhanh, sản phẩm xịn, tư vấn siêu nhiệt tình!"</p>
                    <div class="author">
                        <p>Nguyễn Văn A</p>
                        <span>Chủ doanh nghiệp</span>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="rating">★★★★★</div>
                    <p>"MacBook Pro từ Anh Em Rọt Store quá hoàn hảo! Dịch vụ chăm sóc khách hàng khiến tôi muốn quay lại mãi."</p>
                    <div class="author">
                        <p>Lê Thị B</p>
                        <span>Thiết kế đồ họa</span>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="rating">★★★★★</div>
                    <p>"Giá tốt, sản phẩm chính hãng, bảo hành uy tín. Anh Em Rọt Store là nơi đáng tin cậy nhất!"</p>
                    <div class="author">
                        <p>Phạm Minh C</p>
                        <span>Sinh viên</span>
                    </div>
                </div>
            </div>
            <div class="carousel-controls">
                <button class="prev"><i class="fas fa-chevron-left"></i></button>
                <button class="next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </section>

        <!-- News section (renamed to Blog section to match index.html) -->
        <section class="blog-section">
            <h2>Tin tức mới nhất</h2>
            <div class="blog-grid">
                <!-- Tin tức tĩnh, có thể thay bằng động -->
                <div class="blog-post-card">
                    <img src="assets/images/news1.jpg" alt="Tin tức">
                    <div class="blog-content">
                        <div class="post-meta"><i class="fas fa-calendar-alt"></i> 10/07/2025</div>
                        <h4>iPhone 16 ra mắt</h4>
                        <p>Khám phá những tính năng mới của iPhone 16!</p>
                        <a href="news.php" class="read-more">Đọc thêm</a>
                    </div>
                </div>
                <div class="blog-post-card">
                    <img src="assets/images/news2.jpg" alt="Tin tức">
                    <div class="blog-content">
                        <div class="post-meta"><i class="fas fa-calendar-alt"></i> 10/07/2025</div>
                        <h4>MacBook M3 sắp ra mắt</h4>
                        <p>Hiệu năng vượt trội với chip M3 mới!</p>
                        <a href="news.php" class="read-more">Đọc thêm</a>
                    </div>
                </div>
            </div>
            <div class="pagination">
                <button id="prev-page" class="disabled">❮ Trước</button>
                <span id="page-indicator">1</span>
                <button id="next-page">Tiếp ❯</button>
            </div>
            <div class="view-all-container">
                <a href="news.php" class="view-all-btn">Xem tất cả</a>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="scripts/main.js"></script>
    <script>
        // JavaScript cho Flash Sale timer
        function startTimer() {
            const timer = document.getElementById('timer');
            let time = 3600; // 1 giờ
            setInterval(() => {
                let hours = Math.floor(time / 3600);
                let minutes = Math.floor((time % 3600) / 60);
                let seconds = time % 60;
                timer.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                time--;
                if (time < 0) time = 3600;
            }, 1000);
        }
        startTimer();

        // JavaScript cho carousel testimonials
        const carousel = document.querySelector('.testimonials__carousel');
        const prev = document.querySelector('.carousel-controls .prev');
        const next = document.querySelector('.carousel-controls .next');
        let currentIndex = 0;

        function updateCarousel() {
            const width = carousel.querySelector('.testimonial-card').offsetWidth;
            carousel.style.transform = `translateX(-${currentIndex * width}px)`;
        }

        prev.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });

        next.addEventListener('click', () => {
            if (currentIndex < carousel.querySelectorAll('.testimonial-card').length - 1) {
                currentIndex++;
                updateCarousel();
            }
        });

    </script>
</body>
</html>