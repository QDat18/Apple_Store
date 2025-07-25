<?php
require_once 'admin_header.php';
require_once '../config/db.php';

if (!$conn) {
    error_log("Database connection not available in all_notification.php.");
    die("Unable to connect to the database. Please contact the administrator.");
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_conditions = [];
$params = [];
$types = '';

if ($filter !== 'all') {
    if ($filter === 'unread') {
        $where_conditions[] = "is_read = FALSE";
    } elseif ($filter === 'read') {
        $where_conditions[] = "is_read = TRUE";
    } elseif (in_array($filter, ['info', 'success', 'warning', 'error'])) {
        $where_conditions[] = "type = ?";
        $params[] = $filter;
        $types .= 's';
    }
}

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

try {
    // Get total count
    $count_query = "SELECT COUNT(*) FROM notifications $where_clause";
    $stmt = $conn->prepare($count_query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $total_records = $result->fetch_row()[0];
    $total_pages = ceil($total_records / $limit);
    $stmt->close();
    
    // Get notifications
    $query = "
        SELECT id, title, message, type, is_read, created_at 
        FROM notifications 
        $where_clause
        ORDER BY created_at DESC 
        LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error in all_notification.php: " . $e->getMessage());
    $error_message = "Lỗi database: " . $e->getMessage();
    $notifications = [];
    $total_records = 0;
    $total_pages = 0;
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Vừa xong';
    if ($time < 3600) return floor($time/60) . ' phút trước';
    if ($time < 86400) return floor($time/3600) . ' giờ trước';
    if ($time < 2592000) return floor($time/86400) . ' ngày trước';
    if ($time < 31536000) return floor($time/2592000) . ' tháng trước';
    return floor($time/31536000) . ' năm trước';
}

function getTypeIcon($type) {
    switch ($type) {
        case 'success': return 'fas fa-check-circle';
        case 'warning': return 'fas fa-exclamation-triangle';
        case 'error': return 'fas fa-times-circle';
        default: return 'fas fa-info-circle';
    }
}

function getTypeColor($type) {
    switch ($type) {
        case 'success': return 'var(--success-color)';
        case 'warning': return 'var(--warning-color)';
        case 'error': return 'var(--error-color)';
        default: return 'var(--primary-color)';
    }
}
?>

<div class="notifications-page">
    <div class="page-header">
        <h1><i class="fas fa-bell"></i> Tất cả thông báo</h1>
        <div class="header-actions">
            <button class="btn-primary" onclick="markAllAsRead()">
                <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
            </button>
        </div>
    </div>

    <?php if (isset($error_message)): ?>
        <div class="notification error">
            <i class="fas fa-times-circle"></i>
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" class="filters-form">
            <div class="filter-group">
                <label for="filter">Lọc theo:</label>
                <select name="filter" id="filter" onchange="this.form.submit()">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Tất cả</option>
                    <option value="unread" <?= $filter === 'unread' ? 'selected' : '' ?>>Chưa đọc</option>
                    <option value="read" <?= $filter === 'read' ? 'selected' : '' ?>>Đã đọc</option>
                    <option value="info" <?= $filter === 'info' ? 'selected' : '' ?>>Thông tin</option>
                    <option value="success" <?= $filter === 'success' ? 'selected' : '' ?>>Thành công</option>
                    <option value="warning" <?= $filter === 'warning' ? 'selected' : '' ?>>Cảnh báo</option>
                    <option value="error" <?= $filter === 'error' ? 'selected' : '' ?>>Lỗi</option>
                </select>
            </div>
            
            <div class="search-group">
                <input type="text" name="search" placeholder="Tìm kiếm thông báo..." 
                       value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Notifications List -->
    <div class="notifications-list">
        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>Không có thông báo nào</h3>
                <p>Hiện tại không có thông báo phù hợp với bộ lọc của bạn.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>" 
                     data-id="<?= $notification['id'] ?>">
                    <div class="notification-icon">
                        <i class="<?= getTypeIcon($notification['type']) ?>" 
                           style="color: <?= getTypeColor($notification['type']) ?>"></i>
                    </div>
                    
                    <div class="notification-content">
                        <div class="notification-header">
                            <h4><?= htmlspecialchars($notification['title']) ?></h4>
                            <div class="notification-meta">
                                <span class="notification-time">
                                    <?= timeAgo($notification['created_at']) ?>
                                </span>
                                <span class="notification-type type-<?= $notification['type'] ?>">
                                    <?= ucfirst($notification['type']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="notification-message">
                            <?= htmlspecialchars($notification['message']) ?>
                        </div>
                    </div>
                    
                    <div class="notification-actions">
                        <?php if (!$notification['is_read']): ?>
                            <button class="mark-read" 
                                    onclick="markAsRead(<?= $notification['id'] ?>)">
                                <i class="fas fa-check"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>" 
                   class="btn-secondary">
                    <i class="fas fa-chevron-left"></i> Trước
                </a>
            <?php endif; ?>
            
            <span class="page-info">
                Trang <?= $page ?> / <?= $total_pages ?> 
                (<?= $total_records ?> thông báo)
            </span>
            
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>&filter=<?= $filter ?>&search=<?= urlencode($search) ?>" 
                   class="btn-secondary">
                    Sau <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
async function markAsRead(notificationId) {
    try {
        const response = await fetch('mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                notification_id: notificationId
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('unread');
                notificationItem.classList.add('read');
                const markReadBtn = notificationItem.querySelector('.mark-read');
                if (markReadBtn) {
                    markReadBtn.remove();
                }
            }
        } else {
            alert('Lỗi: ' + result.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi đánh dấu thông báo');
    }
}

async function markAllAsRead() {
    if (!confirm('Bạn có chắc chắn muốn đánh dấu tất cả thông báo là đã đọc?')) {
        return;
    }
    
    try {
        const response = await fetch('mark_all_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Lỗi: ' + result.error);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi đánh dấu thông báo');
    }
}
</script>