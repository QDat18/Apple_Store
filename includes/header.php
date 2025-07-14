<?php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }
require_once $_SERVER['DOCUMENT_ROOT'] . '/Apple_Shop/config/db.php';

$is_admin_page = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;

if ($is_admin_page && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    include $_SERVER['DOCUMENT_ROOT'] . '/Apple_Shop/admin/header.php';
    return;
}

$user_avatar = '/Apple_Shop/assets/logo/default_avatar.png';
$user_full_name = 'Tài khoản';
$is_user_logged_in = false;

if (isset($_SESSION['user_id'])) {
    $is_user_logged_in = true;
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT u.full_name, ud.avatar
        FROM users u
        LEFT JOIN user_detail ud ON u.id = ud.user_id
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user_data = $result->fetch_assoc()) {
        $user_full_name = htmlspecialchars($user_data['full_name']);
        if (!empty($user_data['avatar']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $user_data['avatar'])) {
            $user_avatar = htmlspecialchars($user_data['avatar']);
        }
    }
    $stmt->close();
}

// Lấy số lượng sản phẩm trong giỏ hàng
$cart_count = 0;
if ($is_user_logged_in) {
    $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($cart_data = $result->fetch_assoc()) {
        $cart_count = (int)$cart_data['total'];
    }
    $stmt->close();
}
?>

<div class="top-bar">
    <div class="top-bar-container">
        <div class="top-bar-left">
            <span><i class="fas fa-envelope"></i> anhemrotstore12chuaboc@hvnh.edu.vn</span>
            <span><i class="fas fa-phone"></i> (+84) 827592304</span>
        </div>
        <div class="top-bar-center">
            <span id="typewriter"></span>
            <span id="weather" class="weather-info">⛅ Đang tải thời tiết...</span>
        </div>
        <div class="top-bar-right">
            <a href="https://www.instagram.com/_yud.gnauq/"><i class="fab fa-instagram"></i> Instagram</a>
            <a href="https://web.facebook.com/quys.hokage"><i class="fab fa-facebook"></i> Facebook</a>
        </div>
    </div>
</div>

<header class="main-header">
    <div class="header-container">
        <div class="header-left">
            <div class="logo">
                <a href="/Apple_Shop/index.php">
                    <img src="/Apple_Shop/assets/logo/logo.png" alt="Apple Store Logo">
                </a>
            </div>
            <nav class="desktop-nav" aria-label="Main navigation">
                <ul>
                    <li><a href="/Apple_Shop/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Trang chủ</a></li>
                    <li class="has-dropdown">
                        <a href="/Apple_Shop/products/products.php" aria-expanded="false" aria-controls="products-dropdown">Sản Phẩm</a>
                        <div id="products-dropdown">
                            <ul>
                                <li><a href="/Apple_Shop/products/iphone.php">📱 iPhone Models</a></li>
                                <li><a href="/Apple_Shop/products/macbook.php">💻 MacBook & Mac</a></li>
                                <li><a href="/Apple_Shop/products/ipad.php">🎯 iPad Models</a></li>
                                <li><a href="/Apple_Shop/products/watch.php">⌚ Apple Watch</a></li>
                                <li><a href="/Apple_Shop/products/accessories.php">🎧 Accessories</a></li>
                            </ul>
                        </div>
                    </li>
                    <li><a href="/Apple_Shop/promotion.php" class="<?= basename($_SERVER['PHP_SELF']) === 'promotion.php' ? 'active' : '' ?>">🛠️ Khuyến mại</a></li>
                    <li><a href="/Apple_Shop/about.php" class="<?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>">ℹ️ Về chúng tôi</a></li>
                    <li><a href="/Apple_Shop/news.php" class="<?= basename($_SERVER['PHP_SELF']) === 'news.php' ? 'active' : '' ?>">📝 Tin tức</a></li>
                    <li><a href="/Apple_Shop/contact.php" class="<?= basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : '' ?>">📞 Liên hệ</a></li>

                    <?php if ($is_user_logged_in): ?>
                        <li class="user-profile-dropdown">
                            <a href="#" class="user-dropdown-toggle" aria-expanded="false" aria-controls="user-dropdown-menu">
                                <img src="<?= $user_avatar ?>" alt="Avatar của <?= htmlspecialchars($user_full_name) ?>" class="user-avatar">
                                <span class="user-name"><?= htmlspecialchars($user_full_name) ?></span>
                            </a>
                            <div class="user-dropdown-menu" id="user-dropdown-menu">
                                <a href="/Apple_Shop/profile.php">👤 Thông tin cá nhân</a>
                                <a href="/Apple_Shop/my_orders.php">📦 Đơn hàng của tôi</a>
                                <a href="/Apple_Shop/wishlist.php">❤️ Sản phẩm yêu thích</a>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <a href="/Apple_Shop/admin/dashboard.php">📊 Bảng điều khiển Admin</a>
                                <?php endif; ?>
                                <a href="/Apple_Shop/logout.php">🚪 Đăng xuất</a>
                            </div>
                        </li>
                    <?php else: ?>
                        <li><a href="/Apple_Shop/login.php" class="<?= basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : '' ?>">Đăng nhập</a></li>
                        <li><a href="/Apple_Shop/register.php" class="<?= basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : '' ?>">Đăng ký</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <button class="mobile-menu-toggle" aria-label="Mở menu di động" aria-expanded="false" aria-controls="mobile-nav">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div class="header-right">
            <div class="search-box">
                <button class="search-toggle" aria-label="Tìm kiếm"><i class="fas fa-search"></i></button>
                <div class="search-input-container">
                    <input type="text" class="search-input" placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm sản phẩm">
                    <div class="search-results-container"></div>
                </div>
            </div>
            <div class="cart-icon" id="cart-icon-desktop">
                <a href="/Apple_Shop/cart.php" aria-label="Giỏ hàng (<?= $cart_count ?> sản phẩm)">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count"><?= $cart_count ?></span>
                </a>
            </div>
        </div>
    </div>
</header>

<div class="mobile-nav" id="mobile-nav">
    <div class="mobile-nav-header">
        <div class="logo">
            <a href="/Apple_Shop/index.php">
                <img src="/Apple_Shop/assets/logo/logo.png" alt="Apple Store Logo">
            </a>
        </div>
        <button class="mobile-menu-close" aria-label="Đóng menu di động">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <ul>
        <li><a href="/Apple_Shop/index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Trang chủ</a></li>
        <li class="has-dropdown">
            <a href="/Apple_Shop/products/products.php" aria-expanded="false" aria-controls="mobile-products-submenu">Sản Phẩm <i class="fas fa-chevron-down dropdown-indicator"></i></a>
            <ul class="sub-menu" id="mobile-products-submenu">
                <li><a href="/Apple_Shop/products/iphone.php">📱 iPhone Models</a></li>
                <li><a href="/Apple_Shop/products/macbook.php">💻 MacBook & Mac</a></li>
                <li><a href="/Apple_Shop/products/ipad.php">🎯 iPad Models</a></li>
                <li><a href="/Apple_Shop/products/watch.php">⌚ Apple Watch</a></li>
                <li><a href="/Apple_Shop/products/accessories.php">🎧 Accessories</a></li>
            </ul>
        </li>
        <li><a href="/Apple_Shop/promotion.php" class="<?= basename($_SERVER['PHP_SELF']) === 'promotion.php' ? 'active' : '' ?>">🛠️ Khuyến mại</a></li>
        <li><a href="/Apple_Shop/about.php" class="<?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>">ℹ️ Về chúng tôi</a></li>
        <li><a href="/Apple_Shop/news.php" class="<?= basename($_SERVER['PHP_SELF']) === 'news.php' ? 'active' : '' ?>">📝 Tin tức</a></li>
        <li><a href="/Apple_Shop/contact.php" class="<?= basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : '' ?>">📞 Liên hệ</a></li>

        <?php if ($is_user_logged_in): ?>
            <li class="user-profile-dropdown mobile">
                <a href="#" class="user-dropdown-toggle" aria-expanded="false" aria-controls="mobile-user-dropdown-menu">
                    <img src="<?= $user_avatar ?>" alt="Avatar của <?= htmlspecialchars($user_full_name) ?>" class="user-avatar">
                    <span class="user-name"><?= htmlspecialchars($user_full_name) ?></span>
                    <i class="fas fa-chevron-down dropdown-indicator"></i>
                </a>
                <div class="user-dropdown-menu" id="mobile-user-dropdown-menu">
                    <a href="/Apple_Shop/profile.php">👤 Thông tin cá nhân</a>
                    <a href="/Apple_Shop/my_orders.php">📦 Đơn hàng của tôi</a>
                    <a href="/Apple_Shop/wishlist.php">❤️ Sản phẩm yêu thích</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/Apple_Shop/admin/dashboard.php">📊 Bảng điều khiển Admin</a>
                    <?php endif; ?>
                    <a href="/Apple_Shop/logout.php">🚪 Đăng xuất</a>
                </div>
            </li>
        <?php else: ?>
            <li><a href="/Apple_Shop/login.php" class="<?= basename($_SERVER['PHP_SELF']) === 'login.php' ? 'active' : '' ?>">Đăng nhập</a></li>
            <li><a href="/Apple_Shop/register.php" class="<?= basename($_SERVER['PHP_SELF']) === 'register.php' ? 'active' : '' ?>">Đăng ký</a></li>
        <?php endif; ?>
        <li class="mobile-cart-item">
            <a href="/Apple_Shop/cart.php">🛒 Giỏ hàng <span class="cart-badge badge"><?= $cart_count ?></span></a>
        </li>
    </ul>
</div>
<div class="mobile-nav-overlay"></div>

<?php

?>