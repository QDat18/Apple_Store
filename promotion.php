<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'config/db.php';

// Kiểm tra quyền admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Hàm tạo slug đơn giản
function generateSlug($string) {
    $string = strtolower($string);
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = trim($string);
    $string = preg_replace('/\s+/', '-', $string);
    return $string;
}

// Xử lý thêm khuyến mãi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $expiry_date = $_POST['expiry_date'];
    $image_url = $_POST['image_url'];
    $slug = generateSlug($title); // Tạo slug từ tiêu đề

    // Kiểm tra slug đã tồn tại chưa
    $stmt_check_slug = $conn->prepare("SELECT COUNT(*) FROM promotions WHERE slug = ?");
    $stmt_check_slug->bind_param("s", $slug);
    $stmt_check_slug->execute();
    $slug_exists = $stmt_check_slug->get_result()->fetch_row()[0];
    $stmt_check_slug->close();

    if ($slug_exists > 0) {
        // Thêm một hậu tố để đảm bảo slug là duy nhất nếu trùng
        $slug = $slug . '-' . uniqid();
    }

    // Đã sửa câu lệnh INSERT để thêm slug
    $stmt = $conn->prepare("INSERT INTO promotions (title, slug, description, type, expiry_date, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    // Đã sửa bind_param để bao gồm slug
    $stmt->bind_param("ssssss", $title, $slug, $description, $type, $expiry_date, $image_url);
    $stmt->execute();
    $stmt->close();
    header("Location: promotion.php");
    exit;
}

// Tăng số lượng item/trang lên 20
$items_per_page = 20;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

$total_items = $conn->query("SELECT COUNT(*) FROM promotions")->fetch_row()[0];
$total_pages = ceil($total_items / $items_per_page);

// Nếu không có filter thì luôn hiển thị tất cả khuyến mãi
$filter_type = isset($_GET['type_filter']) ? $_GET['type_filter'] : '';
$filter_expiry = isset($_GET['expiry_filter']) ? $_GET['expiry_filter'] : '';

$sql_filter_where = [];
$sql_filter_params = [];
$sql_filter_types = '';

if (!empty($filter_type)) {
    $sql_filter_where[] = "type = ?";
    $sql_filter_params[] = $filter_type;
    $sql_filter_types .= 's';
}
if (!empty($filter_expiry)) {
    $sql_filter_where[] = "expiry_date = ?";
    $sql_filter_params[] = $filter_expiry;
    $sql_filter_types .= 's';
}

$sql_where_clause = '';
if (!empty($sql_filter_where)) {
    $sql_where_clause = " WHERE " . implode(' AND ', $sql_filter_where);
}

$sql_select = "SELECT * FROM promotions" . $sql_where_clause . " LIMIT ?, ?";
$sql_filter_params[] = $offset;
$sql_filter_params[] = $items_per_page;
$sql_filter_types .= 'ii';

$stmt_select = $conn->prepare($sql_select);
if (!$stmt_select) {
    die("SQL error: " . $conn->error);
}
if (!empty($sql_filter_params)) {
    $stmt_select->bind_param($sql_filter_types, ...$sql_filter_params);
}
$stmt_select->execute();
$result = $stmt_select->get_result();

// Render danh sách khuyến mãi bằng foreach để chắc chắn luôn hiển thị
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khuyến mãi - Anh Em Rọt Store</title>
    <link rel="icon" href="assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/header.css">
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
            display: flex;
            gap: 2rem;
        }

        .promotion-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .promotion-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-color);
        }

        .filter-section {
            width: 25%;
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .filter-section h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .filter-section select,
        .filter-section input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .filter-section select:focus,
        .filter-section input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: var(--shadow-sm);
        }

        .promotion-list {
            width: 75%;
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .promotion-item {
            display: flex;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .promotion-item:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .promotion-image {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-right: 1rem;
        }

        .promotion-content h3 {
            color: var(--info-color);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .promotion-content p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .detail-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.8rem;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .detail-btn:hover {
            background: var(--success-color);
        }

        .admin-form {
            margin-top: 1rem;
            display: <?php echo $is_admin ? 'block' : 'none'; ?>;
        }

        .admin-form input,
        .admin-form select,
        .admin-form textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
        }

        .admin-form button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--accent-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .admin-form button:hover {
            background: var(--success-color);
        }

        .pagination {
            margin-top: 1rem;
            text-align: center;
        }

        .pagination a {
            padding: 0.5rem 1rem;
            margin: 0 0.2rem;
            background: var(--card-background);
            color: var(--accent-color);
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .pagination a:hover {
            background: var(--accent-color);
            color: white;
        }

        .pagination .active {
            background: var(--accent-color);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 1rem;
            }

            .filter-section, .promotion-list {
                width: 100%;
            }

            .promotion-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="filter-section">
            <h3>Lọc khuyến mãi</h3>
            <select id="type_filter" name="type_filter">
                <option value="">Tất cả loại</option>
                <option value="discount">Giảm giá</option>
                <option value="event">Sự kiện</option>
                <option value="shipping">Vận chuyển</option>
            </select>
            <input type="date" id="expiry_filter" name="expiry_filter">
        </div>
        <div class="promotion-list">
            <div class="promotion-header">
                <h1 class="promotion-title">Khuyến mãi</h1>
            </div>
            <?php
            if ($result->num_rows === 0) {
                echo '<p>Không có khuyến mãi nào.</p>';
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="promotion-item" data-type="'.strtolower($row['type']).'" data-expiry="'.$row['expiry_date'].'">';
                    echo '<img src="'.htmlspecialchars($row['image_url']).'" alt="'.htmlspecialchars($row['title']).'" class="promotion-image">';
                    echo '<div class="promotion-content">';
                    echo '<h3>'.htmlspecialchars($row['title']).'</h3>';
                    echo '<p>'.htmlspecialchars($row['description']).'</p>';
                    echo '<a href="promotion_detail.php?id='.$row['id'].'" class="detail-btn"><i class="fas fa-info-circle"></i> Chi tiết</a>';
                    echo '</div></div>';
                }
            }
            ?>
            <div class="admin-form">
                <h3>Thêm khuyến mãi (Chỉ admin)</h3>
                <form method="post" action="promotion.php">
                    <input type="text" name="title" placeholder="Tiêu đề" required>
                    <textarea name="description" placeholder="Mô tả" required></textarea>
                    <select name="type">
                        <option value="discount">Giảm giá</option>
                        <option value="event">Sự kiện</option>
                        <option value="shipping">Vận chuyển</option>
                    </select>
                    <input type="date" name="expiry_date" required>
                    <input type="text" name="image_url" placeholder="Đường dẫn ảnh" required>
                    <button type="submit"><i class="fas fa-plus"></i> Thêm khuyến mãi</button>
                </form>
            </div>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?><?php echo (!empty($filter_type) ? '&type_filter=' . urlencode($filter_type) : ''); ?><?php echo (!empty($filter_expiry) ? '&expiry_filter=' . urlencode($filter_expiry) : ''); ?>" class="<?php echo $page === $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.getElementById('type_filter').addEventListener('change', applyFilters);
        document.getElementById('expiry_filter').addEventListener('change', applyFilters);

        function applyFilters() {
            const type = document.getElementById('type_filter').value;
            const expiry = document.getElementById('expiry_filter').value;
            let url = 'promotion.php';
            const params = [];
            if (type) {
                params.push('type_filter=' + encodeURIComponent(type));
            }
            if (expiry) {
                params.push('expiry_filter=' + encodeURIComponent(expiry));
            }
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            window.location.href = url;
        }

        // Set initial filter values from URL params
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('type_filter')) {
                document.getElementById('type_filter').value = urlParams.get('type_filter');
            }
            if (urlParams.has('expiry_filter')) {
                document.getElementById('expiry_filter').value = urlParams.get('expiry_filter');
            }
        });
    </script>
</body>
</html>