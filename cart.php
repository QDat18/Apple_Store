<?php
session_start();
require_once 'config/db.php';

$user_id = $_SESSION['user_id'] ?? 1; // Giả định user_id

// Xử lý cập nhật số lượng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $cart_id = (int)$_POST['cart_id'];
    $quantity = (int)$_POST['quantity'];
    $storage = $_POST['storage'];
    $color = $_POST['color'];
    // Kiểm tra tồn kho
    $stmt = $conn->prepare("SELECT stock FROM product_variants WHERE product_id = (SELECT product_id FROM cart WHERE id = ?) AND storage = ? AND color = ?");
    $stmt->bind_param("iss", $cart_id, $storage, $color);
    $stmt->execute();
    $result = $stmt->get_result();
    $variant = $result->fetch_assoc();
    $stmt->close();
    if ($quantity > $variant['stock']) {
        echo 'error_stock';
        exit;
    }
    if ($quantity < 1) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
    }
    $stmt->execute();
    $stmt->close();
    echo 'success';
    exit;
}

// Xử lý xóa sản phẩm
if (isset($_GET['remove'])) {
    $cart_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: cart.php');
    exit;
}

// Lấy giỏ hàng
$cart_items = [];
$total_price = 0;
$stmt = $conn->prepare("SELECT c.id AS cart_id, c.product_id, c.quantity, c.storage, c.color, p.name, p.image, pv.price 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        JOIN product_variants pv ON c.product_id = pv.product_id AND c.storage = pv.storage AND c.color = pv.color 
                        WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $item_price = in_array($row['product_id'], [1, 2]) ? $row['price'] * 0.9 : $row['price'];
    $total_price += $item_price * $row['quantity'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/product.css">

</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h1>
            <p>Quản lý và xem lại các sản phẩm bạn đã chọn</p>
            <?php if (!empty($cart_items)): ?>
            <div class="cart-stats">
                <div class="cart-stat">
                    <span class="cart-stat-value"><?= count($cart_items) ?></span>
                    <span class="cart-stat-label">Sản phẩm</span>
                </div>
                <div class="cart-stat">
                    <span class="cart-stat-value"><?= array_sum(array_column($cart_items, 'quantity')) ?></span>
                    <span class="cart-stat-label">Tổng số lượng</span>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="success-message" id="successMessage">
            <i class="fas fa-check-circle"></i> Cập nhật giỏ hàng thành công!
        </div>

        <div class="error-message" id="errorMessage">
            <i class="fas fa-exclamation-triangle"></i> <span id="errorText"></span>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3>Giỏ hàng của bạn đang trống</h3>
                <p>Hãy khám phá các sản phẩm tuyệt vời của chúng tôi</p>
                <a href="iphone.php" class="checkout-btn" style="max-width: 250px; margin: 1.5rem auto 0;">
                    <i class="fas fa-shopping-bag"></i> Bắt đầu mua sắm
                </a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <?php
                        $item_price = in_array($item['product_id'], [1, 2]) ? $item['price'] * 0.9 : $item['price'];
                        $subtotal = $item_price * $item['quantity'];
                        ?>
                        <div class="cart-item" data-cart-id="<?= $item['cart_id'] ?>">
                            <img src="assets/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
                            
                            <div class="item-details">
                                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="item-specs">
                                    <span class="item-spec">
                                        <i class="fas fa-memory"></i> <?= htmlspecialchars($item['storage']) ?>
                                    </span>
                                    <span class="item-spec">
                                        <i class="fas fa-palette"></i> <?= htmlspecialchars($item['color']) ?>
                                    </span>
                                </div>
                                <div class="item-price">$<?= number_format($item_price, 2) ?></div>
                            </div>
                            
                            <div class="item-actions">
                                <div class="quantity-control">
                                    <button class="quantity-btn" onclick="changeQuantity(<?= $item['cart_id'] ?>, -1, '<?= htmlspecialchars($item['storage']) ?>', '<?= htmlspecialchars($item['color']) ?>')">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" min="1" 
                                           onchange="updateCart(<?= $item['cart_id'] ?>, this.value, '<?= htmlspecialchars($item['storage']) ?>', '<?= htmlspecialchars($item['color']) ?>')">
                                    <button class="quantity-btn" onclick="changeQuantity(<?= $item['cart_id'] ?>, 1, '<?= htmlspecialchars($item['storage']) ?>', '<?= htmlspecialchars($item['color']) ?>')">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                
                                <button class="remove-btn" onclick="removeItem(<?= $item['cart_id'] ?>)">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                                
                                <div class="item-total">$<?= number_format($subtotal, 2) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h3 class="summary-title">Tóm tắt đơn hàng</h3>
                    
                    <div class="summary-row">
                        <span class="summary-label">Tạm tính</span>
                        <span class="summary-value">$<?= number_format($total_price, 2) ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Phí vận chuyển</span>
                        <span class="summary-value">Miễn phí</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Thuế</span>
                        <span class="summary-value">$0.00</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="summary-label">Tổng cộng</span>
                        <span class="summary-value summary-total">$<?= number_format($total_price, 2) ?></span>
                    </div>
                    
                    <button class="checkout-btn" onclick="window.location.href='checkout.php'">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </button>
                    
                    <button class="continue-shopping" onclick="window.location.href='iphone.php'">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="scripts/header.js"></script>
    <script src="scripts/product.js"></script>
    <script>
        function showMessage(type, message) {
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            
            if (type === 'success') {
                successMsg.style.display = 'block';
                errorMsg.style.display = 'none';
                setTimeout(() => {
                    successMsg.style.display = 'none';
                }, 3000);
            } else {
                document.getElementById('errorText').textContent = message;
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
                setTimeout(() => {
                    errorMsg.style.display = 'none';
                }, 5000);
            }
        }

        function updateCart(cartId, quantity, storage, color) {
            const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
            cartItem.classList.add('loading');
            
            fetch('cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&cart_id=${cartId}&quantity=${quantity}&storage=${storage}&color=${color}`
            })
            .then(response => response.text())
            .then(data => {
                cartItem.classList.remove('loading');
                if (data === 'success') {
                    showMessage('success', 'Cập nhật giỏ hàng thành công!');
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                } else if (data === 'error_stock') {
                    showMessage('error', 'Số lượng vượt quá tồn kho!');
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage('error', 'Có lỗi khi cập nhật giỏ hàng!');
                }
            })
            .catch(error => {
                cartItem.classList.remove('loading');
                showMessage('error', 'Có lỗi kết nối!');
            });
        }

        function changeQuantity(cartId, change, storage, color) {
            const input = document.querySelector(`[data-cart-id="${cartId}"] .quantity-input`);
            const newQuantity = parseInt(input.value) + change;
            
            if (newQuantity >= 1) {
                input.value = newQuantity;
                updateCart(cartId, newQuantity, storage, color);
            }
        }

        function removeItem(cartId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                window.location.href = `cart.php?remove=${cartId}`;
            }
        }

        // Smooth scroll to top after page load
        window.addEventListener('load', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>