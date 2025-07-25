<?php
session_start();
require_once 'config/db.php';

// Kiểm tra quyền admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Helper function to generate slug
function generateSlug($string) {
    $string = trim($string);
    $string = str_replace(['á','à','ả','ạ','ã','ă','ắ','ằ','ẳ','ặ','ẵ','â','ấ','ầ','ẩ','ậ','ẫ'], 'a', $string);
    $string = str_replace(['Á','À','Ả','Ạ','Ã','Ă','Ắ','Ằ','Ẳ','Ặ','Ẵ','Â','Ấ','Ầ','Ẩ','Ậ','Ẫ'], 'A', $string);
    $string = str_replace(['é','è','ẻ','ẹ','ẽ','ê','ế','ề','ể','ệ','ễ'], 'e', $string);
    $string = str_replace(['É','È','Ẻ','Ẹ','Ẽ','Ê','Ề','Ể','Ệ','Ễ'], 'E', $string);
    $string = str_replace(['í','ì','ỉ','ị','ĩ'], 'i', $string);
    $string = str_replace(['Í','Ì','Ỉ','Ị','Ĩ'], 'I', $string);
    $string = str_replace(['ó','ò','ỏ','ọ','õ','ô','ố','ồ','ổ','ộ','ỗ','ơ','ớ','ờ','ở','ợ','ỡ'], 'o', $string);
    $string = str_replace(['Ó','Ò','Ỏ','Ọ','Õ','Ô','Ố','Ồ','Ổ','Ộ','Ỗ','Ơ','Ớ','Ờ','Ở','Ợ','Ỡ'], 'O', $string);
    $string = str_replace(['ú','ù','ủ','ụ','ũ','ư','ứ','ừ','ử','ự','ữ'], 'u', $string);
    $string = str_replace(['Ú','Ù','Ủ','Ụ','Ũ','Ư','Ứ','Ừ','Ử','Ự','Ữ'], 'U', $string);
    $string = str_replace(['ý','ỳ','ỷ','ỵ','ỹ'], 'y', $string);
    $string = str_replace(['Ý','Ỳ','Ỷ','Ỵ','Ỹ'], 'Y', $string);
    $string = str_replace(['đ'], 'd', $string);
    $string = str_replace(['Đ'], 'D', $string);
    $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string);
    $string = str_replace(' ', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    $string = strtolower($string);
    return $string;
}

// Xử lý thêm tin tức
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    // Removed $category as it's not in DB schema
    $created_at = $_POST['created_date']; // Map created_date from form to created_at in DB
    $image = $_POST['image_url']; // Renamed to image

    // Generate slug
    $slug = generateSlug($title);
    $original_slug = $slug;
    $counter = 1;
    // Check for slug uniqueness
    while (true) {
        $check_slug_stmt = $conn->prepare("SELECT COUNT(*) FROM news WHERE slug = ?");
        $check_slug_stmt->bind_param("s", $slug);
        $check_slug_stmt->execute();
        $slug_count = $check_slug_stmt->get_result()->fetch_row()[0];
        $check_slug_stmt->close();

        if ($slug_count == 0) {
            break; // Slug is unique
        }
        $slug = $original_slug . '-' . $counter++;
    }

    $stmt = $conn->prepare("INSERT INTO news (title, slug, content, image, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $slug, $content, $image, $created_at);
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

// Fetch news, order by created_at
$result = $conn->query("SELECT * FROM news ORDER BY created_at DESC LIMIT $offset, $items_per_page");
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

        .filter-section select, .filter-section input {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .filter-section select:focus, .filter-section input:focus {
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

        .news-content {
            flex-grow: 1;
        }

        .news-content h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            color: var(--text-primary);
        }

        .news-content p {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .news-date {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        .read-more {
            display: inline-block;
            margin-top: 0.5rem;
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        .admin-form {
            background: var(--card-background);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            margin-top: 2rem;
        }

        .admin-form h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .admin-form input,
        .admin-form textarea,
        .admin-form select {
            width: calc(100% - 1rem); /* Adjusted for padding */
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
        }

        .admin-form button {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            transition: var(--transition);
        }

        .admin-form button:hover {
            opacity: 0.9;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            gap: 0.5rem;
        }

        .pagination a {
            display: block;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--text-primary);
            transition: var(--transition);
        }

        .pagination a.active,
        .pagination a:hover {
            background-color: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }

            .filter-section, .news-list {
                width: 100%;
            }

            .news-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .news-image {
                width: 100%;
                height: auto;
                margin-right: 0;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="filter-section">
            <h3>Bộ lọc</h3>
            <label for="date">Ngày đăng:</label>
            <input type="date" id="date" onchange="filterNews()">
        </div>
        <div class="news-list">
            <h2 class="news-header">Tin tức mới nhất</h2>
            <?php
            if ($result->num_rows === 0) {
                echo '<p>Không có tin tức nào.</p>';
            } else {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="news-item" data-date="'.htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))).'">';
                    echo '<img src="'.htmlspecialchars($row['image']).'" alt="'.htmlspecialchars($row['title']).'" class="news-image">';
                    echo '<div class="news-content">';
                    echo '<h4>'.htmlspecialchars($row['title']).'</h4>';
                    echo '<p>'.htmlspecialchars(mb_substr($row['content'], 0, 150, 'UTF-8')).'...</p>';
                    echo '<span class="news-date">Ngày đăng: '.htmlspecialchars(date('d/m/Y', strtotime($row['created_at']))).'</span>';
                    echo '<a href="news_detail.php?slug='.htmlspecialchars($row['slug']).'" class="read-more">Đọc thêm <i class="fas fa-arrow-right"></i></a>';
                    echo '</div></div>';
                }
            }
            ?>
            <?php if ($is_admin): ?>
            <div class="admin-form">
                <h3>Thêm tin tức mới</h3>
                <form action="news.php" method="POST">
                    <input type="text" name="title" placeholder="Tiêu đề tin tức" required>
                    <textarea name="content" placeholder="Nội dung tin tức" rows="5" required></textarea>
                    <label for="admin-created-date">Ngày đăng:</label>
                    <input type="date" name="created_date" id="admin-created-date" required>
                    <input type="text" name="image_url" placeholder="Đường dẫn ảnh" required>
                    <button type="submit"><i class="fas fa-plus"></i> Thêm tin tức</button>
                </form>
            </div>
            <?php endif; ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $page === $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        document.getElementById('date').addEventListener('change', filterNews);

        function filterNews() {
            const date = document.getElementById('date').value;
            const items = document.querySelectorAll('.news-item');

            items.forEach(item => {
                const itemDate = item.dataset.date;

                const dateMatch = !date || itemDate === date;

                item.style.display = dateMatch ? 'flex' : 'none';
            });
        }
    </script>
</body>
</html>