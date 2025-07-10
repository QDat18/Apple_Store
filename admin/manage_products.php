<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    $id = intval($_POST['id'] ?? 0);

    if (empty($name) || $price <= 0 || $stock < 0) {
        $errors[] = "Vui lòng nhập đầy đủ thông tin hợp lệ.";
    }

    $image = $_POST['existing_image'] ?? '';
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../assets/images/";
        $image = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image;
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Chỉ hỗ trợ file ảnh JPG, JPEG, PNG, GIF.";
        } elseif ($_FILES['image']['size'] > 5000000) {
            $errors[] = "Ảnh không được vượt quá 5MB.";
        } elseif (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $errors[] = "Lỗi upload ảnh.";
        }
    }

    if (empty($errors)) {
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sdssi", $name, $price, $description, $image, $stock);
        } elseif ($action === 'edit') {
            $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ?, stock = ? WHERE id = ?");
            $stmt->bind_param("sdssii", $name, $price, $description, $image, $stock, $id);
        }

        if ($stmt->execute()) {
            $success = true;
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
            $log_action = $action === 'add' ? 'add_product' : 'edit_product';
            $details = $action === 'add' ? "Added product: $name" : "Edited product ID: $id";
            $log_stmt->bind_param("iss", $_SESSION['user_id'], $log_action, $details);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
        }
        $stmt->close();
    }
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'delete_product', ?)");
        $details = "Deleted product ID: $id";
        $log_stmt->bind_param("is", $_SESSION['user_id'], $details);
        $log_stmt->execute();
        $log_stmt->close();
        header("Location: manage_products.php?success=1");
        exit;
    }
    $stmt->close();
}

$products = [];
$stmt = $conn->query("SELECT id, name, price, description, image, stock FROM products ORDER BY created_at DESC");
while ($row = $stmt->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Anh Em Rọt Store</h2>
            <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
        </div>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="manage_products.php" class="active"><i class="fas fa-box"></i> Sản phẩm</a>
            <a href="manage_users.php"><i class="fas fa-users"></i> Người dùng</a>
            <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Đơn hàng</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </nav>
    </div>

    <main class="admin-container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php elseif ($success || isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <p>Thao tác thành công!</p>
            </div>
        <?php endif; ?>

        <section class="manage-form">
            <h2>Thêm/Sửa sản phẩm</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="existing_image" id="existing_image">
                <div class="form-group">
                    <label for="name">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="price">Giá ($)</label>
                    <input type="number" name="price" id="price" step="0.01" required min="0">
                </div>
                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea name="description" id="description"></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Hình ảnh</label>
                    <input type="file" name="image" id="image" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="stock">Tồn kho</label>
                    <input type="number" name="stock" id="stock" required min="0">
                </div>
                <button type="submit" name="submit" class="action-button"><i class="fas fa-save"></i> Lưu</button>
            </form>
        </section>

        <section class="products-grid">
            <h2>Danh sách sản phẩm</h2>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Hình ảnh</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']) ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td>$<?= number_format($p['price'], 2) ?></td>
                            <td><?= htmlspecialchars($p['stock']) ?></td>
                            <td><img src="../assets/images/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" width="50"></td>
                            <td>
                                <a href="#" class="action-button edit-product" 
                                   data-id="<?= htmlspecialchars($p['id']) ?>" 
                                   data-name="<?= htmlspecialchars($p['name']) ?>" 
                                   data-price="<?= htmlspecialchars($p['price']) ?>" 
                                   data-description="<?= htmlspecialchars($p['description']) ?>" 
                                   data-image="<?= htmlspecialchars($p['image']) ?>" 
                                   data-stock="<?= htmlspecialchars($p['stock']) ?>">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <a href="?delete=<?= $p['id'] ?>" class="action-button delete-product" 
                                   onclick="return confirm('Xác nhận xóa sản phẩm?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <script>
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('action').value = 'edit';
                document.getElementById('id').value = button.dataset.id;
                document.getElementById('name').value = button.dataset.name;
                document.getElementById('price').value = button.dataset.price;
                document.getElementById('description').value = button.dataset.description;
                document.getElementById('existing_image').value = button.dataset.image;
                document.getElementById('stock').value = button.dataset.stock;
            });
        });
    </script>
    <script src="../js/admin.js"></script>
</body>
</html>