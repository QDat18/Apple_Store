<?php
session_start();
require_once 'config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("Unauthorized access attempt to wishlists.php. Redirecting to login.");
    header('Location: /Apple_Shop/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle remove from wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_wishlist'])) {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Lỗi bảo mật: CSRF token không hợp lệ.";
        error_log("CSRF token validation failed. Expected: {$_SESSION['csrf_token']}, Submitted: {$_POST['csrf_token']}");
    } else {
        $variant_id = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        if ($variant_id > 0) {
            try {
                $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id = ? AND variant_id = ?");
                if (!$stmt) {
                    throw new Exception("Lỗi chuẩn bị xóa sản phẩm khỏi danh sách yêu thích: " . $conn->error);
                }
                $stmt->bind_param("ii", $user_id, $variant_id);
                if ($stmt->execute()) {
                    $success = "Đã xóa sản phẩm khỏi danh sách yêu thích.";
                    error_log("Removed variant_id $variant_id from wishlist for user_id $user_id");
                } else {
                    throw new Exception("Lỗi xóa sản phẩm khỏi danh sách yêu thích: " . $stmt->error);
                }
                $stmt->close();
            } catch (Exception $e) {
                $error = "Đã xảy ra lỗi khi xóa sản phẩm. Vui lòng thử lại.";
                error_log("Wishlist removal failed: " . $e->getMessage());
            }
        }
    }
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Lỗi bảo mật: CSRF token không hợp lệ.";
        error_log("CSRF token validation failed. Expected: {$_SESSION['csrf_token']}, Submitted: {$_POST['csrf_token']}");
    } else {
        $variant_id = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
        if ($variant_id > 0) {
            try {
                // Check stock
                $stmt = $conn->prepare("SELECT stock_quantity FROM product_variants WHERE id = ? AND status = 1");
                if (!$stmt) {
                    throw new Exception("Lỗi kiểm tra tồn kho: " . $conn->error);
                }
                $stmt->bind_param("i", $variant_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $variant = $result->fetch_assoc();
                $stmt->close();

                if (!$variant || $variant['stock_quantity'] <= 0) {
                    $error = "Sản phẩm không còn trong kho.";
                    error_log("Add to cart failed: Variant $variant_id out of stock or not found.");
                } else {
                    // Check if already in cart
                    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND variant_id = ?");
                    if (!$stmt) {
                        throw new Exception("Lỗi kiểm tra giỏ hàng: " . $conn->error);
                    }
                    $stmt->bind_param("ii", $user_id, $variant_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $cart_item = $result->fetch_assoc();
                    $stmt->close();

                    if ($cart_item) {
                        // Update quantity
                        $new_quantity = $cart_item['quantity'] + 1;
                        if ($new_quantity > $variant['stock_quantity']) {
                            $error = "Số lượng vượt quá tồn kho.";
                            error_log("Add to cart failed: Quantity exceeds stock for variant_id $variant_id.");
                        } else {
                            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
                            if (!$stmt) {
                                throw new Exception("Lỗi cập nhật giỏ hàng: " . $conn->error);
                            }
                            $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
                            if (!$stmt->execute()) {
                                throw new Exception("Lỗi cập nhật giỏ hàng: " . $stmt->error);
                            }
                            $stmt->close();
                            $success = "Đã cập nhật giỏ hàng.";
                            error_log("Updated cart: variant_id $variant_id, new quantity $new_quantity for user_id $user_id");
                        }
                    } else {
                        // Add to cart
                        $stmt = $conn->prepare("INSERT INTO cart (user_id, variant_id, quantity) VALUES (?, ?, 1)");
                        if (!$stmt) {
                            throw new Exception("Lỗi thêm vào giỏ hàng: " . $conn->error);
                        }
                        $stmt->bind_param("ii", $user_id, $variant_id);
                        if (!$stmt->execute()) {
                            throw new Exception("Lỗi thêm vào giỏ hàng: " . $stmt->error);
                        }
                        $stmt->close();
                        $success = "Đã thêm sản phẩm vào giỏ hàng.";
                        error_log("Added to cart: variant_id $variant_id for user_id $user_id");
                    }
                }
            } catch (Exception $e) {
                $error = "Đã xảy ra lỗi khi thêm vào giỏ hàng. Vui lòng thử lại.";
                error_log("Add to cart failed: " . $e->getMessage());
            }
        }
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Fetch wishlist items
try {
    $stmt = $conn->prepare("
        SELECT
            w.id AS wishlist_id, w.variant_id, p.product_name, pv.variant_price, pv.variant_image,
            pv.stock_quantity,
            (SELECT vav.value FROM variant_attribute_values vav 
             JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id 
             WHERE pval.variant_id = pv.id AND vav.attribute_id = 1) AS color,
            (SELECT vav.value FROM variant_attribute_values vav 
             JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id 
             WHERE pval.variant_id = pv.id AND vav.attribute_id = 2) AS storage
        FROM wishlists w
        JOIN product_variants pv ON w.variant_id = pv.id
        JOIN products p ON pv.product_id = p.id
        WHERE w.user_id = ? AND pv.status = 1
    ");
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn danh sách yêu thích: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $wishlist_items = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} catch (Exception $e) {
    $error = "Đã xảy ra lỗi khi tải danh sách yêu thích. Vui lòng thử lại.";
    error_log("Wishlist fetch failed: " . $e->getMessage());
    $wishlist_items = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Yêu Thích - Anh Em Rọt Store</title>
    <link rel="icon" href="/Apple_Shop/assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/Apple_Shop/css/header.css">
    <link rel="stylesheet" href="/Apple_Shop/css/product.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary-color: #007aff; /* Apple Blue */
            --secondary-color: #5ac8fa; /* Light Apple Blue */
            --accent-color: #34c759; /* Apple Green */
            --danger-color: #ff3b30; /* Apple Red */
            --background-color: #f5f5f7; /* Apple Light Gray */
            --card-background: #ffffff;
            --text-primary: #1d1d1f; /* Apple Dark Gray */
            --text-secondary: #6e6e73; /* Apple Medium Gray */
            --border-color: #e2e2e8; /* Light Border */
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.2, 0, 0, 1);
        }
        body {
            font-family: 'SF Pro Text', 'Helvetica Neue', 'Segoe UI', sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            padding: 2.5rem;
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
        }

        .wishlist-title {
            font-size: 2.5rem;
            color: var(--text-primary);
            margin-bottom: 2.5rem;
            text-align: center;
            font-weight: 700;
        }
        .wishlist-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px; /* Space between rows */
            margin-bottom: 2rem;
        }
        .wishlist-table th, .wishlist-table td {
            padding: 1rem;
            text-align: left;
        }
        .wishlist-table th {
            background-color: var(--background-color);
            font-weight: 700;
            color: var(--text-primary);
            position: sticky;
            top: 0;
            z-index: 1;
        }
        .wishlist-table tbody tr {
            background-color: #fcfcfc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        .wishlist-table tbody tr:hover {
            background-color: #f0f0f5;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }
        .wishlist-table td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .wishlist-table td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }
        .product-name {
            font-weight: 600;
            color: var(--text-primary);
        }
        .product-specs {
            font-size: 0.9em;
            color: var(--text-secondary);
        }
        .product-price {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.1em;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
            margin-left: 0.5rem;
        }
        .btn:first-child {
            margin-left: 0;
        }

        .btn-add-cart {
            background-color: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        .btn-add-cart:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .btn-add-cart:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .btn-remove {
            background-color: var(--danger-color);
            color: white;
            box-shadow: var(--shadow-sm);
        }
        .btn-remove:hover {
            background-color: #cc2929;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .btn .fas {
            margin-right: 8px;
        }

        .error-message, .success-message {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1.2rem;
            border-radius: var(--border-radius);
            font-weight: 500;
            border: 1px solid;
        }
        .error-message {
            background-color: #ffebeb; /* Light Red */
            color: var(--danger-color);
            border-color: var(--danger-color);
        }
        .success-message {
            background-color: #e6ffed; /* Light Green */
            color: var(--accent-color);
            border-color: var(--accent-color);
        }
        .empty-message {
            text-align: center;
            font-size: 1.3rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            padding: 2rem;
            background-color: #f0f0f5;
            border-radius: var(--border-radius);
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .action-buttons .btn {
            margin: 0; /* Override default margin-left */
        }


        @media (max-width: 768px) {
            .container {
                margin: 2rem auto;
                padding: 1.5rem;
            }
            .wishlist-title {
                font-size: 2rem;
                margin-bottom: 1.5rem;
            }
            .wishlist-table {
                display: block;
                overflow-x: auto; /* Enable horizontal scrolling for table */
                white-space: nowrap; /* Prevent wrapping */
            }
            .wishlist-table thead {
                display: none;
            }
            .wishlist-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 1rem;
                white-space: normal; /* Allow wrapping within a row */
            }
            .wishlist-table td {
                display: flex; /* Use flexbox for label-value pairs */
                justify-content: space-between;
                align-items: center;
                padding: 0.7rem 0.5rem;
                border-bottom: none; /* Remove individual cell borders */
            }
            .wishlist-table td:last-child {
                border-bottom: none;
                flex-direction: column; /* Stack buttons vertically */
                align-items: flex-start;
                gap: 0.8rem;
            }
            .wishlist-table td::before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 1rem; /* Space between label and value */
                flex-shrink: 0;
                color: var(--text-primary);
            }
            .product-image {
                width: 60px;
                height: 60px;
            }
            .btn {
                width: 100%; /* Make buttons full width in mobile */
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
                margin-left: 0; /* Reset margin */
            }
            .action-buttons {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <h1 class="wishlist-title">Danh Sách Yêu Thích</h1>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (empty($wishlist_items)): ?>
            <p class="empty-message">Danh sách yêu thích của bạn đang trống. Hãy thêm sản phẩm ngay!</p>
            <div class="action-buttons">
                 <a href="/Apple_Shop/index.php" class="btn btn-add-cart"><i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <table class="wishlist-table">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Thông số</th>
                        <th>Giá</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishlist_items as $item): ?>
                        <tr>
                            <td data-label="Hình ảnh">
                                <img src="/Apple_Shop/<?= htmlspecialchars($item['variant_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-image">
                            </td>
                            <td data-label="Sản phẩm">
                                <span class="product-name"><?= htmlspecialchars($item['product_name']) ?></span>
                            </td>
                            <td data-label="Thông số">
                                <span class="product-specs">
                                <?php
                                $spec_display = [];
                                if (!empty($item['storage'])) {
                                    $spec_display[] = htmlspecialchars($item['storage']);
                                }
                                if (!empty($item['color'])) {
                                    $spec_display[] = htmlspecialchars($item['color']);
                                }
                                echo implode(' - ', $spec_display);
                                ?>
                                </span>
                            </td>
                            <td data-label="Giá">
                                <span class="product-price"><?= number_format($item['variant_price'], 0, ',', '.') ?> VNĐ</span>
                            </td>
                            <td data-label="Thao tác">
                                <form method="POST" style="display:inline-block; margin-bottom: 5px;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-add-cart" <?= $item['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                                        <i class="fas fa-cart-plus"></i> <?= $item['stock_quantity'] <= 0 ? 'Hết hàng' : 'Thêm vào giỏ' ?>
                                    </button>
                                </form>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?>">
                                    <button type="submit" name="remove_wishlist" class="btn btn-remove">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="action-buttons">
                <a href="cart.php" class="btn btn-add-cart"><i class="fas fa-shopping-cart"></i> Xem giỏ hàng</a>
                <a href="/Apple_Shop/products/products.php" class="btn btn-add-cart"><i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm</a>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>