// Simulated authentication state
let isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
let currentUser = JSON.parse(localStorage.getItem('currentUser')) || null;

// Cache for product data and cart items
let productDataCache = null;
let isFetching = false;
let cartItems = getCartFromStorage();

function getCartFromStorage() {
    // Try to get from new cart system first
    const cartSystemData = localStorage.getItem('cartSystem');
    if (cartSystemData) {
        try {
            const cartSystem = JSON.parse(cartSystemData);
            return cartSystem.items.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                imageUrl: item.image,
                quantity: item.quantity,
                storage: item.storage,
                color: item.color
            }));
        } catch (e) {
            console.error('Error parsing cartSystem:', e);
        }
    }
    // Fallback to old cart format
    const cartData = localStorage.getItem('cart');
    return cartData ? JSON.parse(cartData) : [];
}

const message = "📢Anh Em Rọt Store - Proud of you! ";
const el = document.getElementById("typewriter");
const sound = document.getElementById("type-sound");

let i = 0;
function typeChar() {
    if (i < message.length) {
        el.textContent += message[i++];
        if (sound) {
            sound.currentTime = 0;
        }
        setTimeout(typeChar, 80);
    }
}
typeChar();

// Giả lập thời tiết
const fakeWeatherData = [
    { icon: "☀️", city: "Hà Nội", temp: 33, desc: "Nắng đẹp" },
    { icon: "⛅", city: "Đà Nẵng", temp: 30, desc: "Trời quang mây" },
    { icon: "🌧️", city: "TP.HCM", temp: 28, desc: "Mưa nhẹ" },
    { icon: "🌤️", city: "Hải Phòng", temp: 29, desc: "Nắng nhẹ" },
    { icon: "🌩️", city: "Cần Thơ", temp: 27, desc: "Giông bão" }
];

function showFakeWeather() {
    const data = fakeWeatherData[Math.floor(Math.random() * fakeWeatherData.length)];
    const weatherEl = document.getElementById("weather");
    if (weatherEl) {
        weatherEl.textContent = `${data.icon} ${data.city}: ${data.temp}°C - ${data.desc}`;
    }
}

showFakeWeather();
setInterval(showFakeWeather, 30000);

// Function to show notifications
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' :
            type === 'error' ? '#f44336' :
                type === 'warning' ? '#ff9800' : '#2196f3'};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 10000;
        font-weight: 500;
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        max-width: 350px;
        `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);

    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Function to update user section
function updateUserSection() {
    const userSection = document.getElementById('user-section');
    if (!userSection) return;

    if (isLoggedIn && currentUser) {
        userSection.innerHTML = `
            <div class="user-dropdown">
                <div class="user-icon" aria-label="Tài khoản người dùng">
                    <i class="fas fa-user"></i>
                    <span class="user-name">${currentUser.fullName.split(' ')[0]}</span>
                </div>
                <div class="dropdown-content">
                    <a href="#"><i class="fas fa-user-circle"></i> Thông tin tài khoản</a>
                    <a href="#"><i class="fas fa-shopping-cart"></i> Giỏ hàng của tôi</a>
                    <a href="#" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </div>
            </div>
        `;
        const userDropdown = userSection.querySelector('.user-dropdown');
        if (userDropdown) {
            document.addEventListener('click', (e) => {
                if (!userDropdown.contains(e.target)) {
                    userDropdown.querySelector('.dropdown-content').style.display = 'none';
                }
            });

            userDropdown.querySelector('.user-icon').addEventListener('click', (e) => {
                e.stopPropagation();
                const dropdown = userDropdown.querySelector('.dropdown-content');
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });

            const dropdownItems = userDropdown.querySelectorAll('.dropdown-content a');
            dropdownItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    const action = item.textContent.trim();

                    if (action.includes('Giỏ hàng')) {
                        showNotification('Chuyển hướng đến giỏ hàng...', 'info');
                        setTimeout(() => {
                            window.location.href = '/Apple_Shop/cart.html';
                        }, 1500);
                    } else if (action.includes('Đăng xuất')) {
                        handleLogout();
                    } else if (action.includes('Thông tin')) {
                        showNotification('Chuyển hướng đến trang thông tin tài khoản', 'info');
                        setTimeout(() => {
                            window.location.href = '/Apple_Shop/account.html';
                        }, 1500);
                    }

                    userDropdown.querySelector('.dropdown-content').style.display = 'none';
                });
            });
        }
    } else {
        userSection.innerHTML = `
            <button class="login-btn" id="login-btn">
                <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>Đăng nhập
            </button>
        `;
        const loginBtn = document.getElementById('login-btn');
        if (loginBtn) {
            loginBtn.addEventListener('click', (e) => {
                e.preventDefault();
                showNotification('Chuyển hướng đến trang đăng nhập...', 'info');
                setTimeout(() => {
                    window.location.href = '/Apple_Shop/login.php';
                }, 1500);
            });
        }
    }
}

function handleLogout() {
    showNotification('Đã đăng xuất thành công!', 'success');
    setTimeout(() => {
        isLoggedIn = false;
        currentUser = null;
        localStorage.removeItem('isLoggedIn');
        localStorage.removeItem('currentUser');
        updateUserSection();
    }, 1500);
}

// Listen for cart updates from the Cart class
window.addEventListener('cartUpdated', (event) => {
    cartItems = getCartFromStorage();
    const totalItems = event.detail.count;

    // Update cart count in navbar
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = totalItems;
    }

    // Update cart badge
    const cartBadge = document.getElementById('cart-badge');
    if (cartBadge) {
        cartBadge.textContent = totalItems;
        cartBadge.style.display = totalItems > 0 ? 'flex' : 'none';
    }

    // Update other cart elements
    const cartBadges = document.querySelectorAll('.cart-badge, .badge');
    cartBadges.forEach(badge => {
        if (badge.id !== 'cart-badge') {
            badge.textContent = totalItems;
            badge.style.display = totalItems > 0 ? 'inline' : 'none';
        }
    });
});

// Mega Menu Functionality
// ... (giữ nguyên phần này, chỉ cần đảm bảo các đường dẫn chuyển hướng dùng /Apple_Shop/ nếu cần)
// ...
// (Bạn có thể copy phần Mega Menu, Mobile Nav, Cart Modal, Search Box, ... từ code cũ, chỉ cần sửa các đường dẫn chuyển hướng sang tuyệt đối)

// Export functions for external use
window.NavbarUtils = {
    showNotification,
    updateUserSection,
    getCartFromStorage
}; 