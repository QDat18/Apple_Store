<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = false;

// Xử lý sửa hoặc khóa tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'edit') {
        if (empty($full_name)) {
            $errors[] = "Tên đầy đủ không được để trống.";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("sssi", $full_name, $phone, $address, $id);
            if ($stmt->execute()) {
                $success = true;
                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'edit_user', ?)");
                $details = "Edited user ID: $id";
                $log_stmt->bind_param("is", $_SESSION['user_id'], $details);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
            }
            $stmt->close();
        }
    } elseif ($action === 'lock') {
        $is_verified = $_POST['status'] === 'lock' ? 0 : 1;
        $stmt = $conn->prepare("UPDATE users SET is_verified = ? WHERE id = ?");
        $stmt->bind_param("ii", $is_verified, $id);
        if ($stmt->execute()) {
            $success = true;
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'lock_user', ?)");
            $details = $is_verified ? "Unlocked user ID: $id" : "Locked user ID: $id";
            $log_stmt->bind_param("is", $_SESSION['user_id'], $details);
            $log_stmt->execute();
            $log_stmt->close();
        }
        $stmt->close();
    }
}

// Lấy danh sách người dùng
$users = [];
$stmt = $conn->query("SELECT id, email, full_name, phone, address, role, is_verified FROM users WHERE role = 'customer'");
while ($row = $stmt->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="C:/Apple_Shop/css/admin.css">
</head>
<body>
    <header class="admin-header">
        <h1>Admin Dashboard - Anh Em Rọt Store</h1>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage_products.php"><i class="fas fa-box"></i> Sản phẩm</a>
            <a href="manage_users.php" class="active"><i class="fas fa-users"></i> Người dùng</a>
            <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Đơn hàng</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </nav>
    </header>

    <main class="admin-container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif ($success): ?>
            <div class="alert alert-success">
                <p>Thao tác thành công!</p>
            </div>
        <?php endif; ?>

        <section class="users-grid">
            <h2>Danh sách khách hàng</h2>
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Họ tên</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['id']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['full_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['phone'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['address'] ?? '') ?></td>
                            <td><?= $u['is_verified'] ? 'Hoạt động' : 'Khóa' ?></td>
                            <td>
                                <a href="#" class="action-button edit-user" 
                                   data-id="<?= htmlspecialchars($u['id']) ?>" 
                                   data-full_name="<?= htmlspecialchars($u['full_name'] ?? '') ?>" 
                                   data-phone="<?= htmlspecialchars($u['phone'] ?? '') ?>" 
                                   data-address="<?= htmlspecialchars($u['address'] ?? '') ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="action" value="lock">
                                    <input type="hidden" name="status" value="<?= $u['is_verified'] ? 'lock' : 'unlock' ?>">
                                    <button type="submit" class="action-button" 
                                            onclick="return confirm('Xác nhận <?= $u['is_verified'] ? 'khóa' : 'mở khóa' ?> tài khoản?')">
                                        <i class="fas fa-<?= $u['is_verified'] ? 'lock' : 'unlock' ?>"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <div class="modal" id="edit-user-modal">
            <div class="modal-content">
                <h2>Sửa thông tin người dùng</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit-user-id">
                    <div class="form-group">
                        <label for="edit-full-name">Họ tên</label>
                        <input type="text" name="full_name" id="edit-full-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-phone">Số điện thoại</label>
                        <input type="text" name="phone" id="edit-phone">
                    </div>
                    <div class="form-group">
                        <label for="edit-address">Địa chỉ</label>
                        <textarea name="address" id="edit-address"></textarea>
                    </div>
                    <button type="submit" class="action-button"><i class="fas fa-save"></i> Lưu</button>
                    <button type="button" class="action-button modal-close"><i class="fas fa-times"></i> Đóng</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <script>
        const modal = document.getElementById('edit-user-modal');
        document.querySelectorAll('.edit-user').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('edit-user-id').value = button.dataset.id;
                document.getElementById('edit-full-name').value = button.dataset.full_name;
                document.getElementById('edit-phone').value = button.dataset.phone;
                document.getElementById('edit-address').value = button.dataset.address;
                modal.style.display = 'flex';
            });
        });

        document.querySelector('.modal-close').addEventListener('click', () => {
            modal.style.display = 'none';
        });
    </script>
</body>
</html>