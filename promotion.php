<?php
session_start();
require_once 'config/db.php';

// Kiểm tra quyền admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Xử lý thêm khuyến mãi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $expiry_date = $_POST['expiry_date'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO promotions (title, description, type, expiry_date, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $type, $expiry_date, $image_url);
    $stmt->execute();
    $stmt->close();
    header("Location: promotion.php");
    exit;
}

// Phân trang
$items_per_page = 2;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

$total_items = $conn->query("SELECT COUNT(*) FROM promotions")->fetch_row()[0];
$total_pages = ceil($total_items / $items_per_page);

$result = $conn->query("SELECT * FROM promotions ORDER BY expiry_date DESC LIMIT $offset, $items_per_page");
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
            align-items: center;
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
            color: var(--warning-color);
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
            <select id="type">
                <option value="">Tất cả loại</option>
                <option value="giảm giá">Giảm giá</option>
                <option value="tặng phẩm">Tặng phẩm</option>
            </select>
            <input type="date" id="expiry">
        </div>
        <div class="promotion-list">
            <div class="promotion-header">
                <h1 class="promotion-title">Chương trình khuyến mãi</h1>
            </div>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="promotion-item" data-type="<?php echo strtolower($row['type']); ?>" data-expiry="<?php echo $row['expiry_date']; ?>">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="promotion-image">
                    <div class="promotion-content">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="promotion_detail.php?id=<?php echo $row['id']; ?>" class="detail-btn"><i class="fas fa-info-circle"></i> Chi tiết</a>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="admin-form">
                <h3>Thêm khuyến mãi (Chỉ admin)</h3>
                <form method="post">
                    <input type="text" name="title" placeholder="Tiêu đề" required>
                    <textarea name="description" placeholder="Mô tả" required></textarea>
                    <select name="type">
                        <option value="giảm giá">Giảm giá</option>
                        <option value="tặng phẩm">Tặng phẩm</option>
                    </select>
                    <input type="date" name="expiry_date" required>
                    <input type="text" name="image_url" placeholder="Đường dẫn ảnh" required>
                    <button type="submit"><i class="fas fa-plus"></i> Thêm khuyến mãi</button>
                </form>
            </div>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $page === $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.getElementById('type').addEventListener('change', filterPromotions);
        document.getElementById('expiry').addEventListener('change', filterPromotions);

        function filterPromotions() {
            const type = document.getElementById('type').value.toLowerCase();
            const expiry = document.getElementById('expiry').value;
            const items = document.querySelectorAll('.promotion-item');

            items.forEach(item => {
                const itemType = item.dataset.type;
                const itemExpiry = item.dataset.expiry;

                const typeMatch = !type || itemType === type;
                const expiryMatch = !expiry || itemExpiry === expiry;

                item.style.display = typeMatch && expiryMatch ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>