<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admincss">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo/logo.png" alt="Logo" class="sidebar-logo">
            <h2>Anh Em Rọt Store</h2>
            <button class="sidebar-toggle-internal"><i class="fas fa-bars"></i></button>
        </div>
        <nav>
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>"><i
                    class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="manage_products.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'manage_products.php' ? 'active' : '' ?>"><i
                    class="fas fa-box"></i> <span>Products</span></a>
            <a href="manage_users.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'manage_users.php' ? 'active' : '' ?>"><i
                    class="fas fa-users"></i> <span>Users</span></a>
            <a href="manage_orders.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'manage_orders.php' ? 'active' : '' ?>"><i
                    class="fas fa-shopping-cart"></i> <span>Orders</span></a>
            <a href="manage_categories.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'manage_categories.php' ? 'active' : '' ?>"><i
                    class="fas fa-list"></i> <span>Categories</span></a>
            <a href="manage_purchase_orders.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'manage_purchase_orders.php' ? 'active' : '' ?>"><i
                    class="fas fa-file-invoice"></i> <span>Purchase Orders</span></a>
            <a href="manage_inventory.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'manage_inventory.php' ? 'active' : '' ?>"><i
                    class="fas fa-warehouse"></i> <span>Inventory</span></a>
            <a href="manage_suppliers.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'manage_suppliers.php' ? 'active' : '' ?>"><i
                    class="fas fa-truck"></i> <span>Suppliers</span></a>
            <a href="send_email_to_users.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'send_email_to_users.php' ? 'active' : '' ?>"><i
                    class="fas fa-envelope"></i> <span>Email to User</span></a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span></a>
        </nav>
    </div>

    <div class="admin-container">
        <header class="admin-topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle-master"><i class="fas fa-bars"></i></button>
                <h1 class="admin-title">Admin Panel</h1>
            </div>
            <div class="topbar-center">
                <div id="datetime-display"></div>
                <div id="weather-display">
                    <i class="fas fa-cloud-sun"></i> Đang tải thời tiết...
                </div>
            </div>
            <div class="topbar-right">
                <div class="notification-icon-container">
                    <i class="fas fa-bell notification-icon"></i>
                    <span class="notification-badge" id="notification-count">0</span>
                    <div class="notification-dropdown" id="notification-dropdown">
                        <h4>Thông báo mới</h4>
                        <ul id="notification-list">
                            <li>Không có thông báo mới.</li>
                        </ul>
                        <a href="#" class="view-all-notifications">Xem tất cả</a>
                    </div>
                </div>
                <div class="user-info">
                    <span class="user-name">Xin chào, <?=htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                    <a href="../logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </header>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const sidebar = document.querySelector('.sidebar');
                const adminContainer = document.querySelector('.admin-container');
                const masterToggleButton = document.querySelector('.sidebar-toggle-master'); // Nút trên topbar
                const internalSidebarToggleButton = document.querySelector('.sidebar-toggle-internal'); // Nút trong sidebar

                // --- Xử lý trạng thái ban đầu của Sidebar và khi thay đổi kích thước cửa sổ ---
                function handleSidebarState() {
                    if (window.innerWidth <= 1024) { // Chế độ mobile
                        sidebar.classList.remove('collapsed');
                        adminContainer.classList.remove('sidebar-collapsed');
                        sidebar.classList.remove('active');
                    } else { // Chế độ desktop
                        sidebar.classList.remove('active'); // Đảm bảo không có class 'active' của mobile
                        // Không remove 'collapsed' hoặc 'sidebar-collapsed' để duy trì trạng thái nếu người dùng đã thu gọn trước đó
                    }
                }

                handleSidebarState(); // Gọi lần đầu khi DOM được tải
                window.addEventListener('resize', handleSidebarState); // Gọi khi kích thước cửa sổ thay đổi

                // --- Logic cho nút Master Toggle (trên Topbar) ---
                if (masterToggleButton) {
                    masterToggleButton.addEventListener('click', () => {
                        if (window.innerWidth > 1024) { // Desktop: Thu gọn/mở rộng icon
                            sidebar.classList.toggle('collapsed');
                            adminContainer.classList.toggle('sidebar-collapsed');
                        } else { // Mobile: Trượt vào/ra
                            sidebar.classList.toggle('active');
                        }
                    });
                }

                // --- Logic cho nút Internal Sidebar Toggle (trong Sidebar) ---
                if (internalSidebarToggleButton) {
                    internalSidebarToggleButton.addEventListener('click', () => {
                        if (window.innerWidth <= 1024) { // Mobile: Chỉ đóng sidebar
                            sidebar.classList.remove('active');
                        } else { // Desktop: Cũng toggle thu gọn/mở rộng icon
                            sidebar.classList.toggle('collapsed');
                            adminContainer.classList.toggle('sidebar-collapsed');
                        }
                    });
                }

                // --- Highlight active menu item (Logic cũ, giữ nguyên) ---
                const currentPath = window.location.pathname.split('/').pop();
                document.querySelectorAll('.sidebar nav a').forEach(link => {
                    const linkHref = link.getAttribute('href').split('/').pop();
                    if (linkHref === currentPath) {
                        link.classList.add('active');
                    }
                });

                function updateDateTime() {
                    const now = new Date();
                    const options = {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
                        hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
                    };
                    document.getElementById('datetime-display').textContent = now.toLocaleDateString('vi-VN', options);
                }
                setInterval(updateDateTime, 1000);
                updateDateTime();

                // --- Weather API Integration (Logic cũ, giữ nguyên) ---
                const WEATHER_API_KEY = '12b7516bf68adf45f9bcd10db38ecca3';
                const CITY_NAME = 'Hanoi';

                async function fetchWeatherData() {
                    if (WEATHER_API_KEY !== '12b7516bf68adf45f9bcd10db38ecca3') {
                        document.getElementById('weather-display').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Cần API Key thời tiết';
                        return;
                    }

                    try {
                        const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${CITY_NAME}&appid=${WEATHER_API_KEY}&units=metric&lang=vi`);
                        const data = await response.json();

                        if (data.cod === 200) {
                            const weatherIconCode = data.weather[0].icon;
                            const weatherDescription = data.weather[0].description;
                            const temperature = Math.round(data.main.temp);
                            const iconUrl = `http://openweathermap.org/img/wn/${weatherIconCode}.png`;
                            document.getElementById('weather-display').innerHTML = `
                            <img src="${iconUrl}" alt="${weatherDescription}" style="width: 30px; height: 30px; vertical-align: middle;">
                        ${CITY_NAME}, ${temperature}°C, ${weatherDescription}
                        `;
                        } else {
                            document.getElementById('weather-display').innerHTML = `<i class="fas fa-times-circle"></i> Lỗi thời tiết: ${data.message}`;
                        }
                    } catch (error) {
                        console.error("Lỗi lấy dữ liệu thời tiết:", error);
                        document.getElementById('weather-display').innerHTML = '<i class="fas fa-times-circle"></i> Không lấy được thời tiết';
                    }
                }
                fetchWeatherData();
                setInterval(fetchWeatherData, 3600000);

                // --- Notification System (Logic cũ, giữ nguyên) ---
                const notificationIconContainer = document.querySelector('.notification-icon-container');
                const notificationDropdown = document.getElementById('notification-dropdown');
                const notificationCountSpan = document.getElementById('notification-count');
                const notificationList = document.getElementById('notification-list');

                notificationIconContainer.addEventListener('click', (event) => {
                    notificationDropdown.classList.toggle('show');
                    event.stopPropagation();
                });

                document.addEventListener('click', (event) => {
                    if (!notificationIconContainer.contains(event.target) && notificationDropdown.classList.contains('show')) {
                        notificationDropdown.classList.remove('show');
                    }
                });

                async function fetchNotifications() {
                    try {
                        const response = await fetch('get_notifications.php');
                        const data = await response.json();

                        if (data.count > 0) {
                            notificationCountSpan.textContent = data.count;
                            notificationCountSpan.style.display = 'block';
                            notificationList.innerHTML = '';
                            data.notifications.forEach(notif => {
                                const li = document.createElement('li');
                                li.textContent = notif;
                                notificationList.appendChild(li);
                            });
                        } else {
                            notificationCountSpan.textContent = '0';
                            notificationCountSpan.style.display = 'none';
                            notificationList.innerHTML = '<li>Không có thông báo mới.</li>';
                        }
                    } catch (error) {
                        console.error("Lỗi lấy thông báo:", error);
                        notificationCountSpan.textContent = '0';
                        notificationCountSpan.style.display = 'none';
                        notificationList.innerHTML = '<li>Không lấy được thông báo.</li>';
                    }
                }

                fetchNotifications();
                setInterval(fetchNotifications, 60000);
            });
        </script>
</body>

</html>