<?php
session_start();
require_once '../config/db.php';
require 'admin_header.php';

$errors = [];
$success = false;

// Variables for search
$search_query = trim($_GET['search'] ?? '');

// Xử lý sửa hoặc khóa tài khoản
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $action = $_POST['action'] ?? '';

    if ($action === 'edit') {
        $full_name_from_post = trim($_POST['full_name'] ?? '');
        $first_name = '';
        $last_name = '';

        $parts = explode(' ', $full_name_from_post);
        if (count($parts) > 1) {
            $last_name = array_pop($parts);
            $first_name = implode(' ', $parts);
        } else {
            $first_name = $full_name_from_post;
        }

        if (empty($full_name_from_post)) {
            $errors[] = "Tên đầy đủ không được để trống.";
        }

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE user_detail SET first_name = ?, last_name = ?, phone_number = ?, address = ? WHERE user_id = ?");
            $stmt->bind_param("ssssi", $first_name, $last_name, $phone, $address, $id);
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

$users = [];
$sql = "SELECT u.id, u.email, ud.first_name, ud.last_name, ud.phone_number, ud.address, u.is_verified, u.role FROM users u
INNER JOIN user_detail ud ON u.id = ud.user_id
WHERE u.role = 'customer'";

$params = [];
$types = "";

if (!empty($search_query)) {
    $sql .= " AND (u.email LIKE ? OR ud.first_name LIKE ? OR ud.last_name LIKE ?)";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $params[] = "%" . $search_query . "%";
    $types .= "sss";
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$stmt = $conn->prepare("SELECT is_verified, COUNT(*) as count FROM users WHERE role = 'customer' GROUP BY is_verified");
$stmt->execute();
$result = $stmt->get_result();
$user_status_counts = ['Hoạt động' => 0, 'Khóa' => 0];
while ($row = $result->fetch_assoc()) {
    if ($row['is_verified'] == 1) {
        $user_status_counts['Hoạt động'] = $row['count'];
    } else {
        $user_status_counts['Khóa'] = $row['count'];
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .admin-container {
            padding: 30px;
            max-width: 1200px;
            margin: 20px auto;
        }

        .table-card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .table-card h2 {
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
            border-radius: 5px;
            background-color: var(--input-bg);
            color: var(--text-color);
            font-size: 1rem;
            width: 250px;
        }

        .action-bar .search-box button {
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .action-bar .search-box button:hover {
            background-color: var(--primary-color-dark);
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 1rem;
        }

        .user-table th, .user-table td {
            border: 1px solid var(--border-color);
            padding: 12px;
            text-align: left;
        }

        .user-table th {
            background: var(--table-header-bg);
            color: var(--table-header-text);
            font-weight: bold;
        }

        .user-table tbody tr:nth-child(even) {
            background: var(--table-row-even-bg);
        }

        .user-table tbody tr:hover {
            background: var(--table-row-hover-bg);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .action-button {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: 0.9rem;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .action-button.edit {
            background-color: #007bff;
        }
        .action-button.edit:hover {
            background-color: #0056b3;
        }

        .action-button.lock {
            background-color: #dc3545;
        }
        .action-button.lock:hover {
            background-color: #c82333;
        }

        .action-button.unlock {
            background-color: #28a745;
        }
        .action-button.unlock:hover {
            background-color: #218838;
        }

        .messages {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .messages.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .messages.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: #fff;
        }

        .status-badge.active {
            background-color: #28a745;
        }

        .status-badge.locked {
            background-color: #dc3545;
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
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--card-bg);
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            box-shadow: var(--shadow);
            position: relative;
        }

        .modal-content h2 {
            margin-top: 0;
            margin-bottom: 20px;
            color: var(--primary-color);
            text-align: center;
        }

        .modal-close-btn {
            color: #aaa;
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
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal .form-group label {
            color: var(--text-color-light);
            display: block;
            margin-bottom: 5px;
        }
        .modal .form-group input,
        .modal .form-group textarea {
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background-color: var(--input-bg);
            color: var(--text-color);
            font-size: 1rem;
            width: calc(100% - 22px);
            box-sizing: border-box;
            margin-bottom: 15px;
        }
        .modal .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
        .modal .button-group button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1rem;
        }
        .modal .button-group button[type="submit"] {
            background-color: var(--primary-color);
            color: #fff;
        }
        .modal .button-group button[type="submit"]:hover {
            background-color: var(--primary-color-dark);
        }
        .modal .button-group .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }
        .modal .button-group .btn-secondary:hover {
            background-color: #5a6268;
        }

        .notification-area {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .notification-area ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .notification-area li {
            padding: 5px 0;
            color: #333;
        }
        .notification-area .count {
            font-weight: bold;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <main class="admin-container">
        <section class="table-card">
            <h2>Thông báo</h2>
            <div class="notification-area" id="notification-area">
                <p class="count">Thông báo mới: <span id="notification-count">0</span></p>
                <ul id="notification-list"></ul>
            </div>
        </section>

        <section class="table-card">
            <h2>Danh sách khách hàng</h2>

            <?php if (!empty($errors)): ?>
                <div class="messages error">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php elseif ($success): ?>
                <div class="messages success">
                    <p>Thao tác thành công!</p>
                </div>
            <?php endif; ?>

            <div class="action-bar">
                <form action="manage_users.php" method="GET" class="search-box">
                    <input type="text" name="search" placeholder="Tìm kiếm theo email, họ tên..." value="<?= htmlspecialchars($search_query) ?>">
                    <button type="submit"><i class="fas fa-search"></i> Tìm kiếm</button>
                </form>
            </div>

            <?php if (empty($users)): ?>
                <p>Không tìm thấy người dùng nào.</p>
            <?php else: ?>
                <table class="user-table">
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
                                <td><?= htmlspecialchars(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?></td>
                                <td><?= htmlspecialchars($u['phone_number'] ?? '') ?></td> 
                                <td><?= htmlspecialchars($u['address'] ?? '') ?></td>
                                <td>
                                    <span class="status-badge <?= $u['is_verified'] ? 'active' : 'locked' ?>">
                                        <?= $u['is_verified'] ? 'Hoạt động' : 'Khóa' ?>
                                    </span>
                                </td>
                                <td class="action-buttons">
                                    <button type="button" class="action-button edit-user edit" 
                                        data-id="<?= htmlspecialchars($u['id']) ?>" 
                                        data-full_name="<?= htmlspecialchars(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?>" 
                                        data-phone="<?= htmlspecialchars($u['phone_number'] ?? '') ?>" 
                                        data-address="<?= htmlspecialchars($u['address'] ?? '') ?>">
                                         <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="action" value="lock">
                                        <input type="hidden" name="status" value="<?= $u['is_verified'] ? 'lock' : 'unlock' ?>">
                                        <button type="submit" class="action-button <?= $u['is_verified'] ? 'lock' : 'unlock' ?>" 
                                                onclick="return confirm('Xác nhận <?= $u['is_verified'] ? 'khóa' : 'mở khóa' ?> tài khoản này?')">
                                            <i class="fas fa-<?= $u['is_verified'] ? 'lock' : 'unlock' ?>"></i> <?= $u['is_verified'] ? 'Khóa' : 'Mở khóa' ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="table-card">
            <h2>Thống kê trạng thái người dùng</h2>
            <canvas id="userStatusChart" style="max-width: 400px; margin: 0 auto;"></canvas>
        </section>

        <div id="edit-user-modal" class="modal">
            <div class="modal-content">
                <button class="modal-close-btn">×</button>
                <h2>Sửa thông tin người dùng</h2>
                <form method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit-user-id">
                    <div class="form-group">
                        <label for="edit-full-name">Họ tên:</label>
                        <input type="text" name="full_name" id="edit-full-name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-phone">Số điện thoại:</label>
                        <input type="text" name="phone" id="edit-phone">
                    </div>
                    <div class="form-group">
                        <label for="edit-address">Địa chỉ:</label>
                        <textarea name="address" id="edit-address" rows="3"></textarea>
                    </div>
                    <div class="button-group">
                        <button type="submit"><i class="fas fa-save"></i> Lưu</button>
                        <button type="button" class="btn-secondary modal-close-btn"><i class="fas fa-times"></i> Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer class="admin-footer">
        <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sidebar toggle logic
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                document.querySelectorAll('.sidebar-toggle, .sidebar-toggle-topbar').forEach(button => {
                    button.addEventListener('click', () => {
                        sidebar.classList.toggle('collapsed');
                    });
                });
            }

            // Highlight active menu item
            const currentPath = window.location.pathname.split('/').pop();
            document.querySelectorAll('.sidebar nav a').forEach(link => {
                const linkHref = link.getAttribute('href').split('/').pop();
                if (linkHref === currentPath) {
                    link.classList.add('active');
                }
            });

            // Modal functionality
            const editUserModal = document.getElementById('edit-user-modal');
            const editUserButtons = document.querySelectorAll('.edit-user');
            const editUserIdInput = document.getElementById('edit-user-id');
            const editFullNameInput = document.getElementById('edit-full-name');
            const editPhoneInput = document.getElementById('edit-phone');
            const editAddressInput = document.getElementById('edit-address');
            const closeButtons = document.querySelectorAll('.modal-close-btn');

            function openModal(data) {
                editUserIdInput.value = data.id;
                editFullNameInput.value = data.full_name;
                editPhoneInput.value = data.phone;
                editAddressInput.value = data.address;
                editUserModal.style.display = 'flex';
            }

            function closeModal() {
                editUserModal.style.display = 'none';
            }

            editUserButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const data = {
                        id: button.dataset.id,
                        full_name: button.dataset.full_name,
                        phone: button.dataset.phone,
                        address: button.dataset.address
                    };
                    openModal(data);
                });
            });

            closeButtons.forEach(button => {
                button.addEventListener('click', closeModal);
            });

            window.addEventListener('click', (event) => {
                if (event.target === editUserModal) {
                    closeModal();
                }
            });

            <?php if (!empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] === 'edit')): ?>
                // Giữ lại giá trị full_name từ POST khi có lỗi để hiển thị lại trên modal
                openModal({
                    id: '<?= htmlspecialchars($_POST['id'] ?? '') ?>',
                    full_name: '<?= htmlspecialchars($_POST['full_name'] ?? '') ?>',
                    phone: '<?= htmlspecialchars($_POST['phone'] ?? '') ?>',
                    address: '<?= htmlspecialchars($_POST['address'] ?? '') ?>'
                });
            <?php endif; ?>

            // Biểu đồ trạng thái người dùng
            const ctx = document.getElementById('userStatusChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Hoạt động', 'Khóa'],
                    datasets: [{
                        label: 'Trạng thái người dùng',
                        data: [<?php echo $user_status_counts['Hoạt động']; ?>, <?php echo $user_status_counts['Khóa']; ?>],
                        backgroundColor: ['#28a745', '#dc3545'],
                        borderColor: ['#1e7e34', '#c82333'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#333',
                                font: { size: 14 }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Phân bố trạng thái người dùng',
                            color: '#333',
                            font: { size: 16 }
                        }
                    }
                }
            });

            // Lấy và hiển thị thông báo
            async function fetchNotifications() {
                try {
                    const response = await fetch('get_notifications.php');
                    if (!response.ok) {
                        throw new Error('Lỗi server: ' + response.status);
                    }
                    const data = await response.json();
                    if (data.error) {
                        console.error('Lỗi từ server:', data.error);
                        return;
                    }
                    const notificationCount = document.getElementById('notification-count');
                    const notificationList = document.getElementById('notification-list');
                    notificationCount.textContent = data.count || 0;
                    notificationList.innerHTML = '';
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notification => {
                            const li = document.createElement('li');
                            li.textContent = notification;
                            notificationList.appendChild(li);
                        });
                    } else {
                        notificationList.innerHTML = '<li>Không có thông báo mới.</li>';
                    }
                } catch (error) {
                    console.error('Lỗi lấy thông báo:', error);
                    document.getElementById('notification-list').innerHTML = '<li>Lỗi khi tải thông báo.</li>';
                }
            }

            // Gọi fetchNotifications ngay khi tải trang và mỗi 30 giây
            fetchNotifications();
            setInterval(fetchNotifications, 30000);
        });
    </script>
</body>
</html>