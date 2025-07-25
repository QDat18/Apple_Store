<?php
session_start();
require_once '../config/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$errors = [];
$success = false;

// Variables for search, sort, and edit pre-fill
$search_query = trim(filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
$sort_by = filter_input(INPUT_GET, 'sort_by', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'id';
$sort_order = filter_input(INPUT_GET, 'sort_order', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'ASC';

// Validate sort parameters
$allowed_sort_by = ['id', 'name'];
$allowed_sort_order = ['ASC', 'DESC'];

if (!in_array($sort_by, $allowed_sort_by)) {
    $sort_by = 'id';
}
if (!in_array($sort_order, $allowed_sort_order)) {
    $sort_order = 'ASC';
}

// Xử lý các hành động thêm, sửa, xóa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
    $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '');
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?? 0;

    if (empty($name)) {
        $errors[] = "Tên nhà cung cấp không được để trống.";
    }
    if (!empty($phone) && !preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors[] = "Số điện thoại không hợp lệ.";
    }

    if (empty($errors)) {
        try {
            if ($action === 'add') {
                $stmt = $conn->prepare("INSERT INTO suppliers (name, phone, address) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $phone, $address);
            } elseif ($action === 'edit') {
                $stmt = $conn->prepare("UPDATE suppliers SET name = ?, phone = ?, address = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $phone, $address, $id);
            }

            if ($stmt->execute()) {
                $success = true;
                $log_action = $action === 'add' ? 'add_supplier' : 'edit_supplier';
                $details = $action === 'add' ? "Added supplier: $name" : "Updated supplier ID: $id";

                $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
                $log_stmt->bind_param("iss", $_SESSION['user_id'], $log_action, $details);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                $errors[] = "Lỗi cơ sở dữ liệu: " . $stmt->error;
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                $errors[] = "Tên nhà cung cấp đã tồn tại. Vui lòng chọn tên khác.";
            } else {
                $errors[] = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
            }
        }
    }
} elseif (isset($_GET['delete'])) {
    $id_to_delete = filter_input(INPUT_GET, 'delete', FILTER_VALIDATE_INT);
    if ($id_to_delete > 0) {
        $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id_to_delete);
        if ($stmt->execute()) {
            $success = true;
            $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
            $details = "Deleted supplier ID: $id_to_delete";
            $log_stmt->bind_param("iss", $_SESSION['user_id'], 'delete_supplier', $details);
            $log_stmt->execute();
            $log_stmt->close();
        } else {
            $errors[] = "Lỗi khi xóa nhà cung cấp: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errors[] = "ID nhà cung cấp không hợp lệ để xóa.";
    }
}

// Lấy danh sách nhà cung cấp có lọc và sắp xếp
$suppliers = [];
$sql = "SELECT id, name, phone, address FROM suppliers";
$where_clauses = [];
$params = [];
$types = "";

if (!empty($search_query)) {
    $where_clauses[] = "(name LIKE ? OR phone LIKE ? OR address LIKE ?)";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $types .= "sss";
}

if (!empty($where_clauses)) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}

$sql .= " ORDER BY {$sort_by} {$sort_order}";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $bind_args = [];
    $bind_args[] = $types;
    foreach ($params as $key => $value) {
        $bind_args[] = &$params[$key];
    }
    call_user_func_array([$stmt, 'bind_param'], $bind_args);
}

$stmt->execute();
$result = $stmt->get_result();
$suppliers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Truy vấn dữ liệu cho biểu đồ (giả định có bảng products với cột supplier_id)
$stmt = $conn->prepare("SELECT s.name, COUNT(p.id) as product_count FROM suppliers s LEFT JOIN products p ON s.id = p.supplier_id GROUP BY s.id, s.name ORDER BY product_count DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
$supplier_product_counts = [];
$supplier_names = [];
while ($row = $result->fetch_assoc()) {
    $supplier_names[] = $row['name'];
    $supplier_product_counts[] = $row['product_count'];
}
$stmt->close();

require 'admin_header.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Nhà cung cấp | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        :root {

            --border-color: #ced4da;
            --success-color: #28a745;
            --error-color: #dc3545;
            --border-radius: 8px;
            --shadow-medium: 0 4px 12px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --dark-bg-primary: #343a40;
            --dark-bg-secondary: #2c3034;
            --dark-text-primary: #f8f9fa;
            --dark-text-secondary: #adb5bd;
            --dark-success-color: #2ed573;
            --dark-error-color: #ff6b6b;
            --card-bg: var(--bg-secondary);
            --shadow: var(--shadow-medium);
            --input-bg: var(--bg-secondary);
            --text-color: var(--text-primary);
            --text-color-light: var(--text-secondary);
            --table-header-bg: var(--primary-color);
            --table-header-text: #fff;
            --table-row-even-bg: var(--secondary-color);
            --table-row-hover-bg: #f1f1f1;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --card-bg: var(--dark-bg-secondary);
                --shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                --input-bg: var(--dark-bg-secondary);
                --text-color: var(--dark-text-primary);
                --text-color-light: var(--dark-text-secondary);
                --table-header-bg: var(--dark-bg-primary);
                --table-header-text: var(--dark-text-primary);
                --table-row-even-bg: var(--dark-bg-primary);
                --table-row-hover-bg: #3A3A3A;
            }

            .messages.success {
                background-color: rgba(46, 213, 115, 0.2);
                color: var(--dark-success-color);
                border-color: var(--dark-success-color);
            }

            .messages.error {
                background-color: rgba(255, 107, 107, 0.2);
                color: var(--dark-error-color);
                border-color: var(--dark-error-color);
            }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            color: var(--text-primary);
        }

        .admin-container-page {
            padding: 30px;
            max-width: 1200px;
            margin: 20px auto;
            color: var(--text-color);
        }

        .table-card,
        .card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .table-card h2,
        .card h2 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 25px;
            text-align: center;
            font-size: 2rem;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .action-bar .search-box {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-bar .search-box input[type="text"] {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--input-bg);
            color: var(--text-color);
            font-size: 1rem;
            width: 250px;
            transition: border-color var(--transition);
        }

        .action-bar .search-box input[type="text"]:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .action-bar .search-box button {
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition);
        }

        .action-bar .search-box button:hover {
            background-color: var(--primary-color-dark);
        }

        .btn-add-new {
            padding: 10px 20px;
            background-color: var(--success-color);
            color: #fff;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-add-new:hover {
            background-color: #218838;
        }

        .supplier-table,
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 1rem;
        }

        .supplier-table th,
        .supplier-table td,
        .data-table th,
        .data-table td {
            border: 1px solid var(--border-color);
            padding: 12px;
            text-align: left;
            color: var(--text-color); /* Apply text color to table cells */
        }

        .supplier-table th,
        .data-table th {
            background: var(--table-header-bg);
            color: var(--table-header-text);
            font-weight: bold;
        }

        .supplier-table th a,
        .data-table th a {
            color: var(--table-header-text);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .supplier-table tbody tr:nth-child(even),
        .data-table tbody tr:nth-child(even) {
            background: var(--table-row-even-bg);
        }

        .supplier-table tbody tr:hover,
        .data-table tbody tr:hover {
            background: var(--table-row-hover-bg);
        }

        .supplier-table img,
        .data-table img {
            max-width: 50px;
            height: auto;
            border-radius: var(--border-radius);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .action-button {
            padding: 8px 12px;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color var(--transition), transform 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-button.edit {
            background-color: var(--primary-color);
        }

        .action-button.edit:hover {
            background-color: var(--primary-color-dark);
            transform: translateY(-1px);
        }

        .action-button.delete {
            background-color: #ffffff;
        }

        .action-button.delete:hover {
            background-color: #c82333;
            transform: translateY(-1px);
        }

        .action-button.view-button {
            background-color: var(--secondary-color);
        }

        .action-button.view-button:hover {
            background-color: var(--dark-bg-primary); /* Using a darker background for hover in dark mode */
            transform: translateY(-1px);
        }

        .messages {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: var(--border-radius);
            font-weight: bold;
            transition: all var(--transition);
        }

        .messages ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .messages.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .messages.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: auto;
            padding: 30px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            width: 90%;
            max-width: 600px;
            box-shadow: var(--shadow);
            position: relative;
            animation: fadeIn 0.3s ease-out;
            color: var(--text-color); /* Apply text color to modal content */
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-close-btn {
            color: var(--text-secondary);
            position: absolute;
            top: 15px;
            right: 25px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }

        .modal-close-btn:hover,
        .modal-close-btn:focus {
            color: var(--text-primary);
        }

        .modal .form-group label {
            color: var(--text-color-light);
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .modal .form-group input,
        .modal .form-group textarea,
        .modal .form-group select {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: var(--input-bg);
            color: var(--text-color);
            font-size: 1rem;
            transition: border-color var(--transition);
        }

        .modal .form-group input:focus,
        .modal .form-group textarea:focus,
        .modal .form-group select:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .modal .button-group {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal .button-group button {
            padding: 10px 20px;
            border-radius: var(--border-radius);
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color var(--transition);
        }

        .modal .button-group .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .modal .button-group .btn-primary:hover {
            background-color: var(--primary-color-dark);
        }

        .modal .button-group .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .modal .button-group .btn-secondary:hover {
            background-color: #5a6268;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }

            .form-grid .full-width {
                grid-column: 1 / -1;
            }
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 5px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--text-primary);
            transition: background-color var(--transition);
        }

        .pagination a:hover {
            background-color: var(--table-row-hover-bg);
        }

        .pagination span.current {
            background-color: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
            font-weight: bold;
        }

        .subcategory-list {
            margin-left: 20px;
            border-left: 2px solid var(--border-color);
            padding-left: 10px;
            margin-top: 10px;
            list-style: none;
        }

        .subcategory-list li {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <main class="admin-container-page">
        <section class="table-card">
            <h2>Quản lý Nhà cung cấp</h2>

            <?php if (!empty($errors)): ?>
                <div class="messages error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="messages success">
                    <p>Thao tác thành công!</p>
                </div>
            <?php endif; ?>

            <div class="action-bar">
                <form action="manage_suppliers.php" method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên, SĐT, địa chỉ..."
                        value="<?= htmlspecialchars($search_query) ?>">
                    <button type="submit"><i class="fas fa-search"></i> Tìm kiếm</button>
                </form>
                <button type="button" class="btn-add-new" id="add-supplier-btn">
                    <i class="fas fa-plus"></i> Thêm Nhà cung cấp mới
                </button>
            </div>

            <?php if (empty($suppliers)): ?>
                <p>Chưa có nhà cung cấp nào.</p>
            <?php else: ?>
                <table class="supplier-table">
                    <thead>
                        <tr>
                            <th>
                                <a
                                    href="?<?= http_build_query(array_merge($_GET, ['sort_by' => 'id', 'sort_order' => ($sort_by === 'id' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])) ?>">
                                    ID
                                    <?php if ($sort_by === 'id'): ?><i
                                            class="fas fa-sort<?= $sort_order === 'ASC' ? '-up' : '-down' ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a
                                    href="?<?= http_build_query(array_merge($_GET, ['sort_by' => 'name', 'sort_order' => ($sort_by === 'name' && $sort_order === 'ASC' ? 'DESC' : 'ASC')])) ?>">
                                    Tên Nhà cung cấp
                                    <?php if ($sort_by === 'name'): ?><i
                                            class="fas fa-sort<?= $sort_order === 'ASC' ? '-up' : '-down' ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suppliers as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['id']) ?></td>
                                <td><?= htmlspecialchars($s['name']) ?></td>
                                <td><?= htmlspecialchars($s['phone'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($s['address'] ?? 'N/A') ?></td>
                                <td class="action-buttons">
                                    <button type="button" class="action-button edit-supplier-btn"
                                        data-id="<?= htmlspecialchars($s['id']) ?>"
                                        data-name="<?= htmlspecialchars($s['name']) ?>"
                                        data-phone="<?= htmlspecialchars($s['phone'] ?? '') ?>"
                                        data-address="<?= htmlspecialchars($s['address'] ?? '') ?>">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <a href="?delete=<?= htmlspecialchars($s['id']) ?>" class="action-button delete"
                                        onclick="return confirm('Xác nhận xóa nhà cung cấp &quot;<?= htmlspecialchars($s['name']) ?>&quot;? Thao tác này không thể hoàn tác.')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="table-card">
            <h2>Thống kê sản phẩm theo nhà cung cấp</h2>
            <canvas id="supplierProductChart" style="width: 1000px; height: 600px; ;margin: 0 auto;"></canvas>
        </section>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <div id="supplier-modal" class="modal">
        <div class="modal-content">
            <button class="modal-close-btn">&times;</button>
            <h2 id="modal-title"></h2>
            <form action="manage_suppliers.php" method="POST" id="supplier-form">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="id" id="id" value="">

                <div class="form-group">
                    <label for="name">Tên Nhà cung cấp:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại:</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ:</label>
                    <textarea id="address" name="address" rows="3"></textarea>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Lưu</button>
                    <button type="button" class="btn-secondary modal-close-btn"><i class="fas fa-times"></i>
                        Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.querySelector('.sidebar');
            document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
                button.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                });
            });

            const currentPath = window.location.pathname.split('/').pop();
            document.querySelectorAll('.sidebar nav a').forEach(link => {
                const linkHref = link.getAttribute('href').split('/').pop();
                if (linkHref === currentPath) {
                    link.classList.add('active');
                }
            });

            const supplierModal = document.getElementById('supplier-modal');
            const addSupplierBtn = document.getElementById('add-supplier-btn');
            const modalTitle = document.getElementById('modal-title');
            const supplierForm = document.getElementById('supplier-form');
            const actionInput = document.getElementById('action');
            const idInput = document.getElementById('id');
            const nameInput = document.getElementById('name');
            const phoneInput = document.getElementById('phone');
            const addressInput = document.getElementById('address');
            const closeButtons = document.querySelectorAll('.modal-close-btn');

            function openModal(isEdit = false, data = {}) {
                if (isEdit) {
                    modalTitle.textContent = 'Sửa thông tin Nhà cung cấp';
                    actionInput.value = 'edit';
                    idInput.value = data.id;
                    nameInput.value = data.name;
                    phoneInput.value = data.phone;
                    addressInput.value = data.address;
                } else {
                    modalTitle.textContent = 'Thêm Nhà cung cấp mới';
                    actionInput.value = 'add';
                    idInput.value = '';
                    supplierForm.reset();
                }
                supplierModal.style.display = 'flex';
            }

            function closeModal() {
                supplierModal.style.display = 'none';
            }

            addSupplierBtn.addEventListener('click', () => openModal(false));

            document.querySelectorAll('.edit-supplier-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const data = {
                        id: button.dataset.id,
                        name: button.dataset.name,
                        phone: button.dataset.phone,
                        address: button.dataset.address
                    };
                    openModal(true, data);
                });
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', closeModal);
            });

            window.addEventListener('click', (event) => {
                if (event.target === supplierModal) {
                    closeModal();
                }
            });

            <?php if (!empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                document.getElementById('action').value = '<?= htmlspecialchars(filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '') ?>';
                document.getElementById('id').value = '<?= htmlspecialchars(filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?? '') ?>';
                document.getElementById('name').value = '<?= htmlspecialchars(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '') ?>';
                document.getElementById('phone').value = '<?= htmlspecialchars(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '') ?>';
                document.getElementById('address').value = '<?= htmlspecialchars(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '') ?>';

                modalTitle.textContent = document.getElementById('action').value === 'edit' ? 'Sửa thông tin Nhà cung cấp' : 'Thêm Nhà cung cấp mới';
                supplierModal.style.display = 'flex';
            <?php endif; ?>

            // Biểu đồ sản phẩm theo nhà cung cấp
            const ctx = document.getElementById('supplierProductChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php echo "'" . implode("','", array_map('addslashes', $supplier_names)) . "'"; ?>],
                    datasets: [{
                        label: 'Số lượng sản phẩm',
                        data: [<?php echo implode(',', $supplier_product_counts); ?>],
                        backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'],
                        borderColor: ['#0056b3', '#1e7e34', '#d39e00', '#c82333', '#117a8b'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng sản phẩm',
                                color: '#ffffff',
                                font: {size: 20}
                            },
                            ticks: {
                                color: '#ffffff'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Nhà cung cấp',
                                color: '#ffffff',
                                font: {size: 20}
                            },
                            ticks: {
                                color: '#ffffff'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Số lượng sản phẩm theo nhà cung cấp',
                            color: '#ffffff',
                            font: { size: 20 }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>