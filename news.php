<?php
session_start();
require_once 'config/db.php';

// Kiểm tra quyền admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Xử lý thêm tin tức
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category = $_POST['category'];
    $created_date = $_POST['created_date'];
    $image_url = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO news (title, content, category, created_date, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $content, $category, $created_date, $image_url);
    $stmt->execute();
    $stmt->close();
    header("Location: news.php");
    exit;
}

// Phân trang
$items_per_page = 2;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $items_per_page;

$total_items = $conn->query("SELECT COUNT(*) FROM news")->fetch_row()[0];
$total_pages = ceil($total_items / $items_per_page);

$result = $conn->query("SELECT * FROM news ORDER BY created_date DESC LIMIT $offset, $items_per_page");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức - Anh Em Rọt Store</title>
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

        .news-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .news-title {
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

        .news-list {
            width: 75%;
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .news-item {
            display: flex;
            margin-bottom: 1.5rem;
            padding: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .news-item:hover {
            box-shadow: var(--shadow-lg);
            transform: translateY(-5px);
        }

        .news-image {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border-radius: var(--border-radius);
            margin-right: 1rem;
        }

        .news-content h3 {
            color: var(--info-color);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .news-content p {
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

            .filter-section, .news-list {
                width: 100%;
            }

            .news-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="filter-section">
            <h3>Lọc tin tức</h3>
            <select id="category">
                <option value="">Tất cả danh mục</option>
                <option value="sản phẩm mới">Sản phẩm mới</option>
                <option value="sự kiện">Sự kiện</option>
                <option value="chính sách">Chính sách</option>
            </select>
            <input type="date" id="date">
        </div>
        <div class="news-list">
            <div class="news-header">
                <h1 class="news-title">Tin tức</h1>
            </div>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="news-item" data-category="<?php echo strtolower($row['category']); ?>" data-date="<?php echo $row['created_date']; ?>">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="news-image">
                    <div class="news-content">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo htmlspecialchars($row['content']); ?></p>
                        <a href="news_detail.php?id=<?php echo $row['id']; ?>" class="detail-btn"><i class="fas fa-info-circle"></i> Chi tiết</a>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="admin-form">
                <h3>Thêm tin tức (Chỉ admin)</h3>
                <form method="post">
                    <input type="text" name="title" placeholder="Tiêu đề" required>
                    <textarea name="content" placeholder="Nội dung" required></textarea>
                    <select name="category">
                        <option value="sản phẩm mới">Sản phẩm mới</option>
                        <option value="sự kiện">Sự kiện</option>
                        <option value="chính sách">Chính sách</option>
                    </select>
                    <input type="date" name="created_date" required>
                    <input type="text" name="image_url" placeholder="Đường dẫn ảnh" required>
                    <button type="submit"><i class="fas fa-plus"></i> Thêm tin tức</button>
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
        document.getElementById('category').addEventListener('change', filterNews);
        document.getElementById('date').addEventListener('change', filterNews);

        function filterNews() {
            const category = document.getElementById('category').value.toLowerCase();
            const date = document.getElementById('date').value;
            const items = document.querySelectorAll('.news-item');

            items.forEach(item => {
                const itemCategory = item.dataset.category;
                const itemDate = item.dataset.date;

                const categoryMatch = !category || itemCategory.includes(category);
                const dateMatch = !date || itemDate === date;

                item.style.display = categoryMatch && dateMatch ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>