<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set correct timezone
require_once 'config/db.php';

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$cart_items = [];
$total_price = 0;

// Debug session
error_log("Session ID: " . session_id() . ", CSRF Token: " . ($_SESSION['csrf_token'] ?? 'Not set'));

// Tạo hoặc sử dụng CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Fetches product variant details including attributes.
 * @param mysqli $conn Database connection.
 * @param int $variant_id Variant ID.
 * @return array|null Product variant details or null on failure.
 */
function getProductVariantDetailsForCheckout($conn, $variant_id) {
    $stmt = $conn->prepare("
        SELECT
            pv.id AS variant_id,
            pv.variant_code,
            pv.variant_price,
            pv.stock_quantity,
            pv.variant_image,
            p.id AS product_id,
            p.category_id,
            p.product_name,
            (SELECT vav.value FROM variant_attribute_values vav
             JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id
             WHERE pval.variant_id = pv.id AND vav.attribute_id = 2) AS storage,
            (SELECT vav.value FROM variant_attribute_values vav
             JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id
             WHERE pval.variant_id = pv.id AND vav.attribute_id = 1) AS color
        FROM
            product_variants pv
        JOIN
            products p ON pv.product_id = p.id
        WHERE
            pv.id = ?
    ");
    if (!$stmt) {
        error_log("Prepare failed for getProductVariantDetailsForCheckout: " . $conn->error);
        return null;
    }
    $stmt->bind_param("i", $variant_id);
    if (!$stmt->execute()) {
        error_log("Execute failed for getProductVariantDetailsForCheckout: " . $stmt->error);
        return null;
    }
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data;
}

/**
 * Calculates item price with promotions.
 * @param mysqli $conn Database connection.
 * @param float $base_price Base price of the item.
 * @param int $product_id Product ID.
 * @param int $category_id Category ID.
 * @return float Final item price after discounts.
 */
function calculateItemFinalPrice($conn, $base_price, $product_id, $category_id) {
    $item_price = (float)$base_price;

    // Apply hardcoded category/product discounts
    if (in_array($product_id, [1, 2]) || in_array($category_id, [1, 3])) {
        $item_price *= 0.9; // 10% discount
    }

    // Apply promotions from the 'promotions' table for iPhone 16 Pro (product_id = 2)
    // Prepare statement outside the loop if this function is called repeatedly for many items
    static $promo_stmt = null;
    if ($promo_stmt === null) {
        $promo_stmt = $conn->prepare("
            SELECT type, description
            FROM promotions
            WHERE slug = ? AND expiry_date >= CURDATE()
        ");
        if (!$promo_stmt) {
            error_log("Prepare failed for promotions query: " . $conn->error);
        }
    }

    if ($promo_stmt) {
        $promo_slug = 'giam-gia-10-iphone-16-pro'; // Specific slug for iPhone 16 Pro discount
        $promo_stmt->bind_param("s", $promo_slug);
        if ($promo_stmt->execute()) {
            $promo_result = $promo_stmt->get_result();
            if ($promo_result->num_rows > 0 && $product_id == 2) {
                if ($promo_result->fetch_assoc()['type'] === 'discount') {
                    $item_price *= 0.9; // Apply another 10% discount if promotion exists
                }
            }
        } else {
            error_log("Execute failed for promotions query: " . $promo_stmt->error);
        }
    }

    return $item_price;
}


// Function to fetch all cart items for a user
function fetchCartItems($conn, $user_id) {
    $items = [];
    $stmt = $conn->prepare("
        SELECT
            c.id AS cart_id,
            c.variant_id,
            c.quantity
        FROM cart c
        WHERE c.user_id = ?
    ");
    if (!$stmt) {
        error_log("Prepare failed for fetching cart items: " . $conn->error);
        return [];
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        error_log("Execute failed for fetching cart items: " . $stmt->error);
        return [];
    }
    $result = $stmt->get_result();
    $total_calculated_price = 0;
    while ($row = $result->fetch_assoc()) {
        $variant_details = getProductVariantDetailsForCheckout($conn, $row['variant_id']);
        if ($variant_details) {
            $item = array_merge($row, $variant_details);
            $item_final_price = calculateItemFinalPrice($conn, $item['variant_price'], $item['product_id'], $item['category_id']);
            $item['item_price'] = $item_final_price; // Store the final price after all discounts
            $items[] = $item;
            $total_calculated_price += $item_final_price * $item['quantity'];
        }
    }
    $stmt->close();
    return ['items' => $items, 'total_price' => $total_calculated_price];
}

// Fetch cities from API or fallback
$cities = [];
$cache_file = 'cache/provinces.json';
$cache_time = 60 * 60 * 24; // Cache for 24 hours

if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
    $cities = json_decode(file_get_contents($cache_file), true);
} else {
    $api_url = 'https://provinces.open-api.vn/api/p/';
    $ch = curl_init();
    if ($ch === false) {
        error_log("curl_init failed in checkout.php");
        $error = "Lỗi hệ thống khi lấy danh sách tỉnh/thành phố.";
    } else {
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200 && $response !== false) {
            $cities = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                usort($cities, function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });
                // Save to cache
                if (!is_dir('cache')) {
                    mkdir('cache', 0755, true);
                }
                file_put_contents($cache_file, json_encode($cities));
            } else {
                error_log("JSON decode error for cities API: " . json_last_error_msg());
                $error = "Lỗi dữ liệu khi lấy danh sách tỉnh/thành phố.";
            }
        } else {
            error_log("Failed to fetch cities from API. HTTP Code: $http_code, cURL Error: " . curl_error($ch));
            $error = "Không thể kết nối đến API lấy danh sách tỉnh/thành phố.";
        }
    }

    // Fallback if API fails or cache is empty
    if (empty($cities)) {
        $cities = [
            ['code' => '01', 'name' => 'Thành phố Hà Nội'], // Updated codes to match API
            ['code' => '79', 'name' => 'Thành phố Hồ Chí Minh'],
            ['code' => '48', 'name' => 'Thành phố Đà Nẵng'],
            ['code' => '31', 'name' => 'Thành phố Hải Phòng'],
            ['code' => '92', 'name' => 'Thành phố Cần Thơ'],
            ['code' => '00', 'name' => 'Tỉnh Khác'] // Generic "Other"
        ];
    }
}


// Initial cart fetch for display
$cart_data_for_display = fetchCartItems($conn, $user_id);
$cart_items = $cart_data_for_display['items'];
$total_price = $cart_data_for_display['total_price'];

// Xử lý đặt hàng
$order_data = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    error_log("Processing POST request: " . json_encode($_POST));
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Lỗi bảo mật: CSRF token không hợp lệ.";
        error_log("CSRF token validation failed. Expected: {$_SESSION['csrf_token']}, Submitted: {$_POST['csrf_token']}");
    } else {
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $ward = trim($_POST['ward'] ?? '');
        $payment_method = trim($_POST['payment_method'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        // Validate input data
        if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($district) || empty($ward) || empty($payment_method)) {
            $error = "Vui lòng điền đầy đủ thông tin bắt buộc!";
            error_log("Missing required fields: " . json_encode($_POST));
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email không hợp lệ!";
            error_log("Invalid email format: $email");
        } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $error = "Số điện thoại không hợp lệ! (Chỉ chấp nhận 10 hoặc 11 chữ số)";
            error_log("Invalid phone format: $phone");
        } elseif (!in_array($payment_method, ['cod', 'bank', 'momo'])) {
            $error = "Phương thức thanh toán không hợp lệ!";
            error_log("Invalid payment method: $payment_method");
        } else {
            // Re-fetch cart items
            $cart_data_for_order = fetchCartItems($conn, $user_id);
            $cart_items_for_order = $cart_data_for_order['items'];
            $total_amount = $cart_data_for_order['total_price'];

            if (empty($cart_items_for_order)) {
                $error = "Giỏ hàng của bạn đang trống!";
                error_log("Empty cart for user_id: $user_id on order placement attempt.");
            } else {
                // Check stock
                foreach ($cart_items_for_order as $item) {
                    $variant_details = getProductVariantDetailsForCheckout($conn, $item['variant_id']);
                    if (!$variant_details || $item['quantity'] > $variant_details['stock_quantity']) {
                        $error = "Sản phẩm {$item['product_name']} ({$item['storage']}, {$item['color']}) vượt quá tồn kho! (Tồn kho hiện tại: " . ($variant_details['stock_quantity'] ?? 0) . ")";
                        error_log("Stock insufficient for variant_id: {$item['variant_id']}, requested: {$item['quantity']}, available: " . ($variant_details['stock_quantity'] ?? 'N/A'));
                        break;
                    }
                }

                if (empty($error)) {
                    $conn->begin_transaction();
                    try {
                        // Check if required tables exist
                        $required_tables = ['orders', 'order_items', 'product_variants', 'cart'];
                        foreach ($required_tables as $table) {
                            $result = $conn->query("SHOW TABLES LIKE '$table'");
                            if ($result->num_rows === 0) {
                                throw new Exception("Bảng '$table' không tồn tại trong cơ sở dữ liệu.");
                            }
                        }

                        // Tạo order_code duy nhất
                        $order_code = 'ORD' . date('YmdHis') . rand(100, 999);
                        // Lưu vào bảng orders
                        $stmt = $conn->prepare("INSERT INTO orders (order_code, user_id, order_date, total_amount, status, shipping_address, full_name, email, phone_number, notes, payment_method) VALUES (?, ?, NOW(), ?, 'Pending', ?, ?, ?, ?, ?, ?)");
                        $total_amount = $total_price;
                        $shipping_address = $address . ', ' . $ward . ', ' . $district . ', ' . $city;
                        $stmt->bind_param("sidssssss", $order_code, $user_id, $total_amount, $shipping_address, $full_name, $email, $phone, $notes, $payment_method);
                        if ($stmt->execute()) {
                            $order_id = $stmt->insert_id;
                            $stmt->close();
                            // Lưu từng sản phẩm vào order_items
                            foreach ($cart_items_for_order as $item) {
                                $stmt = $conn->prepare("INSERT INTO order_items (order_id, variant_id, quantity, price) VALUES (?, ?, ?, ?)");
                                $stmt->bind_param("iiid", $order_id, $item['variant_id'], $item['quantity'], $item['item_price']);
                                $stmt->execute();
                                $stmt->close();
                            }
                            // Xóa giỏ hàng
                            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $stmt->close();
                            unset($_SESSION['cart']);
                            // Store order data in session (trước khi redirect)
                            $_SESSION['order_data'] = [
                                'order_code' => $order_code,
                                'full_name' => $full_name,
                                'email' => $email,
                                'phone' => $phone,
                                'address' => $shipping_address,
                                'ward' => $ward,
                                'district' => $district,
                                'city' => $city,
                                'payment_method' => $payment_method,
                                'notes' => $notes,
                                'items' => $cart_items_for_order,
                                'total' => $total_amount
                            ];
                            error_log("[CHECKOUT] Saved order_data to session for order_code: $order_code");
                            unset($_SESSION['csrf_token']);
                            $conn->commit();
                            error_log("[CHECKOUT] Redirecting to order_success.php with order_code: $order_code");
                            header('Location: order_success.php?order_code=' . urlencode($order_code));
                            exit();
                        } else {
                            $error = 'Lỗi khi lưu đơn hàng: ' . $stmt->error;
                            $stmt->close();
                            $conn->rollback();
                        }
                    } catch (Exception $e) {
                        $conn->rollback();
                        $error = "Đã xảy ra lỗi trong quá trình đặt hàng. Vui lòng thử lại hoặc liên hệ hỗ trợ qua hotline: 0123 456 789.";
                        error_log("Order placement failed: " . $e->getMessage());
                    }
                }
            }
        }
    }
}
$shipping_fee = 0; // Or calculate based on address/total_price
$tax = 0; // Or calculate based on total_price
$final_total = (float)$total_price + $shipping_fee + $tax;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/product.css">
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #2d3748;
            --accent-color: #667eea;
            --success-color: #48bb78;
            --danger-color: #f56565;
            --warning-color: #ed8936;
            --info-color: #4299e1;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #2d3748;
            --text-secondary: #718096;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .checkout-progress {
            display: flex;
            justify-content: center;
            margin-bottom: 3rem;
            background: var(--card-background);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
        }

        .progress-step {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 2rem;
            position: relative;
        }

        .progress-step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -2rem;
            top: 50%;
            transform: translateY(-50%);
            width: 4rem;
            height: 2px;
            background: var(--border-color);
        }

        .progress-step.active::after {
            background: var(--accent-color);
        }

        .progress-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--border-color);
            color: var(--text-secondary);
            font-size: 1.2rem;
            transition: var(--transition);
        }

        .progress-step.active .progress-icon {
            background: var(--accent-color);
            color: white;
        }

        .progress-step.completed .progress-icon {
            background: var(--success-color);
            color: white;
        }

        .progress-text {
            font-weight: 600;
            color: var(--text-secondary);
            transition: var(--transition);
        }

        .progress-step.active .progress-text {
            color: var(--accent-color);
        }

        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 3rem;
            align-items: flex-start;
        }

        .checkout-form {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-section:last-child {
            margin-bottom: 0;
        }

        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--accent-color);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label.required::after {
            content: '*';
            color: var(--danger-color);
        }

        .form-input {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
            background: var(--card-background);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input.error {
            border-color: var(--danger-color);
        }

        .error-message {
            color: var(--danger-color);
            font-size: 0.9rem;
            margin-top: 0.25rem;
            grid-column: 1 / -1;
        }

        .form-textarea {
            min-height: 80px;
            resize: vertical;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .payment-methods {
            display: grid;
            gap: 1rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            background: var(--card-background);
        }

        .payment-method:hover {
            border-color: var(--accent-color);
            background: rgba(102, 126, 234, 0.05);
        }

        .payment-method.selected {
            border-color: var(--accent-color);
            background: rgba(102, 126, 234, 0.1);
        }

        .payment-radio {
            margin-right: 1rem;
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .payment-method[data-method="cod"] .payment-icon {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .payment-method[data-method="bank"] .payment-icon {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }

        .payment-method[data-method="momo"] .payment-icon {
            background: linear-gradient(135deg, #d53f8c, #b83280);
            color: white;
        }

        .payment-info {
            flex: 1;
        }

        .payment-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .payment-desc {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .qr-code-container {
            display: none;
            margin-top: 1.5rem;
            padding: 1rem;
            background: var(--card-background);
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .qr-code-container.active {
            display: block;
        }

        .qr-code-image {
            max-width: 200px;
            height: auto;
            margin-bottom: 1rem;
        }

        .qr-code-info {
            font-size: 0.95rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .qr-code-info strong {
            color: var(--text-primary);
        }

        .order-summary {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }

        .summary-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-products {
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .summary-product-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .summary-product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .summary-product-info {
            flex: 1;
        }

        .summary-product-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .summary-product-qty-price {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .summary-details {
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 1rem;
            color: var(--text-primary);
        }

        .summary-row span:last-child {
            font-weight: 600;
        }

        .summary-row.total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent-color);
            border-top: 1px dashed var(--border-color);
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .place-order-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background-color: var(--accent-color);
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
        }

        .place-order-btn:hover {
            background-color: #5a67d8;
            box-shadow: var(--shadow-md);
        }

        .error-message-box {
            background-color: var(--danger-color);
            color: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        @media (max-width: 992px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .checkout-progress {
                flex-wrap: wrap;
            }

            .progress-step {
                width: 50%;
                justify-content: center;
                margin-bottom: 1rem;
            }

            .progress-step:not(:last-child)::after {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .progress-step {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="checkout-progress">
            <div class="progress-step completed">
                <div class="progress-icon"><i class="fas fa-shopping-cart"></i></div>
                <span class="progress-text">Giỏ hàng</span>
            </div>
            <div class="progress-step active">
                <div class="progress-icon"><i class="fas fa-credit-card"></i></div>
                <span class="progress-text">Thanh toán</span>
            </div>
            <div class="progress-step">
                <div class="progress-icon"><i class="fas fa-check-circle"></i></div>
                <span class="progress-text">Hoàn tất</span>
            </div>
        </div>

        <?php if (!empty($error)) : ?>
            <div class="error-message-box">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="checkout-content">
            <div class="checkout-form">
                <form action="checkout.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="place_order" value="1">

                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-user"></i> Thông tin khách hàng</h2>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="full_name" class="form-label required">Họ và tên:</label>
                                <input type="text" id="full_name" name="full_name" class="form-input" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label required">Email:</label>
                                <input type="email" id="email" name="email" class="form-input" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label required">Số điện thoại:</label>
                                <input type="tel" id="phone" name="phone" class="form-input" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Địa chỉ giao hàng</h2>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city" class="form-label required">Tỉnh/Thành phố:</label>
                                <select id="city" name="city" class="form-input form-select" required>
                                    <option value="">Chọn tỉnh/thành phố</option>
                                    <?php foreach ($cities as $city_item) : ?>
                                        <option value="<?= htmlspecialchars($city_item['name']) ?>" data-code="<?= htmlspecialchars($city_item['code']) ?>" <?= (isset($_POST['city']) && $_POST['city'] == $city_item['name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($city_item['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="district" class="form-label required">Quận/Huyện:</label>
                                <select id="district" name="district" class="form-input form-select" required>
                                    <option value="">Chọn quận/huyện</option>
                                    <?php if (isset($_POST['city']) && isset($_POST['district'])) : // Repopulate districts if form submitted
                                        $selectedCityCode = '';
                                        foreach ($cities as $city_item) {
                                            if ($city_item['name'] === $_POST['city']) {
                                                $selectedCityCode = $city_item['code'];
                                                break;
                                            }
                                        }
                                        if ($selectedCityCode) {
                                            $districts_api_url = "https://provinces.open-api.vn/api/p/{$selectedCityCode}?depth=2";
                                            $ch_dist = curl_init($districts_api_url);
                                            curl_setopt($ch_dist, CURLOPT_RETURNTRANSFER, true);
                                            $districts_response = curl_exec($ch_dist);
                                            curl_close($ch_dist);
                                            $districts_data = json_decode($districts_response, true);
                                            if (isset($districts_data['districts'])) {
                                                foreach ($districts_data['districts'] as $dist) {
                                                    echo '<option value="' . htmlspecialchars($dist['name']) . '" data-code="' . htmlspecialchars($dist['code']) . '" ' . ((isset($_POST['district']) && $_POST['district'] == $dist['name']) ? 'selected' : '') . '>' . htmlspecialchars($dist['name']) . '</option>';
                                                }
                                            }
                                        }
                                    endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="ward" class="form-label required">Phường/Xã:</label>
                                <select id="ward" name="ward" class="form-input form-select" required>
                                    <option value="">Chọn phường/xã</option>
                                    <?php if (isset($_POST['district']) && isset($_POST['ward'])) : // Repopulate wards if form submitted
                                        $selectedCityCode = '';
                                        foreach ($cities as $city_item) {
                                            if ($city_item['name'] === $_POST['city']) {
                                                $selectedCityCode = $city_item['code'];
                                                break;
                                            }
                                        }

                                        $selectedDistrictCode = '';
                                        if ($selectedCityCode) {
                                            $districts_api_url = "https://provinces.open-api.vn/api/p/{$selectedCityCode}?depth=2";
                                            $ch_dist = curl_init($districts_api_url);
                                            curl_setopt($ch_dist, CURLOPT_RETURNTRANSFER, true);
                                            $districts_response = curl_exec($ch_dist);
                                            curl_close($ch_dist);
                                            $districts_data = json_decode($districts_response, true);
                                            if (isset($districts_data['districts'])) {
                                                foreach ($districts_data['districts'] as $dist) {
                                                    if ($dist['name'] === $_POST['district']) {
                                                        $selectedDistrictCode = $dist['code'];
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                        if ($selectedDistrictCode) {
                                            $wards_api_url = "https://provinces.open-api.vn/api/d/{$selectedDistrictCode}?depth=2";
                                            $ch_ward = curl_init($wards_api_url);
                                            curl_setopt($ch_ward, CURLOPT_RETURNTRANSFER, true);
                                            $wards_response = curl_exec($ch_ward);
                                            curl_close($ch_ward);
                                            $wards_data = json_decode($wards_response, true);
                                            if (isset($wards_data['wards'])) {
                                                foreach ($wards_data['wards'] as $ward_item) {
                                                    echo '<option value="' . htmlspecialchars($ward_item['name']) . '" ' . ((isset($_POST['ward']) && $_POST['ward'] == $ward_item['name']) ? 'selected' : '') . '>' . htmlspecialchars($ward_item['name']) . '</option>';
                                                }
                                            }
                                        }
                                    endif; ?>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label for="address" class="form-label required">Địa chỉ cụ thể (Số nhà, tên đường):</label>
                                <input type="text" id="address" name="address" class="form-input" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                            </div>
                            <div class="form-group full-width">
                                <label for="notes" class="form-label">Ghi chú (Tùy chọn):</label>
                                <textarea id="notes" name="notes" class="form-input form-textarea"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2 class="section-title"><i class="fas fa-money-bill-wave"></i> Phương thức thanh toán</h2>
                        <div class="payment-methods">
                            <label class="payment-method <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cod') ? 'selected' : '' ?>" data-method="cod">
                                <input type="radio" name="payment_method" value="cod" class="payment-radio" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'cod') ? 'checked' : '' ?> required>
                                <div class="payment-icon"><i class="fas fa-truck"></i></div>
                                <div class="payment-info">
                                    <div class="payment-title">Thanh toán khi nhận hàng (COD)</div>
                                    <div class="payment-desc">Thanh toán tiền mặt cho nhân viên giao hàng khi bạn nhận được đơn.</div>
                                </div>
                            </label>
                            <label class="payment-method <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank') ? 'selected' : '' ?>" data-method="bank">
                                <input type="radio" name="payment_method" value="bank" class="payment-radio" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank') ? 'checked' : '' ?> required>
                                <div class="payment-icon"><i class="fas fa-building-columns"></i></div>
                                <div class="payment-info">
                                    <div class="payment-title">Chuyển khoản ngân hàng</div>
                                    <div class="payment-desc">Thanh toán qua chuyển khoản ngân hàng. Thông tin chi tiết sẽ hiển thị sau khi đặt hàng.</div>
                                </div>
                            </label>
                            <label class="payment-method <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'momo') ? 'selected' : '' ?>" data-method="momo">
                                <input type="radio" name="payment_method" value="momo" class="payment-radio" <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'momo') ? 'checked' : '' ?> required>
                                <div class="payment-icon"><i class="fas fa-wallet"></i></div>
                                <div class="payment-info">
                                    <div class="payment-title">Ví điện tử MoMo</div>
                                    <div class="payment-desc">Thanh toán nhanh chóng qua ứng dụng MoMo.</div>
                                </div>
                            </label>
                        </div>

                        <div id="qrCodeBank" class="qr-code-container <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'bank') ? 'active' : '' ?>">
                            <img src="assets/images/bank_qr.png" alt="QR Code Ngân hàng" class="qr-code-image">
                            <p class="qr-code-info">Quét mã QR để chuyển khoản hoặc chuyển đến thông tin sau:</p>
                            <p class="qr-code-info"><strong>Ngân hàng:</strong> ACB</p>
                            <p class="qr-code-info"><strong>Số tài khoản:</strong> 1234567890</p>
                            <p class="qr-code-info"><strong>Chủ tài khoản:</strong> NGUYEN VAN A</p>
                            <p class="qr-code-info"><strong>Nội dung chuyển khoản:</strong> Mã đơn hàng của bạn</p>
                        </div>
                        <div id="qrCodeMomo" class="qr-code-container <?= (isset($_POST['payment_method']) && $_POST['payment_method'] == 'momo') ? 'active' : '' ?>">
                            <img src="assets/images/momo_qr.png" alt="QR Code MoMo" class="qr-code-image">
                            <p class="qr-code-info">Quét mã QR để thanh toán qua MoMo:</p>
                            <p class="qr-code-info"><strong>Số điện thoại:</strong> 0987 654 321</p>
                            <p class="qr-code-info"><strong>Tên người nhận:</strong> NGUYEN THI B</p>
                            <p class="qr-code-info"><strong>Nội dung:</strong> Mã đơn hàng của bạn</p>
                        </div>
                    </div>
                    <?php if (!empty($cart_items)) : ?>
                        <button type="submit" class="place-order-btn">
                            Đặt hàng ngay - <?= number_format($final_total, 2) ?> VNĐ
                        </button>
                    <?php else : ?>
                        <p class="error-message-box">Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm để đặt hàng.</p>
                        <a href="index.php" class="place-order-btn" style="background-color: #6c757d;">Quay về trang chủ</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="order-summary">
                <h2 class="summary-title"><i class="fas fa-clipboard-list"></i> Tóm tắt đơn hàng</h2>
                <?php if (!empty($cart_items)) : ?>
                    <div class="summary-products">
                        <?php foreach ($cart_items as $item) :
                            $image_path = "assets/products/{$item['variant_image']}";
                            $full_image_path = $_SERVER['DOCUMENT_ROOT'] . '/Apple_Shop/products' . $image_path; // Adjust as needed
                            if (!file_exists($full_image_path)) {
                                $image_path = 'assets/products/default-product.png';
                            }
                        ?>
                            <div class="summary-product-item">
                                <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="summary-product-image">
                                <div class="summary-product-info">
                                    <div class="summary-product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                    <div class="summary-product-qty-price">
                                        <?= htmlspecialchars($item['quantity']) ?> x <?= number_format($item['item_price'], 2) ?> VNĐ
                                        <?php if (!empty($item['storage'])) : ?>
                                            (<?= htmlspecialchars($item['storage']) ?>
                                            <?php if (!empty($item['color'])) : ?>
                                                - <?= htmlspecialchars($item['color']) ?>
                                            <?php endif; ?>)
                                        <?php elseif (!empty($item['color'])) : ?>
                                            (<?= htmlspecialchars($item['color']) ?>)
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-details">
                        <div class="summary-row">
                            <span>Tổng tiền sản phẩm:</span>
                            <span><?= number_format($total_price, 2) ?> VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span><?= number_format($shipping_fee, 2) ?> VNĐ</span>
                        </div>
                        <div class="summary-row">
                            <span>Thuế:</span>
                            <span><?= number_format($tax, 2) ?> VNĐ</span>
                        </div>
                        <div class="summary-row total">
                            <span>Tổng cộng:</span>
                            <span><?= number_format($final_total, 2) ?> VNĐ</span>
                        </div>
                    </div>
                <?php else : ?>
                    <p>Không có sản phẩm nào trong giỏ hàng.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/header.js"></script>
    <script>
        const citySelect = document.getElementById('city');
        const districtSelect = document.getElementById('district');
        const wardSelect = document.getElementById('ward');
        const paymentMethods = document.querySelectorAll('.payment-method input[type="radio"]');
        const qrCodeBank = document.getElementById('qrCodeBank');
        const qrCodeMomo = document.getElementById('qrCodeMomo');

        // Initial population of cities from PHP (already done via PHP echo)
        const citiesData = <?= json_encode($cities) ?>;
        let selectedCityCode = '';
        let selectedDistrictCode = '';

        // Function to update districts based on selected city
        async function updateDistricts() {
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';
            selectedDistrictCode = ''; // Reset district code

            const selectedCityName = citySelect.value;
            const city = citiesData.find(c => c.name === selectedCityName);

            if (city) {
                selectedCityCode = city.code;
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/p/${selectedCityCode}?depth=2`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data && data.districts) {
                        data.districts.sort((a, b) => a.name.localeCompare(b.name));
                        data.districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.name;
                            option.textContent = district.name;
                            option.setAttribute('data-code', district.code);
                            districtSelect.appendChild(option);
                        });

                        // If a district was previously selected (e.g., after form submission), re-select it
                        const prevDistrict = '<?= htmlspecialchars($_POST['district'] ?? '') ?>';
                        if (prevDistrict) {
                            const foundOption = Array.from(districtSelect.options).find(opt => opt.value === prevDistrict);
                            if (foundOption) {
                                districtSelect.value = prevDistrict;
                                selectedDistrictCode = foundOption.getAttribute('data-code');
                                updateWards(); // Update wards based on re-selected district
                            }
                        }
                    }
                } catch (error) {
                    console.error('Lỗi khi lấy danh sách quận/huyện:', error);
                }
            }
        }

        // Function to update wards based on selected district
        async function updateWards() {
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            const selectedDistrictName = districtSelect.value;
            const selectedDistrictOption = districtSelect.options[districtSelect.selectedIndex];
            selectedDistrictCode = selectedDistrictOption ? selectedDistrictOption.getAttribute('data-code') : '';

            if (selectedDistrictCode) {
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/d/${selectedDistrictCode}?depth=2`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data && data.wards) {
                        data.wards.sort((a, b) => a.name.localeCompare(b.name));
                        data.wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.name;
                            option.textContent = ward.name;
                            wardSelect.appendChild(option);
                        });

                        // If a ward was previously selected, re-select it
                        const prevWard = '<?= htmlspecialchars($_POST['ward'] ?? '') ?>';
                        if (prevWard) {
                            wardSelect.value = prevWard;
                        }
                    }
                } catch (error) {
                    console.error('Lỗi khi lấy danh sách phường/xã:', error);
                }
            }
        }

        // Event Listeners
        citySelect.addEventListener('change', updateDistricts);
        districtSelect.addEventListener('change', updateWards);

        paymentMethods.forEach(radio => {
            radio.addEventListener('change', () => {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
                qrCodeBank.classList.remove('active');
                qrCodeMomo.classList.remove('active');

                if (selectedMethod === 'bank') {
                    qrCodeBank.classList.add('active');
                } else if (selectedMethod === 'momo') {
                    qrCodeMomo.classList.add('active');
                }

                // Update selected class for styling
                document.querySelectorAll('.payment-method').forEach(label => {
                    label.classList.remove('selected');
                });
                radio.closest('.payment-method').classList.add('selected');
            });
        });

        // Initial calls to populate dropdowns if form was submitted with values
        window.addEventListener('load', () => {
            // Re-select payment method on load if previously selected
            const initialPaymentMethod = '<?= htmlspecialchars($_POST['payment_method'] ?? '') ?>';
            if (initialPaymentMethod) {
                const selectedRadio = document.querySelector(`input[name="payment_method"][value="${initialPaymentMethod}"]`);
                if (selectedRadio) {
                    selectedRadio.checked = true;
                    selectedRadio.closest('.payment-method').classList.add('selected');
                    // Manually trigger change to show QR code if applicable
                    if (initialPaymentMethod === 'bank') {
                        qrCodeBank.classList.add('active');
                    } else if (initialPaymentMethod === 'momo') {
                        qrCodeMomo.classList.add('active');
                    }
                }
            }

            // Populate districts and wards if city/district were previously selected
            const prevCity = '<?= htmlspecialchars($_POST['city'] ?? '') ?>';
            if (prevCity) {
                // Ensure city is selected in the dropdown
                citySelect.value = prevCity;
                updateDistricts().then(() => {
                    const prevDistrict = '<?= htmlspecialchars($_POST['district'] ?? '') ?>';
                    if (prevDistrict) {
                        districtSelect.value = prevDistrict;
                        updateWards(); // Call updateWards after districts are loaded
                    }
                });
            }
        });
    </script>
</body>

</html>