<?php
require_once '../config/db.php';

// Kiểm tra quyền admin

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $variant_id = intval($_POST['variant_id'] ?? 0);
    $quantity_added = intval($_POST['quantity_added'] ?? 0);

    if ($quantity_added <= 0 || $variant_id <= 0) {
        $errors[] = "Vui lòng nhập thông tin hợp lệ: Số lượng thêm vào phải lớn hơn 0 và chọn một biến thể sản phẩm.";
    } else {
        // Kiểm tra xem variant_id có tồn tại
        $stmt = $conn->prepare("SELECT id, stock_quantity FROM product_variants WHERE id = ? AND status = 1");
        $stmt->bind_param("i", $variant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $errors[] = "Biến thể sản phẩm không tồn tại hoặc không hoạt động.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            // Cập nhật tồn kho trong bảng product_variants
            $stmt = $conn->prepare("UPDATE product_variants SET stock_quantity = stock_quantity + ? WHERE id = ?");
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ii", $quantity_added, $variant_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute update stock failed: " . $stmt->error);
            }

            $conn->commit();
            $success = true;
            $_SESSION['temp_message'] = ['type' => 'success', 'content' => "Đã cập nhật thêm $quantity_added sản phẩm vào kho cho biến thể ID: $variant_id."];

            // Ghi log
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'add_variant_stock', ?)");
            if ($log_stmt === false) {
                error_log("Log prepare failed: " . $conn->error);
            } else {
                $details = "Added $quantity_added units to product variant ID: $variant_id stock.";
                $log_stmt->bind_param("is", $_SESSION['user_id'], $details);
                $log_stmt->execute();
                $log_stmt->close();
            }

        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Lỗi khi cập nhật tồn kho: " . $e->getMessage();
            error_log("Inventory update failed: " . $e->getMessage());
        } finally {
            if (isset($stmt)) {
                $stmt->close();
            }
        }
    }

    // Nếu là AJAX request, trả về JSON
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => empty($errors),
            'message' => empty($errors) ? (isset($_SESSION['temp_message']) ? $_SESSION['temp_message']['content'] : 'Thao tác thành công!') : implode('<br>', $errors),
            'refresh_page' => true
        ]);
        if (isset($_SESSION['temp_message'])) {
            unset($_SESSION['temp_message']);
        }
        exit;
    }
}

// Lấy danh sách biến thể sản phẩm
$variants = $conn->query("
    SELECT pv.id, pv.variant_code, pv.stock_quantity, p.product_name
    FROM product_variants pv
    JOIN products p ON pv.product_id = p.id
    WHERE pv.status = 1 AND p.status = 1
    ORDER BY p.product_name, pv.variant_code ASC
")->fetch_all(MYSQLI_ASSOC);

require 'admin_header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý tồn kho | Apple Store VN</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Giữ nguyên các style hiện có */
        .hidden {
            display: none !important;
        }
        .admin-container {
            padding: 30px;
            max-width: 95%;
            margin: 20px auto;
        }
        .inventory-form-container {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            text-align: center;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .inventory-form-container h2 {
            margin-top: 0;
            margin-bottom: 25px;
            color: var(--primary-color);
            text-align: center;
        }
        .inventory-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            align-items: end;
        }
        @media (max-width: 768px) {
            .inventory-form {
                grid-template-columns: 1fr;
            }
        }
        .form-group {
            margin-bottom: 0;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: var(--text-color);
            font-size: 1.1rem;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            box-sizing: border-box;
            background: var(--input-bg);
            color: var(--text-color);
            font-size: 1rem;
        }
        .button-group {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
        }
        .button-group button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .button-group button.primary {
            background: var(--primary-color);
            color: #fff;
        }
        .button-group button.primary:hover {
            background: var(--primary-color-dark);
            transform: translateY(-2px);
        }
        .current-stock-container {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin-top: 40px;
        }
        .current-stock-container h2 {
            margin-top: 0;
            margin-bottom: 25px;
            color: var(--primary-color);
            text-align: center;
        }
        .stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            font-size: 0.95rem;
        }
        .stock-table th, .stock-table td {
            border: 1px solid var(--border-color);
            padding: 15px;
            text-align: left;
        }
        .stock-table th {
            background: var(--table-header-bg);
            color: var(--table-header-text);
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 1;
            font-size: 1rem;
        }
        .stock-table tbody tr:nth-child(even) {
            background: var(--table-row-even-bg);
        }
        .stock-table tbody tr:hover {
            background: var(--table-row-hover-bg);
        }
        .notification {
            opacity: 1;
            transition: opacity 0.5s ease-out;
            padding: 15px 25px;
            margin-bottom: 25px;
            border-radius: 5px;
            color: #fff;
            font-size: 1.05rem;
        }
        .notification.fade-out {
            opacity: 0;
        }
        .notification p {
            margin: 0;
        }
        .notification.error-notification {
            background: var(--danger-color);
        }
        .notification.success-notification {
            background: var(--success-color);
        }
    </style>
</head>
<body>
    <main class="admin-container">
        <div id="notification-area"></div>

        <section class="inventory-form-container">
            <h2>Cập nhật số lượng biến thể sản phẩm trong kho</h2>
            <form class="inventory-form" method="POST">
                <div class="form-group">
                    <label for="variant_id">Biến thể sản phẩm:</label>
                    <select id="variant_id" name="variant_id" required>
                        <option value="">Chọn biến thể sản phẩm</option>
                        <?php foreach ($variants as $variant): ?>
                            <option value="<?= htmlspecialchars($variant['id']) ?>">
                                <?= htmlspecialchars($variant['product_name'] . ' - ' . $variant['variant_code']) ?> 
                                (Hiện có: <?= htmlspecialchars($variant['stock_quantity']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="quantity_added">Số lượng thêm vào:</label>
                    <input type="number" id="quantity_added" name="quantity_added" required min="1" value="1">
                </div>
                <div class="button-group">
                    <button type="submit" class="primary">Cập nhật kho</button>
                </div>
            </form>
        </section>

        <section class="current-stock-container">
            <h2>Số lượng tồn kho hiện tại</h2>
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>ID Biến thể</th>
                        <th>Tên sản phẩm</th>
                        <th>Mã biến thể</th>
                        <th>Số lượng tồn</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($variants)): ?>
                        <tr>
                            <td colspan="4">Chưa có biến thể sản phẩm nào trong kho.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($variants as $variant): ?>
                            <tr>
                                <td><?= htmlspecialchars($variant['id']) ?></td>
                                <td><?= htmlspecialchars($variant['product_name']) ?></td>
                                <td><?= htmlspecialchars($variant['variant_code']) ?></td>
                                <td><?= htmlspecialchars($variant['stock_quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Apple Store VN. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            const notificationArea = document.getElementById('notification-area');
            const inventoryForm = document.querySelector('.inventory-form');

            document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
                button.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                });
            });

            const currentPath = window.location.pathname.split('/').pop();
            document.querySelectorAll('.sidebar nav a').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            function showNotification(message, type = 'success') {
                const notificationDiv = document.createElement('div');
                notificationDiv.className = `notification ${type}-notification`;
                notificationDiv.innerHTML = `<p>${message}</p>`;
                notificationArea.prepend(notificationDiv);

                setTimeout(() => {
                    notificationDiv.classList.add('fade-out');
                    setTimeout(() => notificationDiv.remove(), 500);
                }, 3000);
            }

            inventoryForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                try {
                    const response = await fetch('manage_inventory.php', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        showNotification(data.message, 'success');
                        inventoryForm.reset();
                        if (data.refresh_page) {
                            window.location.reload();
                        }
                    } else {
                        showNotification(data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error submitting inventory form:', error);
                    showNotification('Lỗi khi thao tác cập nhật kho.', 'error');
                }
            });

            <?php if (isset($_SESSION['temp_message'])): ?>
                showNotification('<?= htmlspecialchars($_SESSION['temp_message']['content']) ?>', '<?= htmlspecialchars($_SESSION['temp_message']['type']) ?>');
                <?php unset($_SESSION['temp_message']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>