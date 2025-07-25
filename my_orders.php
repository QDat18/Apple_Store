<?php
session_start();
require_once 'config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("Unauthorized access attempt to myorders.php. Redirecting to login.");
    header('Location: /Apple_Shop/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Fetch orders
try {
    $stmt = $conn->prepare("
        SELECT
            o.id, o.order_code, o.order_date, o.total_amount, o.status, o.shipping_address,
            o.full_name, o.email, o.phone_number, o.payment_method
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị truy vấn đơn hàng: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Fetch order items for each order
    foreach ($orders as &$order) {
        $stmt = $conn->prepare("
            SELECT
                oi.quantity, oi.price, p.product_name, pv.variant_image,
                (SELECT vav.value FROM variant_attribute_values vav
                 JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id
                 WHERE pval.variant_id = oi.variant_id AND vav.attribute_id = 1) AS color,
                (SELECT vav.value FROM variant_attribute_values vav
                 JOIN product_variant_attribute_links pval ON vav.id = pval.attribute_value_id
                 WHERE pval.variant_id = oi.variant_id AND vav.attribute_id = 2) AS storage
            FROM order_items oi
            JOIN product_variants pv ON oi.variant_id = pv.id
            JOIN products p ON pv.product_id = p.id
            WHERE oi.order_id = ?
        ");
        if (!$stmt) {
            throw new Exception("Lỗi chuẩn bị truy vấn chi tiết đơn hàng: " . $conn->error);
        }
        $stmt->bind_param("i", $order['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $order['items'] = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (Exception $e) {
    $error = "Đã xảy ra lỗi khi tải danh sách đơn hàng. Vui lòng thử lại.";
    error_log("Order fetch failed: " . $e->getMessage());
    $orders = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Sử Đơn Hàng - Anh Em Rọt Store</title>
    <link rel="icon" href="/Apple_Shop/assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/Apple_Shop/css/header.css">
    <link rel="stylesheet" href="/Apple_Shop/css/product.css">
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
            margin: 4rem auto;
            padding: 2.5rem;
            background-color: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
        }
        .orders-title {
            font-size: 2.5rem;
            color: var(--text-primary);
            margin-bottom: 2.5rem;
            text-align: center;
            font-weight: 700;
        }
        .order-card {
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 1.5rem; /* Reduced padding for simplified view */
            margin-bottom: 1.5rem; /* Reduced margin */
            background-color: #ffffff;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            flex-direction: column; /* Stack elements vertically */
            justify-content: space-between;
            align-items: flex-start; /* Align content to the start */
        }
        .order-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
        .order-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 1rem;
            flex-wrap: wrap; /* Allow wrapping on smaller screens */
            gap: 0.5rem; /* Space between items when wrapping */
        }
        .order-code-summary {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.1rem;
            flex-grow: 1; /* Allow to take available space */
        }
        .order-date-summary, .order-total-summary {
            font-size: 0.95rem;
            color: var(--text-secondary);
        }
        .order-status {
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap; /* Prevent status text from wrapping */
        }
        .order-status.pending { background-color: #ffcc00; color: #664400; } /* Apple Yellow */
        .order-status.processing { background-color: var(--primary-color); }
        .order-status.shipped { background-color: #007aff; } /* Apple Blue */
        .order-status.delivered { background-color: var(--accent-color); }
        .order-status.cancelled { background-color: var(--danger-color); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.8rem 1.8rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            margin-top: 1.5rem;
            font-size: 1rem;
            cursor: pointer;
            border: none;
        }
        .btn-view-details {
            background-color: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-sm);
            margin-top: 1rem; /* Adjust margin for standalone button */
        }
        .btn-view-details:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .btn-view-details .fas {
            margin-right: 8px;
        }

        .error-message {
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1.2rem;
            border-radius: var(--border-radius);
            background-color: #ffebeb; /* Light Red */
            color: var(--danger-color);
            font-weight: 500;
            border: 1px solid var(--danger-color);
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
        .empty-message + .btn {
            display: block;
            width: fit-content;
            margin: 0 auto;
        }

        /* Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.6); /* Black w/ opacity */
            backdrop-filter: blur(5px); /* Frosted glass effect */
            -webkit-backdrop-filter: blur(5px); /* For Safari */
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .modal-content {
            background-color: var(--card-background);
            margin: auto;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
            width: 90%;
            max-width: 800px; /* Increased max-width */
            position: relative;
            animation-name: animatetop;
            animation-duration: 0.4s;
            overflow-y: auto; /* Enable scrolling for modal content */
            max-height: 90vh; /* Max height for modal content */
        }
        @keyframes animatetop {
            from {top: -300px; opacity: 0}
            to {top: 0; opacity: 1}
        }
        .close-button {
            color: var(--text-secondary);
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 15px;
            right: 25px;
            cursor: pointer;
            transition: color 0.2s ease;
        }
        .close-button:hover,
        .close-button:focus {
            color: var(--text-primary);
            text-decoration: none;
        }
        .modal-title {
            font-size: 2rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 700;
            padding-right: 30px; /* Make space for close button */
        }
        .modal-section {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px dashed var(--border-color);
        }
        .modal-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .modal-section h3 {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .modal-details p {
            margin: 0.6rem 0;
            font-size: 1rem;
            color: var(--text-primary);
        }
        .modal-details strong {
            color: var(--text-primary);
            min-width: 150px;
            display: inline-block;
            font-weight: 600;
        }
        .modal-items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
            margin-top: 1rem;
        }
        .modal-items-table th, .modal-items-table td {
            padding: 0.8rem;
            text-align: left;
        }
        .modal-items-table th {
            background-color: var(--background-color);
            font-weight: 700;
            color: var(--text-primary);
            border-radius: 8px; /* Rounded corners for header cells */
        }
        .modal-items-table tbody tr {
            background-color: #fcfcfc;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        .modal-items-table tbody tr:hover {
            background-color: #f0f0f5;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .modal-items-table td:first-child {
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        .modal-items-table td:last-child {
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        .modal-product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                margin: 2rem auto;
                padding: 1.5rem;
            }
            .orders-title {
                font-size: 2rem;
                margin-bottom: 1.5rem;
            }
            .order-card {
                padding: 1rem;
            }
            .order-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.8rem;
                margin-bottom: 1rem;
            }
            .order-code-summary {
                font-size: 1rem;
            }
            .order-date-summary, .order-total-summary {
                font-size: 0.85rem;
            }
            .order-status {
                font-size: 0.75rem;
                padding: 0.4rem 0.9rem;
            }
            .btn-view-details {
                width: 100%;
                padding: 0.7rem 1.2rem;
                font-size: 0.9rem;
                margin-top: 0.8rem;
            }

            /* Modal responsive */
            .modal-content {
                width: 95%;
                padding: 20px;
                max-height: 95vh;
            }
            .modal-title {
                font-size: 1.5rem;
                margin-bottom: 1rem;
                padding-right: 20px;
            }
            .modal-section h3 {
                font-size: 1.1rem;
                margin-bottom: 0.8rem;
            }
            .modal-details p {
                font-size: 0.9rem;
                margin: 0.5rem 0;
            }
            .modal-details strong {
                min-width: 100px;
            }
            .modal-items-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .modal-items-table thead {
                display: none;
            }
            .modal-items-table tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--border-color);
                border-radius: 8px;
                padding: 0.8rem;
                white-space: normal;
            }
            .modal-items-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0.5rem;
                border-bottom: none;
            }
            .modal-items-table td::before {
                content: attr(data-label);
                font-weight: bold;
                margin-right: 0.8rem;
                flex-shrink: 0;
                color: var(--text-primary);
            }
            .modal-product-image {
                width: 50px;
                height: 50px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <h1 class="orders-title">Lịch Sử Đơn Hàng</h1>
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (empty($orders)): ?>
            <p class="empty-message">Bạn chưa có đơn hàng nào. Hãy mua sắm ngay!</p>
            <a href="/Apple_Shop/products/products.php" class="btn btn-view-details"><i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm</a>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-summary">
                        <span class="order-code-summary">Mã đơn hàng: <?= htmlspecialchars($order['order_code']) ?></span>
                        <span class="order-date-summary">Ngày: <?= date('d/m/Y', strtotime($order['order_date'])) ?></span>
                        <span class="order-total-summary">Tổng: <?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</span>
                        <span class="order-status <?= strtolower($order['status']) ?>">
                            <?= htmlspecialchars($order['status'] === 'Pending' ? 'Chờ xử lý' :
                                ($order['status'] === 'Processing' ? 'Đang xử lý' :
                                ($order['status'] === 'Shipped' ? 'Đã giao' :
                                ($order['status'] === 'Delivered' ? 'Hoàn thành' : 'Đã hủy')))) ?>
                        </span>
                    </div>
                    <button class="btn btn-view-details" data-order='<?= json_encode($order) ?>'>
                        <i class="fas fa-info-circle"></i> Xem chi tiết
                    </button>
                </div>
            <?php endforeach; ?>
            <a href="/Apple_Shop/index.php" class="btn btn-view-details" style="display: block; width: fit-content; margin: 2rem auto 0;"><i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm</a>
        <?php endif; ?>
    </div>

    <div id="orderDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2 class="modal-title">Chi Tiết Đơn Hàng</h2>

            <div class="modal-section">
                <h3>Thông tin chung</h3>
                <div class="modal-details">
                    <p><strong>Mã đơn hàng:</strong> <span id="modal-order-code"></span></p>
                    <p><strong>Ngày đặt hàng:</strong> <span id="modal-order-date"></span></p>
                    <p><strong>Trạng thái:</strong> <span id="modal-order-status" class="order-status"></span></p>
                    <p><strong>Tổng tiền:</strong> <span id="modal-total-amount"></span></p>
                </div>
            </div>

            <div class="modal-section">
                <h3>Thông tin người nhận</h3>
                <div class="modal-details">
                    <p><strong>Họ và tên:</strong> <span id="modal-full-name"></span></p>
                    <p><strong>Địa chỉ:</strong> <span id="modal-shipping-address"></span></p>
                    <p><strong>Email:</strong> <span id="modal-email"></span></p>
                    <p><strong>Số điện thoại:</strong> <span id="modal-phone-number"></span></p>
                    <p><strong>Phương thức thanh toán:</strong> <span id="modal-payment-method"></span></p>
                </div>
            </div>

            <div class="modal-section">
                <h3>Sản phẩm</h3>
                <table class="modal-items-table">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>Thông số</th>
                            <th>Số lượng</th>
                            <th>Giá</th>
                            <th>Tổng</th>
                        </tr>
                    </thead>
                    <tbody id="modal-order-items">
                        </tbody>
                </table>
            </div>
            <a id="modal-view-invoice-btn" href="#" class="btn btn-view-details" style="display: block; width: fit-content; margin: 2rem auto 0;">
                <i class="fas fa-file-pdf"></i> Xem hóa đơn
            </a>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/header.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById("orderDetailsModal");
            var closeButton = document.querySelector(".close-button");
            var detailButtons = document.querySelectorAll(".btn-view-details");

            // When the user clicks on the button, open the modal
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (this.dataset.order) { // Check if it's a detail button, not "Continue Shopping"
                        var order = JSON.parse(this.dataset.order);
                        populateModal(order);
                        modal.style.display = "flex"; // Use flex to center
                    }
                });
            });

            // When the user clicks on <span> (x), close the modal
            closeButton.onclick = function() {
                modal.style.display = "none";
            }

            // When the user clicks anywhere outside of the modal, close it
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            function populateModal(order) {
                document.getElementById('modal-order-code').textContent = order.order_code;
                document.getElementById('modal-order-date').textContent = new Date(order.order_date).toLocaleDateString('vi-VN', {
                    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });

                var statusElement = document.getElementById('modal-order-status');
                statusElement.textContent = formatOrderStatus(order.status);
                statusElement.className = 'order-status ' + order.status.toLowerCase(); // Update class for styling

                document.getElementById('modal-total-amount').textContent = formatCurrency(order.total_amount) + ' VNĐ';
                document.getElementById('modal-full-name').textContent = order.full_name;
                document.getElementById('modal-shipping-address').textContent = order.shipping_address;
                document.getElementById('modal-email').textContent = order.email;
                document.getElementById('modal-phone-number').textContent = order.phone_number;
                document.getElementById('modal-payment-method').textContent = formatPaymentMethod(order.payment_method);

                var itemsTableBody = document.getElementById('modal-order-items');
                itemsTableBody.innerHTML = ''; // Clear previous items

                order.items.forEach(item => {
                    var row = itemsTableBody.insertRow();
                    row.innerHTML = `
                        <td data-label="Hình ảnh"><img src="/Apple_Shop/${item.variant_image}" alt="${item.product_name}" class="modal-product-image"></td>
                        <td data-label="Sản phẩm">${item.product_name}</td>
                        <td data-label="Thông số">${formatSpecs(item.storage, item.color)}</td>
                        <td data-label="Số lượng">${item.quantity}</td>
                        <td data-label="Giá">${formatCurrency(item.price)} VNĐ</td>
                        <td data-label="Tổng">${formatCurrency(item.price * item.quantity)} VNĐ</td>
                    `;
                });

                // Update invoice button link
                var invoiceButton = document.getElementById('modal-view-invoice-btn');
                invoiceButton.href = `/Apple_Shop/sales_invoice.php?order_code=${order.order_code}`;
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN').format(amount);
            }

            function formatSpecs(storage, color) {
                let specs = [];
                if (storage) specs.push(storage);
                if (color) specs.push(color);
                return specs.join(' - ');
            }

            function formatOrderStatus(status) {
                switch (status) {
                    case 'Pending': return 'Chờ xử lý';
                    case 'Processing': return 'Đang xử lý';
                    case 'Shipped': return 'Đã giao';
                    case 'Delivered': return 'Hoàn thành';
                    case 'Cancelled': return 'Đã hủy';
                    default: return status;
                }
            }

            function formatPaymentMethod(method) {
                switch (method) {
                    case 'cod': return 'Thanh toán khi nhận hàng';
                    case 'bank': return 'Chuyển khoản ngân hàng';
                    case 'momo': return 'Ví MoMo';
                    default: return method;
                }
            }
        });
    </script>
</body>
</html>