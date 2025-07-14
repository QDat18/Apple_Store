<?php
session_start();
require_once 'config/db.php';

$user_id = $_SESSION['user_id'] ?? 1; // Giả định user_id

// Gọi API để lấy danh sách tỉnh/thành phố
$api_url = 'https://provinces.open-api.vn/api/p/';
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$cities = [];
if ($http_code === 200) {
    $cities = json_decode($response, true);
    usort($cities, function ($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
} else {
    $cities = [
        ['code' => 1, 'name' => 'Hà Nội'],
        ['code' => 2, 'name' => 'TP. Hồ Chí Minh'],
        ['code' => 3, 'name' => 'Đà Nẵng'],
        ['code' => 4, 'name' => 'Hải Phòng'],
        ['code' => 5, 'name' => 'Cần Thơ'],
        ['code' => 0, 'name' => 'Khác']
    ];
}

// Xử lý đặt hàng
$order_data = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);
    $ward = trim($_POST['ward']);
    $payment_method = trim($_POST['payment_method']);
    $notes = trim($_POST['notes'] ?? '');

    // Kiểm tra dữ liệu đầu vào
    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($district) || empty($ward) || empty($payment_method)) {
        $error = "Vui lòng điền đầy đủ thông tin bắt buộc!";
    } else {
        // Lấy giỏ hàng
        $stmt = $conn->prepare("SELECT c.id AS cart_id, c.product_id, c.quantity, c.storage, c.color, p.name, pv.price, pv.stock 
                                FROM cart c 
                                JOIN products p ON c.product_id = p.id 
                                JOIN product_variants pv ON c.product_id = pv.product_id AND c.storage = pv.storage AND c.color = pv.color 
                                WHERE c.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        if (empty($cart_items)) {
            $error = "Giỏ hàng của bạn đang trống!";
        } else {
            // Kiểm tra tồn kho
            foreach ($cart_items as $item) {
                if ($item['quantity'] > $item['stock']) {
                    $error = "Số lượng sản phẩm {$item['name']} ({$item['storage']}, {$item['color']}) vượt quá tồn kho!";
                    break;
                }
            }

            if (!isset($error)) {
                // Tạo đơn hàng
                $order_code = 'ORD' . date('YmdHis') . rand(100, 999);
                $stmt = $conn->prepare("INSERT INTO orders (user_id, order_code, full_name, email, phone, address, city, district, ward, payment_method, notes, total, status) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'pending')");
                $stmt->bind_param("issssssssss", $user_id, $order_code, $full_name, $email, $phone, $address, $city, $district, $ward, $payment_method, $notes);
                if ($stmt->execute()) {
                    $order_id = $conn->insert_id;

                    // Tạo chi tiết đơn hàng và cập nhật tồn kho
                    $total_amount = 0;
                    foreach ($cart_items as $item) {
                        $item_price = in_array($item['product_id'], [1, 2]) ? $item['price'] * 0.9 : $item['price'];
                        $total_amount += $item_price * $item['quantity'];
                        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, storage, color) 
                                                VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("iiidss", $order_id, $item['product_id'], $item['quantity'], $item_price, $item['storage'], $item['color']);
                        $stmt->execute();

                        $stmt = $conn->prepare("UPDATE product_variants SET stock = stock - ? 
                                                WHERE product_id = ? AND storage = ? AND color = ?");
                        $stmt->bind_param("iiss", $item['quantity'], $item['product_id'], $item['storage'], $item['color']);
                        $stmt->execute();
                    }

                    // Xóa giỏ hàng
                    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();

                    // Tạo bản ghi thanh toán
                    $stmt = $conn->prepare("INSERT INTO payments (order_id, amount, method, status) 
                                            VALUES (?, ?, ?, 'pending')");
                    $stmt->bind_param("ids", $order_id, $total_amount, $payment_method);
                    $stmt->execute();
                    $stmt->close();

                    // Lưu thông tin đơn hàng để sử dụng trong PDF
                    $order_data = [
                        'order_code' => $order_code,
                        'full_name' => $full_name,
                        'email' => $email,
                        'phone' => $phone,
                        'address' => $address,
                        'city' => $city,
                        'district' => $district,
                        'ward' => $ward,
                        'payment_method' => $payment_method,
                        'notes' => $notes,
                        'items' => $cart_items,
                        'total' => $total_amount
                    ];

                    $_SESSION['order_data'] = $order_data; // Lưu dữ liệu đơn hàng
                    header('Location: order_success.php?order_code=' . urlencode($order_data['order_code']));
                    exit;
                } else {
                    $error = "Lỗi khi tạo đơn hàng: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
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

$shipping_fee = 0;
$tax = 0;
$final_total = $total_price + $shipping_fee + $tax;
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
            position: sticky;
            top: 2rem;
            height: fit-content;
        }

        .summary-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-items {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 1.5rem;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 1rem;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .item-price {
            font-weight: 600;
            color: var(--accent-color);
            font-size: 0.9rem;
        }

        .item-quantity {
            background: var(--background-color);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-left: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-row:last-child {
            border-bottom: none;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 2px solid var(--border-color);
        }

        .summary-label {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .summary-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .summary-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .place-order-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--accent-color), var(--success-color));
            color: white;
            border: none;
            padding: 1rem;
            border-radius: var(--border-radius);
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .place-order-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .place-order-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .back-to-cart {
            width: 100%;
            background: none;
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            padding: 0.75rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .back-to-cart:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .security-badge {
            background: linear-gradient(135deg, rgba(72, 187, 120, 0.1), rgba(56, 178, 172, 0.1));
            border: 1px solid var(--success-color);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .security-badge i {
            color: var(--success-color);
            font-size: 1.2rem;
        }

        .security-text {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        @media (max-width: 1024px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .order-summary {
                position: static;
                order: -1;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .checkout-progress {
                padding: 1rem;
            }

            .progress-step {
                padding: 0 1rem;
            }

            .progress-step:not(:last-child)::after {
                width: 2rem;
                right: -1rem;
            }

            .progress-text {
                display: none;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .checkout-form,
            .order-summary {
                padding: 1.5rem;
            }

            .qr-code-image {
                max-width: 150px;
            }
        }

        @media (max-width: 480px) {
            .checkout-progress {
                padding: 1rem 0.5rem;
            }

            .progress-step {
                padding: 0 0.5rem;
            }

            .progress-step:not(:last-child)::after {
                width: 1rem;
                right: -0.5rem;
            }

            .section-title {
                font-size: 1.1rem;
            }

            .form-input {
                padding: 0.625rem 0.75rem;
            }

            .qr-code-image {
                max-width: 120px;
            }
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid var(--accent-color);
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="checkout-progress">
            <div class="progress-step completed">
                <div class="progress-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <span class="progress-text">Giỏ hàng</span>
            </div>
            <div class="progress-step active">
                <div class="progress-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <span class="progress-text">Thanh toán</span>
            </div>
            <div class="progress-step">
                <div class="progress-icon">
                    <i class="fas fa-check"></i>
                </div>
                <span class="progress-text">Hoàn tất</span>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="checkout-content">
            <div class="checkout-form">
                <form method="POST" id="checkoutForm">
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-shipping-fast"></i>
                            Thông tin giao hàng
                        </h3>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Họ và tên</label>
                                <input type="text" name="full_name" class="form-input" required
                                    value="<?= isset($full_name) ? htmlspecialchars($full_name) : '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" class="form-input" required
                                    value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-input" required
                                    value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required">Thành phố</label>
                                <select name="city" class="form-input form-select" required>
                                    <option value="">Chọn thành phố</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?= htmlspecialchars($city['name']) ?>" <?= isset($city_selected) && $city_selected === $city['name'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($city['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Quận/Huyện</label>
                                <select name="district" class="form-input form-select" required>
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Phường/Xã</label>
                                <select name="ward" class="form-input form-select" required>
                                    <option value="">Chọn phường/xã</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label required">Địa chỉ</label>
                            <input type="text" name="address" class="form-input" placeholder="Số nhà, tên đường"
                                required value="<?= isset($address) ? htmlspecialchars($address) : '' ?>">
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="notes" class="form-input form-textarea"
                                placeholder="Ghi chú thêm về đơn hàng (tùy chọn)"><?= isset($notes) ? htmlspecialchars($notes) : '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-credit-card"></i>
                            Phương thức thanh toán
                        </h3>

                        <div class="payment-methods">
                            <div class="payment-method <?= isset($payment_method) && $payment_method === 'cod' ? 'selected' : '' ?>"
                                data-method="cod">
                                <input type="radio" name="payment_method" value="cod" class="payment-radio"
                                    <?= isset($payment_method) && $payment_method === 'cod' ? 'checked' : 'checked' ?>>
                                <div class="payment-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-title">Thanh toán khi nhận hàng</div>
                                    <div class="payment-desc">Thanh toán bằng tiền mặt khi nhận hàng</div>
                                </div>
                            </div>

                            <div class="payment-method <?= isset($payment_method) && $payment_method === 'bank' ? 'selected' : '' ?>"
                                data-method="bank">
                                <input type="radio" name="payment_method" value="bank" class="payment-radio"
                                    <?= isset($payment_method) && $payment_method === 'bank' ? 'checked' : '' ?>>
                                <div class="payment-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-title">Chuyển khoản ngân hàng</div>
                                    <div class="payment-desc">Chuyển khoản qua ATM/Internet Banking</div>
                                </div>
                            </div>

                            <div class="payment-method <?= isset($payment_method) && $payment_method === 'momo' ? 'selected' : '' ?>"
                                data-method="momo">
                                <input type="radio" name="payment_method" value="momo" class="payment-radio"
                                    <?= isset($payment_method) && $payment_method === 'momo' ? 'checked' : '' ?>>
                                <div class="payment-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="payment-info">
                                    <div class="payment-title">Ví MoMo</div>
                                    <div class="payment-desc">Thanh toán qua ứng dụng MoMo</div>
                                </div>
                            </div>
                        </div>

                        <div class="qr-code-container" id="qrCodeContainer">
                            <img src="assets/images/qr-code.png" alt="QR Code Ngân Hàng" class="qr-code-image">
                            <div class="qr-code-info">
                                <p><strong>Ngân hàng:</strong> Vietcombank</p>
                                <p><strong>Số tài khoản:</strong> 1234567890123</p>
                                <p><strong>Chủ tài khoản:</strong> Nguyễn Văn A</p>
                                <p><strong>Nội dung chuyển khoản:</strong> Thanh toán đơn hàng
                                    <?php echo isset($order_code) ? htmlspecialchars($order_code) : '[Mã đơn hàng]'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="place_order" value="1">
                    <button type="submit" class="place-order-btn"><i class="fas fa-check-circle"></i> Đặt hàng</button>
                    <a href="cart.php" class="back-to-cart"><i class="fas fa-arrow-left"></i> Quay lại giỏ hàng</a>
                </form>

                <div class="security-badge">
                    <i class="fas fa-shield-alt"></i>
                    <span class="security-text">Thông tin của bạn được bảo mật an toàn</span>
                </div>
            </div>

            <div class="order-summary">
                <h3 class="summary-title">
                    <i class="fas fa-receipt"></i>
                    Tóm tắt đơn hàng
                </h3>

                <div class="order-items">
                    <?php foreach ($cart_items as $item): ?>
                        <?php
                        $item_price = in_array($item['product_id'], [1, 2]) ? $item['price'] * 0.9 : $item['price'];
                        $subtotal = $item_price * $item['quantity'];
                        ?>
                        <div class="order-item">
                            <img src="assets/products/<?= htmlspecialchars($item['image']) ?>"
                                alt="<?= htmlspecialchars($item['name']) ?>" class="item-image">
                            <div class="item-info">
                                <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="item-variant"><?= htmlspecialchars($item['storage']) ?> -
                                    <?= htmlspecialchars($item['color']) ?>
                                </div>
                                <div class="item-price">$<?= number_format($item_price, 2) ?></div>
                            </div>
                            <div class="item-quantity">×<?= $item['quantity'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Tạm tính</span>
                    <span class="summary-value">$<?= number_format($total_price, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Phí vận chuyển</span>
                    <span class="summary-value">Miễn phí</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Thuế VAT</span>
                    <span class="summary-value">$0.00</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Tổng cộng</span>
                    <span class="summary-value summary-total">$<?= number_format($final_total, 2) ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="scripts/header.js"></script>
    <script src="scripts/product.js"></script>
    <script>
        const cities = <?php echo json_encode($cities); ?>;

        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', () => {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                method.classList.add('selected');
                method.querySelector('input[type="radio"]').checked = true;

                const qrCodeContainer = document.getElementById('qrCodeContainer');
                if (method.dataset.method === 'bank') {
                    qrCodeContainer.classList.add('active');
                } else {
                    qrCodeContainer.classList.remove('active');
                }
            });
        });

        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            const button = this.querySelector('.place-order-btn');
            button.classList.add('loading');
            button.disabled = true;

            document.querySelectorAll('.form-input.error, .form-select.error').forEach(input => input.classList.remove('error'));

            const inputs = this.querySelectorAll('input[required], select[required]');
            let valid = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('error');
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
                button.classList.remove('loading');
                button.disabled = false;
                alert('Vui lòng điền đầy đủ thông tin bắt buộc!');
            }
        });

        const citySelect = document.querySelector('select[name="city"]');
        const districtSelect = document.querySelector('select[name="district"]');
        const wardSelect = document.querySelector('select[name="ward"]');

        citySelect.addEventListener('change', async () => {
            const cityCode = cities.find(city => city.name === citySelect.value)?.code;
            districtSelect.innerHTML = '<option value="">Chọn quận/huyện</option>';
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            if (cityCode) {
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`);
                    const data = await response.json();
                    data.districts.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.name;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                } catch (error) {
                    console.error('Lỗi khi lấy danh sách quận/huyện:', error);
                }
            }
        });

        districtSelect.addEventListener('change', async () => {
            const cityCode = cities.find(city => city.name === citySelect.value)?.code;
            const districtName = districtSelect.value;
            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            if (cityCode && districtName) {
                try {
                    const response = await fetch(`https://provinces.open-api.vn/api/p/${cityCode}?depth=2`);
                    const data = await response.json();
                    const district = data.districts.find(d => d.name === districtName);
                    if (district) {
                        const wardResponse = await fetch(`https://provinces.open-api.vn/api/d/${district.code}?depth=2`);
                        const wardData = await wardResponse.json();
                        wardData.wards.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.name;
                            option.textContent = ward.name;
                            wardSelect.appendChild(option);
                        });
                    }
                } catch (error) {
                    console.error('Lỗi khi lấy danh sách phường/xã:', error);
                }
            }
        });
    </script>
</body>

</html>