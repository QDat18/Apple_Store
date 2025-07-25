<?php
// api/products.php
// File này sẽ xử lý các yêu cầu tìm kiếm sản phẩm từ frontend React

// Bắt đầu session nếu cần (ví dụ: để kiểm tra quyền truy cập nếu API yêu cầu xác thực)
// session_start();

// Bao gồm file kết nối cơ sở dữ liệu
// Đảm bảo đường dẫn này chính xác so với vị trí của file products.php
// Ví dụ: nếu products.php nằm trong /api/, thì db.php nằm trong /config/, đường dẫn sẽ là '../config/db.php'
require_once '../config/db.php';

// Đặt header để trình duyệt biết rằng phản hồi là JSON
header('Content-Type: application/json');

// Lấy tham số tìm kiếm từ URL (GET request)
// Sử dụng null coalescing operator (?? '') để đảm bảo biến luôn là chuỗi, tránh lỗi nếu tham số không tồn tại
$search_query = $_GET['query'] ?? '';

// Mảng để lưu trữ các sản phẩm tìm thấy
$products_data = [];

// Kiểm tra nếu có từ khóa tìm kiếm
if (!empty($search_query)) {
    // Chuẩn bị từ khóa tìm kiếm cho LIKE statement (thêm dấu % để tìm kiếm một phần)
    $search_term = '%' . $search_query . '%';

    // Lấy kết nối cơ sở dữ liệu từ db.php (biến $conn được tạo ra trong db.php)
    global $conn;

    // Kiểm tra xem kết nối có tồn tại không
    if (!$conn) {
        // Ghi log lỗi và trả về phản hồi JSON báo lỗi
        error_log("Lỗi kết nối CSDL trong api/products.php: \$conn không được thiết lập.");
        echo json_encode(['error' => 'Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.']);
        exit;
    }

    // Truy vấn chính để lấy thông tin sản phẩm cơ bản
    // `status = 1` để chỉ lấy các sản phẩm đang hoạt động (còn hàng)
    // JOIN với product_variants để lấy giá và tồn kho (có thể lấy từ biến thể mặc định hoặc biến thể đầu tiên)
    // JOIN với categories để lấy tên danh mục
    // GROUP BY p.id để tránh trùng lặp sản phẩm nếu có nhiều biến thể khớp
    $stmt = $conn->prepare("
        SELECT
            p.id,
            p.product_name AS name,
            p.description,
            pv.variant_price AS price, -- Lấy giá từ biến thể mặc định (hoặc biến thể đầu tiên)
            pv.stock_quantity AS stock,
            c.name AS category_name -- Lấy tên danh mục
        FROM products p
        JOIN product_variants pv ON p.id = pv.product_id
        JOIN categories c ON p.category_id = c.id
        WHERE
            p.status = 1 AND
            (p.product_name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)
        GROUP BY p.id -- Group by product ID để tránh trùng lặp sản phẩm nếu nhiều biến thể khớp
        LIMIT 10 -- Giới hạn số lượng kết quả để tránh tải quá nhiều dữ liệu
    ");

    if ($stmt) {
        // Gán tham số cho prepared statement
        $stmt->bind_param("sss", $search_term, $search_term, $search_term);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($product = $result->fetch_assoc()) {
            $product['variants'] = []; // Khởi tạo mảng biến thể cho mỗi sản phẩm

            // Truy vấn các biến thể và thuộc tính của từng sản phẩm
            // Sử dụng LEFT JOIN để đảm bảo sản phẩm vẫn hiển thị ngay cả khi không có thuộc tính cụ thể
            // GROUP_CONCAT để gom các thuộc tính thành một chuỗi duy nhất
            $stmt_variants = $conn->prepare("
                SELECT
                    pv.id AS variant_id,
                    pv.variant_code,
                    pv.variant_price,
                    pv.stock_quantity,
                    GROUP_CONCAT(DISTINCT CONCAT(va.name, ': ', vav.value) ORDER BY va.name SEPARATOR '; ') AS attributes_display
                FROM product_variants pv
                LEFT JOIN product_variant_attributes pva ON pv.id = pva.variant_id
                LEFT JOIN variant_attribute_values vav ON pva.attribute_value_id = vav.id
                LEFT JOIN variant_attributes va ON vav.attribute_id = va.id
                WHERE pv.product_id = ?
                GROUP BY pv.id
                ORDER BY pv.variant_price ASC
            ");

            if ($stmt_variants) {
                $stmt_variants->bind_param("i", $product['id']);
                $stmt_variants->execute();
                $variants_result = $stmt_variants->get_result();

                while ($variant = $variants_result->fetch_assoc()) {
                    $product['variants'][] = [
                        'variant_id' => $variant['variant_id'],
                        'variant_code' => $variant['variant_code'],
                        'variant_price' => (float)$variant['variant_price'],
                        'stock_quantity' => (int)$variant['stock_quantity'],
                        'attributes' => $variant['attributes_display'] // Ví dụ: "Màu sắc: Đen; Dung lượng: 256GB"
                    ];
                }
                $stmt_variants->close();
            } else {
                error_log("Lỗi chuẩn bị truy vấn biến thể cho sản phẩm ID " . $product['id'] . ": " . $conn->error);
            }
            $products_data[] = $product;
        }
        $stmt->close();
    } else {
        error_log("Lỗi chuẩn bị truy vấn sản phẩm chính: " . $conn->error);
    }
    // Đóng kết nối CSDL sau khi hoàn thành tất cả các truy vấn
    $conn->close();
}

// Trả về dữ liệu sản phẩm dưới dạng JSON
echo json_encode($products_data);
?>
