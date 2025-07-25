<?php
// manage_categories.php
require_once '../config/db.php';
require 'admin_header.php';

// Ensure session is started for $_SESSION usage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Class CategoryController
 * Handles all category-related operations.
 */
class CategoryController {
    private $conn;
    private $user_id; // Assuming user_id is always available from session

    public function __construct($conn, $user_id) {
        $this->conn = $conn;
        $this->user_id = $user_id;
    }

    /**
     * Fetches categories based on criteria.
     * @param int|null $exclude_id Category ID to exclude.
     * @param bool $only_parents Only fetch parent categories (parent_id IS NULL).
     * @return array
     */
    public function getCategories($exclude_id = null, $only_parents = false) {
        $categories = [];
        $sql = "SELECT id, name, parent_id, slug FROM categories WHERE is_active = 1";
        $params = [];
        $types = '';

        if ($only_parents) {
            $sql .= " AND parent_id IS NULL";
        }

        if ($exclude_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
            $types .= 'i';
        }

        $sql .= " ORDER BY id ASC";

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Failed to prepare getCategories statement: " . $this->conn->error);
                return [];
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            if (isset($stmt)) $stmt->close();
        } else {
            error_log("Failed to getCategories: " . $this->conn->error);
        }
        return $categories;
    }

    /**
     * Recursively formats categories for a dropdown.
     * @param array $categories
     * @param int|null $parentId
     * @param string $prefix
     * @return array
     */
    public function formatCategoriesForDropdown($categories, $parentId = null, $prefix = '') {
        $output = [];
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parentId) {
                $output[] = [
                    'id' => $category['id'],
                    'name' => $prefix . htmlspecialchars($category['name'])
                ];
                $output = array_merge($output, $this->formatCategoriesForDropdown($categories, $category['id'], $prefix . '-- '));
            }
        }
        return $output;
    }

    /**
     * Adds a new category.
     * @param string $category_name
     * @param int|null $parent_id
     * @return array Success status and message/errors.
     */
    public function addCategory($category_name, $parent_id) {
        $errors = [];
        $slug = $this->generateSlug($category_name);

        if (empty($category_name)) {
            $errors[] = "Tên danh mục không được để trống.";
        }

        if ($this->isSlugExists($slug)) {
            $errors[] = "Danh mục với slug này đã tồn tại.";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->conn->prepare("INSERT INTO categories (name, parent_id, slug) VALUES (?, ?, ?)");
        if (!$stmt) {
            return ['success' => false, 'errors' => ["Lỗi chuẩn bị câu lệnh thêm danh mục: " . $this->conn->error]];
        }

        $stmt->bind_param("sis", $category_name, $parent_id, $slug);
        if ($stmt->execute()) {
            $this->logActivity('add_category', "Added category: $category_name");
            return ['success' => true, 'message' => "Danh mục '{$category_name}' đã được thêm thành công!"];
        } else {
            return ['success' => false, 'errors' => ["Lỗi khi thêm danh mục: " . $this->conn->error]];
        }
    }

    /**
     * Updates an existing category.
     * @param int $category_id
     * @param string $category_name
     * @param int|null $parent_id
     * @return array Success status and message/errors.
     */
    public function updateCategory($category_id, $category_name, $parent_id) {
        $errors = [];
        $slug = $this->generateSlug($category_name);

        if (empty($category_name)) {
            $errors[] = "Tên danh mục không được để trống.";
        }

        if ($this->isSlugExists($slug, $category_id)) {
            $errors[] = "Danh mục với slug này đã tồn tại.";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->conn->prepare("UPDATE categories SET name = ?, parent_id = ?, slug = ? WHERE id = ?");
        if (!$stmt) {
            return ['success' => false, 'errors' => ["Lỗi chuẩn bị câu lệnh cập nhật danh mục: " . $this->conn->error]];
        }

        $stmt->bind_param("sisi", $category_name, $parent_id, $slug, $category_id);
        if ($stmt->execute()) {
            $this->logActivity('edit_category', "Edited category: $category_name (ID: $category_id)");
            return ['success' => true, 'message' => "Danh mục '{$category_name}' đã được cập nhật thành công!"];
        } else {
            return ['success' => false, 'errors' => ["Lỗi khi cập nhật danh mục: " . $this->conn->error]];
        }
    }

    /**
     * Deletes a category.
     * @param int $category_id
     * @return array Success status and message/errors.
     */
    public function deleteCategory($category_id) {
        $errors = [];

        if ($this->hasChildren($category_id)) {
            $errors[] = "Không thể xóa danh mục này vì nó có danh mục con. Vui lòng xóa danh mục con trước.";
        }

        if ($this->hasRelatedProducts($category_id)) {
            $errors[] = "Không thể xóa danh mục này vì có sản phẩm liên quan. Vui lòng xóa hoặc chuyển sản phẩm trước.";
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ?");
        if (!$stmt) {
            return ['success' => false, 'errors' => ["Lỗi chuẩn bị câu lệnh xóa danh mục: " . $this->conn->error]];
        }

        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            $this->logActivity('delete_category', "Deleted category ID: $category_id");
            return ['success' => true, 'message' => "Danh mục đã được xóa thành công!"];
        } else {
            return ['success' => false, 'errors' => ["Lỗi khi xóa danh mục: " . $this->conn->error]];
        }
    }

    /**
     * Fetches a single category by ID.
     * @param int $category_id
     * @return array|null
     */
    public function getCategoryById($category_id) {
        $stmt = $this->conn->prepare("SELECT id, name, parent_id FROM categories WHERE id = ? AND is_active = 1");
        if (!$stmt) {
            error_log("Failed to prepare getCategoryById statement: " . $this->conn->error);
            return null;
        }
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        $stmt->close();
        return $category;
    }

    /**
     * Fetches subcategories for a given parent ID.
     * @param int $parent_id
     * @return array
     */
    public function getSubcategories($parent_id) {
        $stmt = $this->conn->prepare("SELECT id, name FROM categories WHERE parent_id = ? AND is_active = 1 ORDER BY name ASC");
        if (!$stmt) {
            error_log("Prepare get_subcategories failed: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $subcategories = [];
        while ($row = $result->fetch_assoc()) {
            $subcategories[] = $row;
        }
        $stmt->close();
        return $subcategories;
    }

    /**
     * Fetches user notifications (logs).
     * @return array
     */
    public function fetchNotifications() {
        $notifications = [];
        if (!$this->user_id) {
            error_log("fetch_notifications failed: user_id not set in controller");
            return ['success' => false, 'message' => 'Phiên đăng nhập không hợp lệ.', 'notifications' => []];
        }

        // Check if logs table exists
        $table_check = $this->conn->query("SHOW TABLES LIKE 'logs'");
        if ($table_check && $table_check->num_rows > 0) {
            $stmt = $this->conn->prepare("SELECT details, created_at FROM logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
            if ($stmt === false) {
                error_log("Prepare fetch_notifications failed: " . $this->conn->error);
                return ['success' => false, 'message' => 'Lỗi khi lấy thông báo.', 'notifications' => []];
            }
            $stmt->bind_param("i", $this->user_id);
            if (!$stmt->execute()) {
                error_log("Execute fetch_notifications failed: " . $stmt->error);
                return ['success' => false, 'message' => 'Lỗi khi lấy thông báo.', 'notifications' => []];
            }
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $notifications[] = [
                    'message' => $row['details'],
                    'time' => $row['created_at']
                ];
            }
            $stmt->close();
        } else {
            error_log("Table 'logs' does not exist");
        }
        return [
            'success' => true,
            'notifications' => $notifications,
            'message' => empty($notifications) ? 'Không có thông báo mới.' : 'Lấy thông báo thành công.'
        ];
    }

    /**
     * Generates a URL-friendly slug from a string.
     * @param string $text
     * @return string
     */
    private function generateSlug($text) {
        $text = strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/\s+/', '-', $text);
        return $text;
    }

    /**
     * Checks if a slug already exists in the database.
     * @param string $slug
     * @param int|null $exclude_id ID to exclude from the check (for edits).
     * @return bool
     */
    private function isSlugExists($slug, $exclude_id = null) {
        $sql = "SELECT COUNT(*) AS count FROM categories WHERE slug = ?";
        $params = [$slug];
        $types = 's';

        if ($exclude_id !== null) {
            $sql .= " AND id != ?";
            $params[] = $exclude_id;
            $types .= 'i';
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Failed to prepare isSlugExists statement: " . $this->conn->error);
            return true; // Assume exists on error to prevent duplicates
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'] ?? 0;
        $stmt->close();
        return $count > 0;
    }

    /**
     * Checks if a category has child categories.
     * @param int $category_id
     * @return bool
     */
    private function hasChildren($category_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM categories WHERE parent_id = ?");
        if (!$stmt) {
            error_log("Failed to prepare hasChildren statement: " . $this->conn->error);
            return true; // Assume children exist on error
        }
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'] ?? 0;
        $stmt->close();
        return $count > 0;
    }

    /**
     * Checks if a category has related products.
     * @param int $category_id
     * @return bool
     */
    private function hasRelatedProducts($category_id) {
        // Assuming a 'products' table with 'category_id' column
        $table_check = $this->conn->query("SHOW TABLES LIKE 'products'");
        if (!$table_check || $table_check->num_rows === 0) {
            // If products table doesn't exist, no related products can be found
            return false;
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM products WHERE category_id = ?");
        if (!$stmt) {
            error_log("Failed to prepare hasRelatedProducts statement: " . $this->conn->error);
            return true; // Assume products exist on error
        }
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'] ?? 0;
        $stmt->close();
        return $count > 0;
    }

    /**
     * Logs activity to the 'logs' table.
     * @param string $action
     * @param string $details
     */
    private function logActivity($action, $details) {
        $table_check = $this->conn->query("SHOW TABLES LIKE 'logs'");
        if ($table_check && $table_check->num_rows > 0) {
            $log_stmt = $this->conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
            if ($log_stmt) {
                $log_stmt->bind_param("iss", $this->user_id, $action, $details);
                $log_stmt->execute();
                $log_stmt->close();
            } else {
                error_log("Failed to prepare log activity statement: " . $this->conn->error);
            }
        } else {
            error_log("Table 'logs' does not exist, cannot log activity.");
        }
    }
}

// Initialize controller
$user_id = $_SESSION['user_id'] ?? 0; // Get user ID from session
$categoryController = new CategoryController($conn, $user_id);

$errors = [];
$success_message = '';
$edit_category = null;

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'fetch_notifications') {
        ob_clean(); // Clean output buffer before sending JSON
        $response = $categoryController->fetchNotifications();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    if (isset($_POST['add_category']) || isset($_POST['edit_category'])) {
        $category_name = trim(filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT);
        $parent_id = ($parent_id === false || $parent_id === 0) ? null : $parent_id;

        if (isset($_POST['add_category'])) {
            $result = $categoryController->addCategory($category_name, $parent_id);
        } elseif (isset($_POST['edit_category'])) {
            $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
            if (!$category_id) {
                $errors[] = "ID danh mục không hợp lệ để chỉnh sửa.";
            } else {
                $result = $categoryController->updateCategory($category_id, $category_name, $parent_id);
            }
        }

        if (isset($result)) {
            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
                header("Location: manage_categories.php");
                exit();
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        }
    } elseif (isset($_POST['delete_category'])) {
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        if (!$category_id) {
            $errors[] = "ID danh mục không hợp lệ để xóa.";
        } else {
            $result = $categoryController->deleteCategory($category_id);
            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
                header("Location: manage_categories.php");
                exit();
            } else {
                $errors = array_merge($errors, $result['errors']);
            }
        }
    } elseif ($action === 'get_subcategories') {
        ob_clean();
        $parent_id = filter_input(INPUT_POST, 'parent_id', FILTER_VALIDATE_INT);
        if ($parent_id) {
            $subcategories = $categoryController->getSubcategories($parent_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'subcategories' => $subcategories]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ID danh mục cha không hợp lệ.']);
        }
        exit;
    }
}

// Handle GET requests for editing
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($category_id) {
        $edit_category = $categoryController->getCategoryById($category_id);
        if (!$edit_category) {
            $errors[] = "Không tìm thấy danh mục để chỉnh sửa.";
        }
    } else {
        $errors[] = "ID danh mục không hợp lệ để chỉnh sửa.";
    }
}

// Handle success message from session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Data for rendering the page
$parent_categories = $categoryController->getCategories(null, true);
$all_categories = $categoryController->getCategories();
$categories_for_dropdown = $categoryController->getCategories($edit_category['id'] ?? null);
$formatted_categories_for_dropdown = $categoryController->formatCategoriesForDropdown($categories_for_dropdown);
?>

<main class="admin-container">
    <section class="card">
        <h2>Quản lý Danh mục</h2>

        <?php if (!empty($errors)): ?>
            <div class="messages error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="messages success">
                <p><?= htmlspecialchars($success_message) ?></p>
            </div>
        <?php endif; ?>

        <form action="manage_categories.php" method="POST" class="form-grid">
            <?php if ($edit_category): ?>
                <input type="hidden" name="category_id" value="<?= htmlspecialchars($edit_category['id']) ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="category_name">Tên Danh mục:</label>
                <input type="text" id="category_name" name="category_name" value="<?= htmlspecialchars($edit_category['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="parent_id">Danh mục cha:</label>
                <select id="parent_id" name="parent_id">
                    <option value="NULL">-- Không có --</option>
                    <?php foreach ($formatted_categories_for_dropdown as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= ($edit_category && $edit_category['parent_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= $cat['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="button-group">
                <?php if ($edit_category): ?>
                    <button type="submit" name="edit_category" class="action-button edit-button"><i class="fas fa-edit"></i> Cập nhật Danh mục</button>
                    <a href="manage_categories.php" class="action-button cancel-button"><i class="fas fa-times"></i> Hủy</a>
                <?php else: ?>
                    <button type="submit" name="add_category" class="action-button add-button"><i class="fas fa-plus"></i> Thêm Danh mục</button>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="card mt-4">
        <h2>Danh sách Danh mục Cha</h2>
        <?php if (empty($parent_categories)): ?>
            <p>Không có danh mục cha nào.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Danh mục</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parent_categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['id']) ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td class="actions">
                                    <a href="manage_categories.php?action=edit&id=<?= htmlspecialchars($category['id']) ?>" class="action-button edit-button small">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="manage_categories.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['id']) ?>">
                                        <button type="submit" name="delete_category" class="action-button delete-button small">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                    <button class="action-button view-button small" data-category-id="<?= htmlspecialchars($category['id']) ?>" data-category-name="<?= htmlspecialchars($category['name']) ?>">
                                        <i class="fas fa-eye"></i> Xem chi tiết
                                    </button>
                                </td>
                            </tr>
                            <tr id="subcategoryContainer_<?= htmlspecialchars($category['id']) ?>" class="subcategory-container" style="display: none;">
                                <td colspan="3">
                                    <div class="subcategory-table">
                                        <h3>Danh mục con của <?= htmlspecialchars($category['name']) ?></h3>
                                        <div class="table-responsive">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Tên Danh mục</th>
                                                        <th>Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="subcategoryList_<?= htmlspecialchars($category['id']) ?>"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <section class="card mt-4">
        <h2>Thông báo</h2>
        <div id="notification-area" class="notification-area">
            <ul id="notification-list" class="notification-list">
                </ul>
        </div>
    </section>
</main>

<footer class="admin-footer">
    <p>© 2025 Anh Em Rọt Store. All rights reserved.</p>
</footer>
<link rel="stylesheet" href="../css/admin.css">
<style>
    .subcategory-container {
        background-color: var(--background-secondary);
        padding: 10px;
    }
    .subcategory-table {
        margin: 10px 0;
    }
    .subcategory-table h3 {
        margin: 0 0 10px 0;
        font-size: 1.2rem;
        color: var(--text-primary);
    }
    .view-button {
        background-color: var(--info);
    }
    .view-button:hover {
        background-color: var(--info-dark);
    }
    .notification-area {
        background-color: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius);
        padding: 15px;
        margin-top: 20px;
        box-shadow: var(--shadow-light);
    }

    .notification-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .notification-list li {
        padding: 8px 0;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .notification-list li:last-child {
        border-bottom: none;
    }

    .notification-list li small {
        color: var(--text-secondary);
        font-size: 0.85em;
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    function toggleSubcategories(parentId, parentName) {
        const container = document.getElementById(`subcategoryContainer_${parentId}`);
        const subcategoryList = document.getElementById(`subcategoryList_${parentId}`);
        if (container.style.display === 'table-row') {
            container.style.display = 'none';
            return;
        }
        $.ajax({
            url: 'manage_categories.php',
            type: 'POST',
            data: { action: 'get_subcategories', parent_id: parentId },
            dataType: 'json',
            success: function(response) {
                subcategoryList.innerHTML = '';
                if (response.success && response.subcategories.length > 0) {
                    response.subcategories.forEach(sub => {
                        subcategoryList.innerHTML += `
                            <tr>
                                <td>${sub.id}</td>
                                <td>${sub.name}</td>
                                <td class="actions">
                                    <a href="manage_categories.php?action=edit&id=${sub.id}" class="action-button edit-button small">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="manage_categories.php" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                        <input type="hidden" name="category_id" value="${sub.id}">
                                        <button type="submit" name="delete_category" class="action-button delete-button small">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    subcategoryList.innerHTML = '<tr><td colspan="3">Không có danh mục con.</td></tr>';
                }
                container.style.display = 'table-row';
            },
            error: function(xhr, status, error) {
                console.log('Raw response:', xhr.responseText);
                showNotification('notification-list', 'Lỗi khi lấy danh mục con.', 'error');
            }
        });
    }

    function showNotification(elementId, message, type) {
        let notificationElement = $(`#${elementId}`);
        if (notificationElement.length === 0) {
            // If the element doesn't exist, create it and append to body or a suitable parent
            // For general notifications, you might want a fixed global notification area.
            // For now, let's just make sure it's created.
            // Adjust this part based on where your notification elements are supposed to be.
            $('body').append(`<div id="${elementId}" class="messages" style="display:none; position:fixed; top:20px; right:20px; z-index:1000;"></div>`);
            notificationElement = $(`#${elementId}`);
        }
        notificationElement.html(`<p>${message}</p>`).removeClass('success error').addClass(type).slideDown();
        setTimeout(() => notificationElement.slideUp(), 5000);
    }


    $(document).ready(function() {
        // Sidebar toggle logic (moved from admin_header.php if it's there)
        const sidebar = $('.sidebar');
        const masterToggleButton = $('.sidebar-toggle-master');
        const internalSidebarToggleButton = $('.sidebar-toggle-internal');

        if (masterToggleButton.length) {
            masterToggleButton.click(function() {
                if (window.innerWidth > 1024) {
                    sidebar.toggleClass('collapsed');
                    $('.admin-container, .admin-topbar').toggleClass('sidebar-collapsed');
                } else {
                    sidebar.toggleClass('active');
                }
            });
        }

        if (internalSidebarToggleButton.length) {
            internalSidebarToggleButton.click(function() {
                if (window.innerWidth <= 1024) {
                    sidebar.removeClass('active');
                } else {
                    sidebar.toggleClass('collapsed');
                    $('.admin-container, .admin-topbar').toggleClass('sidebar-collapsed');
                }
            });
        }

        function fetchNotifications() {
            $.ajax({
                url: 'manage_categories.php',
                type: 'POST',
                data: { action: 'fetch_notifications' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const notificationList = $('#notification-list');
                        notificationList.empty();
                        if (response.notifications.length > 0) {
                            response.notifications.forEach(n => {
                                notificationList.append(`<li>${n.message} <br><small>${n.time}</small></li>`);
                            });
                        } else {
                            notificationList.append('<li>Không có thông báo mới.</li>');
                        }
                        // This assumes you have a notification-badge element, if not, it won't cause issues
                        $('.notification-badge').text(response.notifications.length).toggle(response.notifications.length > 0);
                    } else {
                        showNotification('notification-list', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Raw response:', xhr.responseText);
                    showNotification('notification-list', 'Lỗi khi tải thông báo: ' + xhr.responseText, 'error');
                }
            });
        }

        // Initial fetch and set interval
        fetchNotifications();
        setInterval(fetchNotifications, 30000);

        $('.data-table').on('click', '.view-button', function() {
            const parentId = $(this).data('category-id');
            const parentName = $(this).data('category-name');
            toggleSubcategories(parentId, parentName);
        });
    });

    <?php ob_end_flush(); // Ensures output buffering is properly handled and flushed ?>
</script>
</body>
</html>