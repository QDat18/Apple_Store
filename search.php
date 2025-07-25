<?php
header('Content-Type: application/json; charset=utf-8');
require_once $_SERVER['DOCUMENT_ROOT'] . '/Apple_Shop/config/db.php';

function removeDiacritics($text) {
    if (empty($text)) return $text;
    $unicode = [
        'a' => '/[àáảãạăắằẳẵặâấầẩẫậ]/u',
        'e' => '/[èéẻẽẹêếềểễệ]/u',
        'i' => '/[ìíỉĩị]/u',
        'o' => '/[òóỏõọôốồổỗộơớờởỡợ]/u',
        'u' => '/[ùúủũụưứừửữự]/u',
        'y' => '/[ỳýỷỹỵ]/u',
        'd' => '/[đ]/u',
        'A' => '/[ÀÁẢÃẠĂẮẰẲẴẶÂẤẦẨẪẬ]/u',
        'E' => '/[ÈÉẺẼẸÊẾỀỂỄỆ]/u',
        'I' => '/[ÌÍỈĨỊ]/u',
        'O' => '/[ÒÓỎÕỌÔỐỒỔỖỘƠỚỜỞỠỢ]/u',
        'U' => '/[ÙÚỦŨỤƯỨỪỬỮỰ]/u',
        'Y' => '/[ỲÝỶỸỴ]/u',
        'D' => '/[Đ]/u',
    ];
    foreach ($unicode as $nonUnicode => $pattern) {
        $text = preg_replace($pattern, $nonUnicode, $text);
    }
    return $text;
}

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (strlen($query) < 2) {
    echo json_encode([
        'status' => 'success',
        'data' => [],
        'query' => $query,
        'message' => 'Truy vấn phải có ít nhất 2 ký tự.'
    ], JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
    exit;
}

$normalized_query = removeDiacritics($query);
$like_query = "%$normalized_query%";
$original_like_query = "%$query%";

// Corrected WHERE clause to search using both original and normalized queries
$stmt = $conn->prepare("
    SELECT
        p.id,
        p.product_name,
        p.product_image,
        MIN(pv.variant_price) AS price,
        p.category_id
    FROM
        products p
    JOIN
        product_variants pv ON p.id = pv.product_id
    WHERE
        (p.product_name LIKE ? OR p.product_name LIKE ?) -- Search using both original and normalized queries
        AND p.status = 1
        AND pv.status = 1
    GROUP BY
        p.id
    ORDER BY
        CASE
            WHEN p.product_name LIKE ? THEN 1
            ELSE 2
        END,
        p.product_name ASC
    LIMIT 10
");

if (!$stmt) {
    error_log("Prepare failed in search.php: " . $conn->error);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi truy vấn cơ sở dữ liệu',
        'query' => $query
    ], JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
    exit;
}

// Bind parameters: 1 for original_like_query, 1 for like_query, 1 for original_like_query in ORDER BY
$stmt->bind_param("sss", $original_like_query, $like_query, $original_like_query);
if (!$stmt->execute()) {
    error_log("Execute failed in search.php: " . $stmt->error);
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi thực thi truy vấn',
        'query' => $query
    ], JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
    exit;
}

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $image_path = '/Apple_Shop/ ' . $row['product_image'];
    if (empty($row['product_image']) || !file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
        $image_path = '/Apple_Shop/assets/products/default_product.png';
    }
    // Apply 10% discount for iPhone (category_id 1) and Mac (category_id 2)
    $row['price'] = in_array($row['category_id'], [1, 2]) ? (float)$row['price'] * 0.9 : (float)$row['price'];
    $results[] = [
        'id' => (int)$row['id'],
        'product_name' => htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8'),
        'product_image' => $image_path,
        'price' => number_format($row['price'], 0, ',', '.'),
        'currency' => 'VND',
        'product_link' => '/Apple_Shop/products/product_detail.php?id=' . $row['id']
    ];
}
$stmt->close();
$conn->close();

echo json_encode([
    'status' => 'success',
    'data' => $results,
    'query' => $query,
    'message' => empty($results) ? 'Không tìm thấy sản phẩm phù hợp.' : ''
], JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
?>