<?php
session_start();
require_once 'config/db.php';

// Tạo CSRF token nếu chưa có
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
$cart_items = [];
$total_cart_amount = 0;

// Lấy thông tin giỏ hàng từ database
$stmt = $conn->prepare("
    SELECT
        c.id AS cart_item_id,
        c.quantity,
        pv.id AS variant_id,
        pv.variant_code,
        pv.variant_price,
        pv.stock_quantity,
        pv.variant_image,
        p.id AS product_id,
        p.category_id,
        p.product_name,
        (SELECT vav.value FROM product_variant_attribute_links pval
            JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
            WHERE pval.variant_id = pv.id AND vav.attribute_id = 2 LIMIT 1) AS storage,
        (SELECT vav.value FROM product_variant_attribute_links pval
            JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
            WHERE pval.variant_id = pv.id AND vav.attribute_id = 1 LIMIT 1) AS color,
        (SELECT vav.hex_code FROM product_variant_attribute_links pval
            JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
            WHERE pval.variant_id = pv.id AND vav.attribute_id = 1 LIMIT 1) AS color_hex
    FROM
        cart c
    JOIN
        product_variants pv ON c.variant_id = pv.id
    JOIN
        products p ON pv.product_id = p.id
    WHERE
        c.user_id = ?
    ORDER BY
        c.added_at DESC
");
if (!$stmt) {
    error_log("Prepare failed in cart.php: " . $conn->error);
    die("Lỗi truy vấn cơ sở dữ liệu");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $item_price = in_array($row['category_id'], [1, 2]) ? $row['variant_price'] * 0.9 : $row['variant_price'];
    $row['item_price'] = $item_price;
    $row['subtotal'] = $item_price * $row['quantity'];
    $cart_items[] = $row;
    $total_cart_amount += $row['subtotal'];
}
$stmt->close();


// Xử lý cập nhật giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['update', 'remove'])) {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi bảo mật CSRF']);
        exit();
    }
    $cart_id = isset($_POST['cart_id']) ? (int) $_POST['cart_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
    if ($_POST['action'] === 'update') {
        if ($quantity < 1) {
            echo json_encode(['status' => 'error', 'message' => 'Số lượng không hợp lệ']);
            exit();
        }
        $stmt = $conn->prepare("SELECT pv.stock_quantity FROM cart c JOIN product_variants pv ON c.variant_id = pv.id WHERE c.id = ? AND c.user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if ($row && $quantity <= $row['stock_quantity']) {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
            $stmt->execute();
            $stmt->close();
            // Lấy lại cart_count
            $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart_count = ($stmt->get_result()->fetch_assoc()['total']) ?: 0;
            $stmt->close();
            echo json_encode(['status' => 'success', 'message' => 'Cập nhật giỏ hàng thành công', 'cart_count' => $cart_count]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Số lượng vượt quá tồn kho']);
        }
        exit();
    } elseif ($_POST['action'] === 'remove') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        // Lấy lại cart_count
        $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_count = ($stmt->get_result()->fetch_assoc()['total']) ?: 0;
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Xóa sản phẩm thành công', 'cart_count' => $cart_count]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo_icon.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/product.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .cart-header h1 {
            font-size: 2.5rem;
            color: #333;
            font-weight: 700;
        }

        .cart-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 15px;
        }

        .cart-stat {
            text-align: center;
        }

        .cart-stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: #e74c3c;
        }

        .cart-stat-label {
            font-size: 0.9rem;
            color: #666;
        }

        .success-message,
        .error-message {
            display: none;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            text-align: center;
        }

        .success-message {
            background: #2ecc71;
            color: white;
        }

        .error-message {
            background: #e74c3c;
            color: white;
        }

        .empty-cart {
            text-align: center;
            padding: 50px;
        }

        .empty-cart-icon {
            font-size: 3rem;
            color: #666;
            margin-bottom: 20px;
        }

        .cart-content {
            display: flex;
            gap: 30px;
        }

        .cart-items {
            flex: 3;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .item-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-right: 20px;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .item-specs {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .item-spec {
            font-size: 0.9rem;
            color: #666;
        }

        .item-spec.color-spec {
            display: flex;
            align-items: center;
        }

        .color-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
            border: 1px solid #ccc;
        }

        .item-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #e74c3c;
        }

        .item-actions {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .quantity-btn {
            padding: 5px 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }

        .remove-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .item-total {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .cart-summary {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .summary-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .summary-label {
            color: #666;
        }

        .summary-value {
            font-weight: 600;
            color: #333;
        }

        .summary-total {
            font-size: 1.2rem;
            color: #e74c3c;
        }

        .checkout-btn,
        .continue-shopping {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .checkout-btn {
            background: #3498db;
            color: white;
            border: none;
        }

        .continue-shopping {
            background: #f9f9f9;
            color: #333;
            border: 1px solid #ddd;
        }

        @media (max-width: 768px) {
            .cart-content {
                flex-direction: column;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .item-image {
                margin-bottom: 15px;
            }

            .item-actions {
                align-items: flex-start;
            }
        }
    </style>
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
                <a href="products/products.php" class="continue-shopping">
                    <i class="fas fa-shopping-bag"></i> Bắt đầu mua sắm
                </a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="cart-item" data-cart-id="<?= htmlspecialchars($item['cart_item_id']) ?>">
                            <?php
                            $image_path = "assets/products/{$item['variant_image']}";
                            if (!file_exists($image_path)) {
                                $image_path = 'assets/products/default_product.png';
                            }
                            ?>
                            <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>"
                                class="item-image">

                            <div class="item-details">
                                <div class="item-name">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                    <?php
                                    $specs = [];
                                    if (!empty($item['storage'])) {
                                        $specs[] = htmlspecialchars($item['storage']);
                                    }
                                    if (!empty($item['color'])) {
                                        $colorLabel = htmlspecialchars($item['color']);
                                        if (!empty($item['color_hex'])) {
                                            $colorLabel .= ' <span style="display:inline-block;width:16px;height:16px;border-radius:50%;background:' . htmlspecialchars($item['color_hex']) . ';border:1.5px solid #ccc;margin-left:4px;vertical-align:middle;"></span>';
                                        }
                                        $specs[] = $colorLabel;
                                    }
                                    ?>
                                    <?php if ($specs): ?>
                                        <span style="font-weight:400;color:#555;font-size:1.05rem;">(<?= implode(' - ', $specs) ?>)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="item-price"><?= number_format($item['item_price'], 0, ',', '.') ?> VNĐ</div>
                            </div>

                            <div class="item-actions">
                                <div class="quantity-control">
                                    <button class="quantity-btn"
                                        onclick="changeQuantity(<?= htmlspecialchars($item['cart_item_id']) ?>, -1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="quantity-input"
                                        value="<?= htmlspecialchars($item['quantity']) ?>" min="1"
                                        onchange="updateCart(<?= htmlspecialchars($item['cart_item_id']) ?>, this.value)">
                                    <button class="quantity-btn"
                                        onclick="changeQuantity(<?= htmlspecialchars($item['cart_item_id']) ?>, 1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>

                                <button class="remove-btn" onclick="removeItem(<?= htmlspecialchars($item['cart_item_id']) ?>)">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>

                                <div class="item-total"><?= number_format($item['subtotal'], 0, ',', '.') ?> VNĐ</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h3 class="summary-title">Tóm tắt đơn hàng</h3>

                    <div class="summary-row">
                        <span class="summary-label">Tạm tính</span>
                        <span class="summary-value"><?= number_format($total_cart_amount, 0, ',', '.') ?> VNĐ</span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Phí vận chuyển</span>
                        <span class="summary-value">Miễn phí</span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Thuế</span>
                        <span class="summary-value">0 VNĐ</span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Tổng cộng</span>
                        <span class="summary-value summary-total"><?= number_format($total_cart_amount, 0, ',', '.') ?>
                            VNĐ</span>
                    </div>

                    <button class="checkout-btn" onclick="window.location.href='checkout.php'">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </button>

                    <a href="products/products.php" class="continue-shopping">
                        <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

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

        function updateCart(cartId, quantity) {
            const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
            cartItem.classList.add('loading');

            fetch('cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update&cart_id=${cartId}&quantity=${quantity}&csrf_token=${encodeURIComponent(document.querySelector('input[name="csrf_token"]').value)}`
            })
                .then(response => response.json())
                .then(data => {
                    cartItem.classList.remove('loading');
                    if (data.status === 'success') {
                        showMessage('success', data.message);
                        if (typeof updateCartCount === 'function' && data.cart_count !== undefined) updateCartCount(data.cart_count);
                        setTimeout(() => { location.reload(); }, 800);
                    } else {
                        showMessage('error', data.message);
                        setTimeout(() => { location.reload(); }, 2000);
                    }
                })
                .catch(error => {
                    cartItem.classList.remove('loading');
                    showMessage('error', 'Có lỗi kết nối!');
                });
        }

        function changeQuantity(cartId, change) {
            const input = document.querySelector(`[data-cart-id="${cartId}"] .quantity-input`);
            const newQuantity = parseInt(input.value) + change;

            if (newQuantity >= 1) {
                input.value = newQuantity;
                updateCart(cartId, newQuantity);
            }
        }

        function removeItem(cartId) {
            if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                fetch('cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=remove&cart_id=${cartId}&csrf_token=${encodeURIComponent(document.querySelector('input[name="csrf_token"]').value)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showMessage('success', data.message);
                            if (typeof updateCartCount === 'function' && data.cart_count !== undefined) updateCartCount(data.cart_count);
                            setTimeout(() => location.reload(), 800);
                        } else {
                            showMessage('error', data.message);
                        }
                    })
                    .catch(error => showMessage('error', 'Có lỗi kết nối!'));
            }
        }
        window.addEventListener('load', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>