<?php
require_once 'config/db.php';

$news_data = null;

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $news_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} elseif (isset($_GET['slug'])) {
    $slug = $_GET['slug'];
    $stmt = $conn->prepare("SELECT * FROM news WHERE slug = ?");
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    $news_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}


if (!$news_data) {
    header("Location: news.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news_data['title']); ?> - Anh Em Rọt Store</title>
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
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: var(--card-background);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 2rem;
        }

        .detail-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .detail-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 0.5rem;
        }

        .detail-header p {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .detail-content img {
            max-width: 100%;
            height: auto;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .detail-content p {
            font-size: 1rem;
            color: var(--text-primary);
            margin-bottom: 1rem;
            white-space: pre-wrap; /* Preserve whitespace and line breaks */
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: none;
            border: 2px solid var(--border-color);
            color: var(--text-secondary);
            border-radius: var(--border-radius);
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .back-btn:hover {
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .detail-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="detail-header">
            <h1 class="detail-title"><?php echo htmlspecialchars($news_data['title']); ?></h1>
            <p>Ngày đăng: <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($news_data['created_at']))); ?></p>
        </div>
        <div class="detail-content">
            <img src="<?php echo htmlspecialchars($news_data['image']); ?>" alt="<?php echo htmlspecialchars($news_data['title']); ?>">
            <p><?php echo nl2br(htmlspecialchars($news_data['content'])); ?></p>
        </div>
        <a href="news.php" class="back-btn"><i class="fas fa-arrow-left"></i> Quay lại Tin tức</a>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>