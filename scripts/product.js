// product.js
const selectedStorage = {};
const selectedColor = {};
const selectedVariantId = {};

const productsData = window.products || [];
productsData.forEach(product => {
    selectedStorage[product.id] = product.selected_storage;
    selectedColor[product.id] = product.selected_color;
    selectedVariantId[product.id] = product.variant_id;
});

function updateProductDisplay(productCard, productId) {
    const currentSelectedStorage = selectedStorage[productId] || '';
    const currentSelectedColor = selectedColor[productId] || '';

    fetch('../cart/get_price.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&storage=${encodeURIComponent(currentSelectedStorage)}&color=${encodeURIComponent(currentSelectedColor)}&csrf_token=${encodeURIComponent(document.querySelector('input[name="csrf_token"]').value)}`
    }).then(response => response.json()).then(data => {
        const priceElement = productCard.querySelector('.current-price');
        const stockElement = productCard.querySelector('.product-stock');
        const addToCartBtn = productCard.querySelector('.add-to-cart-btn');
        const addToWishlistBtn = productCard.querySelector('.add-to-wishlist-btn');
        const productFromData = productsData.find(p => p.id == productId);
        const isDiscountedCategory = [1, 2].includes(parseInt(productFromData?.category_id));

        if (data.price && priceElement && stockElement && addToCartBtn) {
            const originalPriceValue = isDiscountedCategory ? (data.price / 0.9) : data.price;
            const priceDisplay = `${data.price.toLocaleString('vi-VN')} VNĐ${isDiscountedCategory ? `<span class="original-price">${originalPriceValue.toLocaleString('vi-VN')} VNĐ</span>` : ''}`;

            priceElement.innerHTML = priceDisplay;
            stockElement.textContent = `${data.stock} sản phẩm`;
            selectedVariantId[productId] = data.variant_id;
            addToCartBtn.dataset.variantId = data.variant_id;
            if (addToWishlistBtn) {
                addToWishlistBtn.dataset.variantId = data.variant_id;
            }

            if (data.stock <= 0) {
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Hết hàng';
                if (addToWishlistBtn) {
                    addToWishlistBtn.disabled = true;
                    addToWishlistBtn.innerHTML = '<i class="fas fa-heart"></i> Hết hàng';
                }
            } else {
                addToCartBtn.disabled = false;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Thêm vào giỏ';
                if (addToWishlistBtn) {
                    addToWishlistBtn.disabled = false;
                    addToWishlistBtn.innerHTML = '<i class="fas fa-heart"></i> Thêm vào danh sách yêu thích';
                }
            }
        } else {
            console.error('Error fetching price for product ID', productId, ':', data.error);
            if (priceElement) priceElement.innerHTML = 'Giá không xác định';
            if (stockElement) stockElement.textContent = '0 sản phẩm';
            if (addToCartBtn) {
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Không có sẵn';
            }
            if (addToWishlistBtn) {
                addToWishlistBtn.disabled = true;
                addToWishlistBtn.innerHTML = '<i class="fas fa-heart"></i> Không có sẵn';
            }
            selectedVariantId[productId] = null;
        }
    }).catch(error => {
        console.error('Error fetching price:', error);
        const priceElement = productCard.querySelector('.current-price');
        const stockElement = productCard.querySelector('.product-stock');
        const addToCartBtn = productCard.querySelector('.add-to-cart-btn');
        const addToWishlistBtn = productCard.querySelector('.add-to-wishlist-btn');
        if (priceElement) priceElement.innerHTML = 'Giá không xác định';
        if (stockElement) stockElement.textContent = '0 sản phẩm';
        if (addToCartBtn) {
            addToCartBtn.disabled = true;
            addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Không có sẵn';
        }
        if (addToWishlistBtn) {
            addToWishlistBtn.disabled = true;
            addToWishlistBtn.innerHTML = '<i class="fas fa-heart"></i> Không có sẵn';
        }
    });
}

function attachOptionButtonListeners() {
    document.querySelectorAll('.option-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const productCard = this.closest('.product-card');
            if (!productCard) return;
            const productId = productCard.dataset.productId;
            const type = this.dataset.type;
            const value = this.dataset.value;

            productCard.querySelectorAll(`.${type}-options .option-btn`).forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            if (type === 'storage') {
                selectedStorage[productId] = value;
            } else {
                selectedColor[productId] = value;
            }

            updateProductDisplay(productCard, productId);
        });
    });

    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const productId = this.dataset.productId;
            const variantId = selectedVariantId[productId];
            const quantity = 1;

            if (!variantId) {
                alert('Vui lòng chọn biến thể sản phẩm.');
                return;
            }

            addToCart(productId, variantId);
        });
    });

    document.querySelectorAll('.add-to-wishlist-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            const productId = this.dataset.productId;
            const variantId = selectedVariantId[productId];

            if (!variantId) {
                alert('Vui lòng chọn biến thể sản phẩm.');
                return;
            }

            addToWishlist(productId, variantId);
        });
    });
}

async function addToCart(productId, variantId) {
    if (!variantId) {
        alert('Vui lòng chọn biến thể sản phẩm trước khi thêm vào giỏ hàng.');
        return;
    }

    try {
        const response = await fetch('../cart/add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `variant_id=${variantId}&quantity=1&csrf_token=${encodeURIComponent(document.querySelector('input[name="csrf_token"]').value)}`
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message);
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        } else {
            alert('Có lỗi khi thêm vào giỏ hàng: ' + data.message);
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        alert('Có lỗi khi thêm vào giỏ hàng.');
    }
}

async function addToWishlist(productId, variantId) {
    if (!variantId) {
        alert('Vui lòng chọn biến thể sản phẩm trước khi thêm vào danh sách yêu thích.');
        return;
    }

    try {
        const response = await fetch('../wishlist/add_to_wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `variant_id=${variantId}&csrf_token=${encodeURIComponent(document.querySelector('input[name="csrf_token"]').value)}`
        });
        const data = await response.json();
        if (data.success) {
            alert(data.message);
        } else {
            alert('Có lỗi khi thêm vào danh sách yêu thích: ' + data.message);
        }
    } catch (error) {
        console.error('Error adding to wishlist:', error);
        alert('Có lỗi khi thêm vào danh sách yêu thích.');
    }
}

function createProductCard(product) {
    const price = parseFloat(product.price) || 0;
    const originalPrice = parseFloat(product.original_price) || price;
    const isDiscounted = [1, 2].includes(parseInt(product.category_id));
    const priceDisplay = `${price.toLocaleString('vi-VN')} VNĐ${isDiscounted ? `<span class="original-price">${originalPrice.toLocaleString('vi-VN')} VNĐ</span>` : ''}`;

    return `
        <div class="product-card" data-product-id="${product.id}">
            ${isDiscounted ? '<span class="discount-badge">Giảm 10%</span>' : ''}
            <a href="product_detail.php?id=${product.id}" class="product-image-container">
                <img src="../assets/products/${product.image}" alt="${product.name}" class="product-image">
            </a>
            <div class="product-content">
                <a href="product_detail.php?id=${product.id}" class="product-name">${product.name}</a>
                <div class="price-container">
                    <span class="current-price">${priceDisplay}</span>
                </div>
                <p class="product-stock">${product.stock} sản phẩm</p>
                <div class="options-container storage-options">
                    <p class="options-title">Dung lượng:</p>
                    ${product.storage_options.map((storage, index) => `
                        <button class="option-btn storage-option-btn ${index === 0 ? 'active' : ''}"
                                data-type="storage" data-value="${storage}">
                            ${storage}
                        </button>
                    `).join('')}
                </div>
                <div class="options-container color-options">
                    <p class="options-title">Màu sắc:</p>
                    ${product.color_options.map((color, index) => `
                        <button class="color-option-btn ${index === 0 ? 'active' : ''}"
                                data-type="color" data-value="${color.name}"
                                style="background-color: ${color.hex || '#000000'};"
                                title="${color.name}">
                        </button>
                    `).join('')}
                </div>
                <div class="product-actions">
                    <button class="add-to-cart-btn"
                            data-product-id="${product.id}"
                            data-variant-id="${product.variant_id}"
                            ${product.stock <= 0 ? 'disabled' : ''}>
                        <i class="fas fa-shopping-cart"></i> ${product.stock <= 0 ? 'Hết hàng' : 'Thêm vào giỏ'}
                    </button>
                    ${product.stock <= 0 ? `
                        <button class="add-to-wishlist-btn"
                                data-product-id="${product.id}"
                                data-variant-id="${product.variant_id}"
                                disabled>
                            <i class="fas fa-heart"></i> Hết hàng
                        </button>
                    ` : `
                        <button class="add-to-wishlist-btn"
                                data-product-id="${product.id}"
                                data-variant-id="${product.variant_id}">
                            <i class="fas fa-heart"></i> Thêm vào danh sách yêu thích
                        </button>
                    `}
                </div>
            </div>
        </div>
    `;
}

function loadProducts(category, page) {
    const productGrid = document.getElementById('productGrid');
    productGrid.innerHTML = '<p>Đang tải sản phẩm...</p>';

    fetch(`../products/fetch_products.php?category=${category}&page=${page}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            window.products = data.products;
            productGrid.innerHTML = '';
            if (data.products.length === 0) {
                productGrid.innerHTML = '<p>Không có sản phẩm nào trong danh mục này.</p>';
            } else {
                data.products.forEach(product => {
                    productGrid.innerHTML += createProductCard(product);
                });
                attachOptionButtonListeners();
            }
            updatePagination(category, page, data.total_pages);
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            productGrid.innerHTML = '<p>Có lỗi khi tải sản phẩm. Vui lòng thử lại sau.</p>';
        });
}

function updatePagination(category, page, totalPages) {
    const paginationContainer = document.createElement('div');
    paginationContainer.className = 'pagination';
    let paginationHtml = '';

    if (page > 1) {
        paginationHtml += `<a href="#" class="page-link prev-next-btn" data-page="${page - 1}">« Trước</a>`;
    }

    const numLinks = 5;
    let startPage = Math.max(1, page - Math.floor(numLinks / 2));
    let endPage = Math.min(totalPages, page + Math.ceil(numLinks / 2) - 1);

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
        paginationHtml += `<a href="#" class="page-link ${i === page ? 'active' : ''}" data-page="${i}">${i}</a>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += `<span class="pagination-ellipsis">...</span>`;
        }
        paginationHtml += `<a href="#" class="page-link" data-page="${totalPages}">${totalPages}</a>`;
    }

    if (page < totalPages) {
        paginationHtml += `<a href="#" class="page-link prev-next-btn" data-page="${page + 1}">Tiếp »</a>`;
    }

    paginationContainer.innerHTML = paginationHtml;
    const existingPagination = document.querySelector('.pagination');
    if (existingPagination) {
        existingPagination.replaceWith(paginationContainer);
    } else {
        document.getElementById('productGrid').insertAdjacentElement('afterend', paginationContainer);
    }

    document.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = parseInt(link.dataset.page);
            const currentCategory = document.querySelector('.filter-btn.active')?.dataset.category || '';
            loadProducts(currentCategory, page);
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    attachOptionButtonListeners();

    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            const category = this.dataset.category;
            loadProducts(category, 1);
        });
    });

    document.querySelectorAll('.product-card').forEach(card => {
        const stockElement = card.querySelector('.product-stock');
        if (stockElement) {
            const stock = parseInt(stockElement.textContent) || 0;
            const addToCartBtn = card.querySelector('.add-to-cart-btn');
            const addToWishlistBtn = card.querySelector('.add-to-wishlist-btn');
            if (stock <= 0) {
                if (addToCartBtn) {
                    addToCartBtn.disabled = true;
                    addToCartBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Hết hàng';
                }
                if (addToWishlistBtn) {
                    addToWishlistBtn.disabled = true;
                    addToWishlistBtn.innerHTML = '<i class="fas fa-heart"></i> Hết hàng';
                }
            }
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
  // Cập nhật giá/ảnh/tồn kho khi chọn storage/color
  document.querySelectorAll('.product-card-modern').forEach(function(card) {
    const productId = card.dataset.productId;
    const product = products.find(p => p.id == productId);
    if (!product) return;
    const img = card.querySelector('.main-product-image');
    const price = card.querySelector('.main-product-price');
    const originalPrice = card.querySelector('.main-product-original-price');
    const stock = card.querySelector('.main-product-stock');
    const storageBtns = card.querySelectorAll('.storage-option-btn');
    const colorBtns = card.querySelectorAll('.color-option-btn');
    function updateCardDisplay() {
      // Tìm variant phù hợp
      const storage = card.querySelector('.storage-option-btn.active')?.dataset.value;
      const color = card.querySelector('.color-option-btn.active')?.dataset.value;
      const variant = product.variants.find(v => v.storage === storage && v.color === color);
      if (variant) {
        if (img && variant.variant_image) img.src = '../assets/products/' + variant.variant_image;
        if (price && variant.price) price.textContent = Number(variant.price).toLocaleString('vi-VN') + ' VNĐ';
        if (originalPrice && variant.price) {
          let orig = Number(variant.price);
          if (card.querySelector('.discount-badge')) orig = Math.round(orig / 0.9);
          originalPrice.textContent = orig.toLocaleString('vi-VN') + ' VNĐ';
        }
        if (stock && variant.stock !== undefined) stock.textContent = 'Còn ' + variant.stock + ' sản phẩm';
      }
    }
    storageBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        storageBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        updateCardDisplay();
      });
    });
    colorBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        colorBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        updateCardDisplay();
      });
    });
  });

  // Quick view modal
  window.showQuickView = function(productId) {
    const product = products.find(p => p.id == productId);
    if (!product) return;
    const modal = document.getElementById('quickViewModal');
    document.getElementById('modalProductName').textContent = product.name;
    document.getElementById('modalProductImage').src = '../assets/products/' + product.image;
    // Specs
    const specsContainer = document.getElementById('modalSpecs');
    specsContainer.innerHTML = '';
    if (product.specs) {
      for (const [group, items] of Object.entries(product.specs)) {
        const specGroup = document.createElement('div');
        specGroup.className = 'spec-group';
        let html = `<h3 class="spec-title">${group}</h3>`;
        for (const [key, val] of Object.entries(items)) {
          html += `<div class="spec-item"><span class="spec-label">${key}</span><span class="spec-value">${val}</span></div>`;
        }
        specGroup.innerHTML = html;
        specsContainer.appendChild(specGroup);
      }
    }
    // Khuyến mãi
    const promotionList = document.getElementById('promotionList');
    promotionList.innerHTML = '';
    if (product.promotions) {
      product.promotions.forEach(p => {
        const li = document.createElement('li');
        li.textContent = p;
        promotionList.appendChild(li);
      });
    }
    modal.style.display = 'flex';
  };
  document.querySelectorAll('.close-btn').forEach(btn => btn.addEventListener('click', function(){ document.getElementById('quickViewModal').style.display = 'none'; }));
  window.onclick = function (event) {
    const modal = document.getElementById('quickViewModal');
    if (event.target === modal) modal.style.display = 'none';
  };
});