// header.js

document.addEventListener('DOMContentLoaded', () => {
    console.log('Header.js loaded on page:', window.location.pathname); // Debug: Log current page

    // Typewriter Effect
    const message = "📢 Anh Em Rọt Store - Proud of you! ";
    const el = document.getElementById("typewriter");
    let i = 0;
    if (el) {
        function typeChar() {
            if (i < message.length) {
                el.textContent += message[i];
                i++;
                setTimeout(typeChar, 70);
            } else {
                setTimeout(() => {
                    el.textContent = '';
                    i = 0;
                    typeChar();
                }, 3000);
            }
        }
        typeChar();
    } else {
        console.warn('Typewriter element not found on page:', window.location.pathname); // Debug
    }

    // Weather Fetcher
    const weatherElement = document.getElementById('weather');
    if (weatherElement) {
        function fetchWeather() {
            if (!navigator.geolocation) {
                weatherElement.textContent = 'Trình duyệt không hỗ trợ định vị.';
                console.warn('Geolocation not supported'); // Debug
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
                            weatherElement.innerHTML = `<i class="fas fa-cloud"></i> ${temp}°C, ${description}`;
                        })
                        .catch(error => {
                            console.error('Error fetching weather:', error);
                            weatherElement.textContent = 'Không thể lấy thông tin thời tiết.';
                        });
                },
                error => {
                    console.error('Geolocation error:', error); // Debug
                    weatherElement.textContent = 'Vui lòng cho phép định vị để xem thời tiết.';
                }
            );
        }
        fetchWeather(); // Initial fetch
        setInterval(fetchWeather, 600000); // Update every 10 minutes (600,000 ms)
    } else {
        console.warn('Weather element not found on page:', window.location.pathname); // Debug
    }

    // Mobile Navigation
    const mobileNavToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNav = document.querySelector('.mobile-nav');
    const mobileNavOverlay = document.querySelector('.mobile-nav-overlay');
    const mobileNavClose = document.querySelector('.mobile-menu-close');

    if (mobileNavToggle && mobileNav && mobileNavOverlay && mobileNavClose) {
        console.log('Mobile nav elements found on page:', window.location.pathname); // Debug

        // Ensure mobile nav is hidden on page load
        mobileNav.classList.remove('active');
        mobileNavOverlay.classList.remove('active');
        document.body.classList.remove('no-scroll');
        mobileNavToggle.setAttribute('aria-expanded', 'false');

        mobileNavToggle.addEventListener('click', () => {
            console.log('Mobile menu toggle clicked on page:', window.location.pathname); // Debug
            mobileNav.classList.add('active');
            mobileNavOverlay.classList.add('active');
            document.body.classList.add('no-scroll');
            mobileNavToggle.setAttribute('aria-expanded', 'true');
        });

        mobileNavClose.addEventListener('click', () => {
            console.log('Mobile menu close clicked on page:', window.location.pathname); // Debug
            mobileNav.classList.remove('active');
            mobileNavOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
            mobileNavToggle.setAttribute('aria-expanded', 'false');
        });

        mobileNavOverlay.addEventListener('click', () => {
            console.log('Mobile nav overlay clicked on page:', window.location.pathname); // Debug
            mobileNav.classList.remove('active');
            mobileNavOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
            mobileNavToggle.setAttribute('aria-expanded', 'false');
        });

        // Initialize menu state based on screen size
        function initializeMenuState() {
            if (window.innerWidth >= 1024) {
                mobileNav.classList.remove('active');
                mobileNavOverlay.classList.remove('active');
                document.body.classList.remove('no-scroll');
                mobileNavToggle.setAttribute('aria-expanded', 'false');
                console.log('Initialized for desktop: mobile nav hidden on page:', window.location.pathname); // Debug
            } else {
                console.log('Initialized for mobile: mobile nav toggle enabled on page:', window.location.pathname); // Debug
            }
        }

        // Run on page load
        initializeMenuState();

        // Update on resize
        window.addEventListener('resize', () => {
            initializeMenuState();
        });
    } else {
        console.error('Mobile nav elements missing on page:', window.location.pathname, {
            mobileNavToggle: !!mobileNavToggle,
            mobileNav: !!mobileNav,
            mobileNavOverlay: !!mobileNavOverlay,
            mobileNavClose: !!mobileNavClose
        }); // Debug
    }

    // Mobile Dropdown
    document.querySelectorAll('.mobile-nav .has-dropdown > a, .mobile-nav .user-profile-dropdown > .user-dropdown-toggle').forEach(item => {
        item.addEventListener('click', function(e) {
            console.log('Mobile dropdown clicked on page:', window.location.pathname, item.textContent); // Debug
            e.preventDefault();
            const parentLi = this.parentElement;
            const isActive = parentLi.classList.contains('active');

            // Close other open dropdowns
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

    // Search Functionality
    const searchToggle = document.querySelector('.search-toggle');
    const searchInputContainer = document.querySelector('.search-input-container');
    const searchInput = document.querySelector('.search-input');
    const searchResultsContainer = document.querySelector('.search-results-container');

    if (searchToggle && searchInputContainer && searchInput && searchResultsContainer) {
        console.log('Search elements found on page:', window.location.pathname); // Debug

        searchToggle.addEventListener('click', () => {
            console.log('Search toggle clicked on page:', window.location.pathname); // Debug
            searchInputContainer.classList.toggle('active');
            if (searchInputContainer.classList.contains('active')) {
                searchInput.focus();
            }
        });

        searchInput.addEventListener('input', debounce(() => {
            const query = searchInput.value.trim();
            console.log('Search query on page:', window.location.pathname, query); 

            if (query.length < 2) {
                searchResultsContainer.innerHTML = '';
                return;
            }

            fetch(`/Apple_Shop/search.php?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    searchResultsContainer.innerHTML = ''; 
                    if (data.length > 0) {
                        data.forEach(product => {
                            const resultItem = document.createElement('a');
                            resultItem.href = `/Apple_Shop/products/product_detail.php?id=${product.id}`;
                            resultItem.classList.add('search-result-item');
                            resultItem.innerHTML = `
                                <img src="/Apple_Shop/assets/products/${product.image}" alt="${product.name}">
                                <span>${product.name}</span>
                                <span>${product.price}</span>
                            `;
                            searchResultsContainer.appendChild(resultItem);
                        });
                    } else {
                        searchResultsContainer.innerHTML = '<p class="no-results">Không tìm thấy sản phẩm nào.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching search results:', error); // Debug
                    searchResultsContainer.innerHTML = '<p class="no-results">Lỗi khi tải kết quả tìm kiếm.</p>';
                });
        }, 300)); // Debounce for 300ms
    } else {
        console.warn('Search elements missing on page:', window.location.pathname, {
            searchToggle: !!searchToggle,
            searchInputContainer: !!searchInputContainer,
            searchInput: !!searchInput,
            searchResultsContainer: !!searchResultsContainer
        }); // Debug
    }

    // Cart Modal and Count
    const cartIcon = document.getElementById('cart-icon'); // Thêm ID vào thẻ a của giỏ hàng trong header.php nếu chưa có
    const cartModal = document.getElementById('cartModal');
    const cartModalClose = document.getElementById('cartModalClose');
    const cartModalOverlay = document.getElementById('cartModalOverlay');
    const cartItemCountBadge = document.getElementById('cart-item-count'); // Lấy thẻ badge bằng ID

    // Hàm cập nhật số lượng giỏ hàng
    window.updateCartCount = function(count = null) { // Chỉnh sửa để nhận 'count' hoặc fetch
        if (count !== null) {
            // Nếu có giá trị count được truyền vào, sử dụng giá trị đó
            if (cartItemCountBadge) {
                cartItemCountBadge.textContent = count;
            }
            return;
        }

        // Nếu không có giá trị count được truyền, fetch từ server
        fetch('/Apple_Shop/cart/get_cart_count.php') // Tạo file này nếu chưa có
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success' && cartItemCountBadge) {
                    cartItemCountBadge.textContent = data.count;
                }
            })
            .catch(error => {
                console.error('Error fetching cart count:', error);
                if (cartItemCountBadge) {
                    cartItemCountBadge.textContent = '0'; // Đặt về 0 nếu có lỗi
                }
            });
    };

    // Hàm để lấy và hiển thị chi tiết giỏ hàng trong modal
    function fetchCartDetails() {
        fetch('/Apple_Shop/cart/get_cart_details.php') // Tạo file này nếu chưa có
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const cartModalBody = document.getElementById('cartModalBody');
                const cartModalTotal = document.getElementById('cartModalTotal');
                cartModalBody.innerHTML = ''; // Clear previous items
                let total = 0;

                if (data.status === 'success' && data.items.length > 0) {
                    data.items.forEach(item => {
                        const itemHtml = `
                            <div class="cart-modal-item">
                                <img src="../assets/images/${item.image}" alt="${item.name}">
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
            e.preventDefault(); // Prevent default link behavior
            fetchCartDetails(); // Fetch and display cart details when modal opens
            cartModal.classList.add('active');
            cartModalOverlay.classList.add('active');
            document.body.classList.add('no-scroll');
            console.log('Cart modal opened on page:', window.location.pathname); // Debug
        });

        cartModalClose.addEventListener('click', () => {
            console.log('Cart modal close clicked on page:', window.location.pathname); // Debug
            cartModal.classList.remove('active');
            cartModalOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });

        cartModalOverlay.addEventListener('click', () => {
            console.log('Cart modal overlay clicked on page:', window.location.pathname); // Debug
            cartModal.classList.remove('active');
            cartModalOverlay.classList.remove('active');
            document.body.classList.remove('no-scroll');
        });

        // Periodically update cart count (every 10 seconds)
        // updateCartCount(); // Initial fetch
        // setInterval(updateCartCount, 10000);
        // Do this on DOMContentLoaded now
    } else {
        console.warn('Cart modal elements missing on page:', window.location.pathname, {
            cartIcon: !!cartIcon,
            cartModal: !!cartModal,
            cartModalClose: !!cartModalClose,
            cartModalOverlay: !!cartModalOverlay
        }); // Debug
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


document.addEventListener('DOMContentLoaded', () => {
    // Only call updateCartCount if it's defined (i.e., header.js has loaded)
    if (typeof updateCartCount === 'function') {
        updateCartCount();
    }
});