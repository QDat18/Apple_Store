// header.js
document.addEventListener('DOMContentLoaded', () => {
    console.log('Header.js loaded on page:', window.location.pathname);

    // Typewriter Effect (giữ nguyên)
    const messages = [
        "📢 Anh Em Rọt Store - Proud of you!",
        "🔥 Ưu đãi mỗi ngày – đừng bỏ lỡ!",
        "🎉 Mua Apple - Nhận quà liền tay!"
    ];

    const el = document.getElementById("typewriter");
    let messageIndex = 0;
    let charIndex = 0;
    let isDeleting = false;

    function typeWriterEffect() {
        if (!el) {
            console.warn('Typewriter element not found on page:', window.location.pathname);
            return;
        }

        const currentMessage = messages[messageIndex];
        const visibleText = currentMessage.substring(0, charIndex);

        el.textContent = visibleText;

        let typingSpeed = isDeleting ? 40 : 70;

        if (!isDeleting && charIndex === currentMessage.length) {
            typingSpeed = 2000; // chờ 2s trước khi xóa
            isDeleting = true;
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            messageIndex = (messageIndex + 1) % messages.length; // chuyển sang câu tiếp theo
            typingSpeed = 500;
        }

        charIndex += isDeleting ? -1 : 1;

        setTimeout(typeWriterEffect, typingSpeed);
    }

    typeWriterEffect();

    // Weather Fetcher (giữ nguyên)
    const weatherElement = document.getElementById('weather');
    if (weatherElement) {
        function fetchWeather() {
            if (!navigator.geolocation) {
                weatherElement.textContent = 'Trình duyệt không hỗ trợ định vị.';
                console.warn('Geolocation not supported');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                position => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    const apiKey = '22145b24c37c502d0669770e77623110';
                    const WEATHER_URL = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}&lang=vi`;

                    fetch(WEATHER_URL)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            const temp = Math.round(data.main.temp);
                            const description = data.weather[0].description;
                            const city = data.name;
                            weatherElement.innerHTML = `<i class="fas fa-cloud"></i>${city} ,${temp}°C, ${description}`;
                        })
                        .catch(error => {
                            console.error('Error fetching weather:', error);
                            weatherElement.textContent = 'Không thể lấy thông tin thời tiết.';
                        });
                },
                error => {
                    console.error('Geolocation error:', error);
                    weatherElement.textContent = 'Vui lòng cho phép định vị để xem thời tiết.';
                }
            );
        }
        fetchWeather();
        setInterval(fetchWeather, 600000); // Update every 10 minutes
    } else {
        console.warn('Weather element not found on page:', window.location.pathname);
    }

    // Mobile Navigation (giữ nguyên)
    const mobileNavToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileNavOverlay = document.querySelector('.mobile-nav-overlay');
    const mobileNavClose = document.querySelector('.mobile-menu-close');

    if (mobileNavToggle && mobileNav && mobileNavOverlay && mobileNavClose) {
        mobileNav.classList.remove('active');
        mobileNavOverlay.classList.remove('active');
        document.body.classList.remove('no-scroll');
        mobileNavToggle.setAttribute('aria-expanded', 'false');

        mobileNavToggle.addEventListener('click', () => {
            mobileNav.classList.add('active');
            mobileNavOverlay.classList.add('active');
            document.body.classList.add('no-scroll');
            mobileNavToggle.setAttribute('aria-expanded', 'true');
        });

        mobileNavClose.addEventListener('click', () => {
            mobileNav.classList.remove('active');
            mobileNavOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
            mobileNavToggle.setAttribute('aria-expanded', 'false');
        });

        mobileNavOverlay.addEventListener('click', () => {
            mobileNav.classList.remove('active');
            mobileNavOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
            mobileNavToggle.setAttribute('aria-expanded', 'false');
        });

        function initializeMenuState() {
            if (window.innerWidth >= 1024) {
                mobileNav.classList.remove('active');
                mobileNavOverlay.classList.remove('active');
                document.body.classList.remove('no-scroll');
                mobileNavToggle.setAttribute('aria-expanded', 'false');
            }
        }

        initializeMenuState();
        window.addEventListener('resize', initializeMenuState);
    } else {
        console.error('Mobile nav elements missing on page:', window.location.pathname, {
            mobileNavToggle: !!mobileNavToggle,
            mobileNav: !!mobileNav,
            mobileNavOverlay: !!mobileNavOverlay,
            mobileNavClose: !!mobileNavClose
        });
    }

    // Mobile Dropdown (giữ nguyên)
    document.querySelectorAll('.mobile-nav .has-dropdown > a, .mobile-nav .user-profile-dropdown > .user-dropdown-toggle').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const parentLi = this.parentElement;
            const isActive = parentLi.classList.contains('active');

            document.querySelectorAll('.mobile-nav .has-dropdown.active, .mobile-nav .user-profile-dropdown.active').forEach(openDropdown => {
                if (openDropdown !== parentLi) {
                    openDropdown.classList.remove('active');
                    const openSubMenu = openDropdown.querySelector('.sub-menu, .user-dropdown-menu');
                    if (openSubMenu) openSubMenu.style.display = 'none';
                }
            });

            parentLi.classList.toggle('active');
            const subMenu = parentLi.querySelector('.sub-menu, .user-dropdown-menu');
            if (subMenu) {
                subMenu.style.display = parentLi.classList.contains('active') ? 'block' : 'none';
                this.setAttribute('aria-expanded', parentLi.classList.contains('active'));
            }
        });
    });

    // Search Functionality (đã sửa)
    const searchToggle = document.querySelector('.search-toggle');
    const searchInputContainer = document.querySelector('.search-input-container');
    const searchInput = document.querySelector('.search-input');
    const searchResultsContainer = document.querySelector('.search-results-container');

    if (searchToggle && searchInputContainer && searchInput && searchResultsContainer) {
        searchToggle.addEventListener('click', () => {
            searchInputContainer.classList.toggle('active');
            if (searchInputContainer.classList.contains('active')) {
                searchInput.focus();
            } else {
                searchResultsContainer.style.display = 'none';
            }
        });

        searchInput.addEventListener('input', debounce(() => {
            const query = searchInput.value.trim();
            searchResultsContainer.innerHTML = '';
            searchResultsContainer.style.display = 'none';

            if (query.length < 2) {
                return;
            }

            fetch(`/Apple_Shop/search.php?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success' && Array.isArray(data.data) && data.data.length > 0) {
                        searchResultsContainer.style.display = 'block';
                        data.data.forEach(product => {
                            const resultItem = document.createElement('a');
                            resultItem.href = `/Apple_Shop/products/product_detail.php?id=${product.id}`;
                            resultItem.classList.add('search-result-item');
                            resultItem.innerHTML = `
                        <img src="/Apple_Shop/${product.product_image}" alt="${product.product_name}" class="search-result-image">
                        <div class="search-result-info">
                            <span class="search-result-name">${product.product_name}</span>
                            <span class="search-result-price">$${product.price}</span>
                        </div>
                    `;
                            searchResultsContainer.appendChild(resultItem);
                        });
                    } else if (data.status === 'error') {
                        searchResultsContainer.style.display = 'block';
                        searchResultsContainer.innerHTML = `<p class="search-result-error">${data.message || 'Lỗi server. Vui lòng thử lại sau.'}</p>`;
                    } else {
                        searchResultsContainer.style.display = 'block';
                        searchResultsContainer.innerHTML = `<p class="search-result-empty">${data.message || 'Không tìm thấy sản phẩm nào.'}</p>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    searchResultsContainer.style.display = 'block';
                    searchResultsContainer.innerHTML = '<p class="search-result-error">Không thể tải kết quả tìm kiếm. Vui lòng kiểm tra kết nối hoặc thử lại sau.</p>';
                });
        }, 300));

        // Ẩn kết quả khi click ra ngoài
        document.addEventListener('click', (e) => {
            if (!searchInputContainer.contains(e.target) && !searchResultsContainer.contains(e.target)) {
                searchResultsContainer.style.display = 'none';
            }
        });
    } else {
        console.warn('Search elements missing on page:', window.location.pathname, {
            searchToggle: !!searchToggle,
            searchInputContainer: !!searchInputContainer,
            searchInput: !!searchInput,
            searchResultsContainer: !!searchResultsContainer
        });
    }

    // Cart Modal and Count (giữ nguyên)
    const cartIcon = document.getElementById('cart-icon');
    const cartModal = document.getElementById('cartModal');
    const cartModalClose = document.getElementById('cartModalClose');
    const cartModalOverlay = document.getElementById('cartModalOverlay');
    const cartItemCountBadge = document.getElementById('cart-item-count');

    // Đảm bảo updateCartCount luôn đồng bộ với backend
    window.updateCartCount = function (count = null) {
        const cartItemCountBadge = document.getElementById('cart-item-count');
        if (count !== null) {
            if (cartItemCountBadge) {
                cartItemCountBadge.textContent = count;
            }
            return;
        }
        fetch('/Apple_Shop/cart/get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                if (cartItemCountBadge) {
                    cartItemCountBadge.textContent = data.cart_count || 0;
                }
            });
    };

    // Toast helper
    window.showToast = function (msg, type = 'info') {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.position = 'fixed';
            container.style.top = '32px';
            container.style.right = '32px';
            container.style.zIndex = '2000';
            document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = msg;
        container.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300) }, 2500);
    };

    // Khi thêm vào giỏ/wishlist từ bất kỳ nơi nào, gọi updateCartCount() và showToast()
    // ... giữ nguyên fetchCartDetails, debounce, các hiệu ứng khác ...

    function fetchCartDetails() {
        fetch('/Apple_Shop/cart/get_cart_details.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const cartModalBody = document.getElementById('cartModalBody');
                const cartModalTotal = document.getElementById('cartModalTotal');
                cartModalBody.innerHTML = '';
                let total = 0;

                if (data.status === 'success' && data.items.length > 0) {
                    data.items.forEach(item => {
                        const itemHtml = `
                            <div class="cart-modal-item">
                                <img src="/Apple_Shop/assets/images/${item.image}" alt="${item.name}">
                                <div class="item-details">
                                    <h4>${item.name}</h4>
                                    <p>${item.storage} | ${item.color}</p>
                                    <p>${item.quantity} x $${item.price}</p>
                                </div>
                            </div>
                        `;
                        cartModalBody.innerHTML += itemHtml;
                        total += item.quantity * parseFloat(item.price);
                    });
                    cartModalTotal.textContent = `$${total.toFixed(2)}`;
                } else {
                    cartModalBody.innerHTML = '<p>Giỏ hàng trống.</p>';
                    cartModalTotal.textContent = '$0.00';
                }
            })
            .catch(error => {
                console.error('Error fetching cart details:', error);
                document.getElementById('cartModalBody').innerHTML = '<p>Không thể tải chi tiết giỏ hàng.</p>';
                document.getElementById('cartModalTotal').textContent = '$0.00';
            });
    }

    if (cartIcon && cartModal && cartModalClose && cartModalOverlay) {
        cartIcon.addEventListener('click', (e) => {
            e.preventDefault();
            fetchCartDetails();
            cartModal.classList.add('active');
            cartModalOverlay.classList.add('active');
            document.body.classList.add('no-scroll');
        });

        cartModalClose.addEventListener('click', () => {
            cartModal.classList.remove('active');
            cartModalOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });

        cartModalOverlay.addEventListener('click', () => {
            cartModal.classList.remove('active');
            cartModalOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });

        updateCartCount(); // Initial fetch
    } else {
        console.warn('Cart modal elements missing on page:', window.location.pathname, {
            cartIcon: !!cartIcon,
            cartModal: !!cartModal,
            cartModalClose: !!cartModalClose,
            cartModalOverlay: !!cartModalOverlay
        });
    }

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});