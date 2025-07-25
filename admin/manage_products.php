<?php
session_start();
require_once '../config/db.php';

// Kiểm tra quyền truy cập admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Bắt đầu bộ đệm đầu ra
ob_start();

$errors = [];
$success = false;
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

/**
 * Lấy dữ liệu sản phẩm cùng với thông tin biến thể và danh mục.
 */
function getProductsData($conn, $search_query, $sort_by, $sort_order, $page, $limit, $category_id) {
    $products = [];
    $total_products = 0;
    $total_pages = 0;
    $offset = ($page - 1) * $limit;

    // Truy vấn tổng số sản phẩm cho phân trang
    $count_sql = "SELECT COUNT(*) FROM products p";
    $where_clauses_count = [];
    $params_count = [];
    $types_count = "";

    if (!empty($search_query)) {
        $where_clauses_count[] = "(p.product_name LIKE ? OR p.description LIKE ? OR EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.id AND pv.variant_code LIKE ?))";
        $params_count[] = "%" . $search_query . "%";
        $params_count[] = "%" . $search_query . "%";
        $params_count[] = "%" . $search_query . "%";
        $types_count .= "sss";
    }

    if (!empty($category_id)) {
        $where_clauses_count[] = "p.category_id = ?";
        $params_count[] = $category_id;
        $types_count .= "i";
    }

    if (!empty($where_clauses_count)) {
        $count_sql .= " WHERE " . implode(" AND ", $where_clauses_count);
    }

    $stmt_count = $conn->prepare($count_sql);
    if ($stmt_count === false) {
        error_log("Failed to prepare count statement: " . $conn->error);
        return ['products' => [], 'total_products' => 0, 'total_pages' => 0];
    }
    if (!empty($params_count)) {
        $stmt_count->bind_param($types_count, ...$params_count);
    }
    $stmt_count->execute();
    $total_products = $stmt_count->get_result()->fetch_row()[0];
    $stmt_count->close();

    $total_pages = ceil($total_products / $limit);

    // Truy vấn chính để lấy sản phẩm
    $sql = "
        SELECT
            p.id,
            p.product_name,
            p.description,
            p.price AS base_price,
            p.product_image,
            p.status,
            p.created_at,
            p.updated_at,
            c.name AS category_name,
            GROUP_CONCAT(
                DISTINCT CONCAT(
                    pv.id, ':', pv.variant_code, ':', pv.stock_quantity, ':', pv.variant_price, ':', COALESCE(pv.variant_image, ''), ':', pv.status, ':',
                    (SELECT GROUP_CONCAT(CONCAT(va.name, ':', vav.value) SEPARATOR ';')
                     FROM product_variant_attribute_links pval
                     JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
                     JOIN variant_attributes va ON vav.attribute_id = va.id
                     WHERE pval.variant_id = pv.id)
                ) ORDER BY pv.id SEPARATOR '|||'
            ) AS variants_data
        FROM products p
        JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_variants pv ON p.id = pv.product_id
    ";

    $where_clauses = [];
    $params = [];
    $types = "";

    if (!empty($search_query)) {
        $where_clauses[] = "(p.product_name LIKE ? OR p.description LIKE ? OR EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id = p.id AND pv.variant_code LIKE ?))";
        $params[] = "%" . $search_query . "%";
        $params[] = "%" . $search_query . "%";
        $params[] = "%" . $search_query . "%";
        $types .= "sss";
    }

    if (!empty($category_id)) {
        $where_clauses[] = "p.category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }

    if (!empty($where_clauses)) {
        $sql .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $sql .= " GROUP BY p.id";

    // Sorting
    $order_map = [
        'name' => 'p.product_name',
        'price_asc' => 'p.price ASC',
        'price_desc' => 'p.price DESC',
        'created_at' => 'p.created_at DESC'
    ];
    $sql .= " ORDER BY " . ($order_map[$sort_by] ?? 'p.created_at DESC');

    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Failed to prepare getProductsData statement: " . $conn->error);
        return ['products' => [], 'total_products' => 0, 'total_pages' => 0];
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $variants = [];
        if (!empty($row['variants_data'])) {
            foreach (explode('|||', $row['variants_data']) as $variant_str) {
                $parts = explode(':', $variant_str, 7);
                if (count($parts) >= 6) {
                    $variant_id = $parts[0];
                    $sku = $parts[1];
                    $stock_quantity = $parts[2];
                    $price = $parts[3];
                    $image_url = $parts[4];
                    $status = $parts[5];
                    $attributes_str = $parts[6] ?? '';

                    $attributes = [];
                    if (!empty($attributes_str)) {
                        foreach (explode(';', $attributes_str) as $attr_pair) {
                            $attr_parts = explode(':', $attr_pair, 2);
                            if (count($attr_parts) === 2) {
                                list($attr_name, $attr_value) = $attr_parts;
                                $attributes[] = ['name' => $attr_name, 'value' => $attr_value];
                            }
                        }
                    }

                    $variants[] = [
                        'id' => $variant_id,
                        'sku' => $sku,
                        'stock_quantity' => $stock_quantity,
                        'price' => $price,
                        'image_url' => $image_url,
                        'status' => $status,
                        'attributes' => $attributes
                    ];
                }
            }
        }
        $row['variants'] = $variants;
        unset($row['variants_data']);
        $products[] = $row;
    }
    $stmt->close();
    return ['products' => $products, 'total_products' => $total_products, 'total_pages' => $total_pages];
}

/**
 * Lấy tất cả danh mục
 */
function getCategories($conn) {
    $categories = [];
    $result = $conn->query("SELECT id, name, parent_id FROM categories WHERE is_active = 1 ORDER BY name ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

/**
 * Lấy tất cả thuộc tính biến thể và giá trị của chúng
 */
function getVariantAttributes($conn) {
    $attributes = [];
    $sql = "
        SELECT va.id, va.name, va.display_name, GROUP_CONCAT(vav.id, ':', vav.value SEPARATOR ';') AS valuess
        FROM variant_attributes va
        LEFT JOIN variant_attribute_values vav ON va.id = vav.attribute_id
        GROUP BY va.id
        ORDER BY va.name ASC";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $values = [];
            if (!empty($row['valuess'])) {
                foreach (explode(';', $row['valuess']) as $value_str) {
                    list($value_id, $value) = explode(':', $value_str, 2);
                    $values[] = ['id' => $value_id, 'value' => $value];
                }
            }
            $attributes[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'display_name' => $row['display_name'],
                'valuess' => $values
            ];
        }
    }
    return $attributes;
}

/**
 * Xử lý hình ảnh tải lên
 * Đã sửa để trả về đường dẫn tuyệt đối từ thư mục gốc của trang web
 */
function handleImageUpload($file_input_name) {
    if (!isset($_FILES[$file_input_name]) || $_FILES[$file_input_name]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$file_input_name];
    $file_name = $file['name'];
    $file_tmp_name = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($file_ext, $allowed_ext)) {
        throw new Exception("Chỉ cho phép tải lên ảnh định dạng JPG, JPEG, PNG, GIF.");
    }
    if ($file_size > 5 * 1024 * 1024) {
        throw new Exception("Kích thước file quá lớn (tối đa 5MB).");
    }

    // Đường dẫn vật lý đến thư mục uploads trên server
    $upload_dir_physical = realpath(__DIR__ . '/../assets/products/');
    if (!$upload_dir_physical) {
        throw new Exception("Thư mục tải ảnh lên không tồn tại hoặc không thể truy cập.");
    }

    $new_file_name = uniqid('', true) . '.' . $file_ext;
    $file_destination_physical = $upload_dir_physical . '/' . $new_file_name;

    if (move_uploaded_file($file_tmp_name, $file_destination_physical)) {
        // Trả về đường dẫn tương đối từ web root (bắt đầu bằng /)
        // Giả định 'assets/products/' là thư mục con trực tiếp của web root
        // Thêm /Apple_Shop/ vào đầu đường dẫn theo yêu cầu của người dùng
        return '/Apple_Shop/assets/products/' . $new_file_name;
    } else {
        throw new Exception("Lỗi khi tải ảnh lên.");
    }
}

/**
 * Xóa file ảnh cũ nếu có
 * Đã sửa để xử lý đường dẫn tuyệt đối từ web root
 */
function deleteOldImage($image_path) {
    if ($image_path) {
        // Chuyển đường dẫn web root thành đường dẫn vật lý trên server
        // Giả định web root là thư mục cha của thư mục hiện tại (admin)
        // Đảm bảo đường dẫn vật lý được tạo đúng với cấu trúc thư mục
        $doc_root = realpath(__DIR__ . '/../');
        // Xóa /Apple_Shop khỏi đầu đường dẫn nếu nó tồn tại để có đường dẫn tương đối đúng
        $relative_path_from_doc_root = str_replace('/Apple_Shop', '', $image_path);
        $physical_path = $doc_root . $relative_path_from_doc_root;

        if (file_exists($physical_path) && !is_dir($physical_path)) {
            unlink($physical_path);
        }
    }
}

/**
 * Kiểm tra SKU có tồn tại và không trùng lặp
 */
function checkDuplicateSKU($conn, $sku, $variant_id = 0, $product_id = 0) {
    $sql = "SELECT id FROM product_variants WHERE variant_code = ? AND id != ? AND product_id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $sku, $variant_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    return $exists;
}

// --- Xử lý form thêm/sửa sản phẩm ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'errors' => []];

    $product_id = intval($_POST['product_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $base_price = floatval($_POST['base_price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $existing_image_url = $_POST['existing_image_url'] ?? null;
    $remove_image = isset($_POST['remove_product_image']) ? 1 : 0;

    // Kiểm tra đầu vào
    if (empty($name) || empty($description) || $base_price <= 0 || $category_id <= 0) {
        $errors[] = "Vui lòng điền đầy đủ thông tin sản phẩm (Tên, Mô tả, Giá cơ bản, Danh mục).";
    }

    // Kiểm tra danh mục tồn tại
    $stmt_check_cat = $conn->prepare("SELECT id FROM categories WHERE id = ? AND is_active = 1");
    $stmt_check_cat->bind_param("i", $category_id);
    $stmt_check_cat->execute();
    if ($stmt_check_cat->get_result()->num_rows === 0) {
        $errors[] = "Danh mục không hợp lệ hoặc không tồn tại.";
    }
    $stmt_check_cat->close();

    $product_image_path = $existing_image_url;
    if ($remove_image && $existing_image_url) {
        deleteOldImage($existing_image_url);
        $product_image_path = null;
    }

    // Xử lý ảnh sản phẩm
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        try {
            if ($existing_image_url && !$remove_image) {
                deleteOldImage($existing_image_url);
            }
            $product_image_path = handleImageUpload('product_image');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            if ($product_id > 0) {
                // Cập nhật sản phẩm
                $sql_product = "UPDATE products SET product_name = ?, description = ?, price = ?, category_id = ?, product_image = ?, status = ?, updated_at = NOW() WHERE id = ?";
                $stmt_product = $conn->prepare($sql_product);
                if ($stmt_product === false) {
                    throw new Exception("Failed to prepare update product statement: " . $conn->error);
                }
                $stmt_product->bind_param("ssdisii", $name, $description, $base_price, $category_id, $product_image_path, $is_active, $product_id);
                $stmt_product->execute();
                $stmt_product->close();

                // Log cập nhật
                $user_id = $_SESSION['user_id'] ?? null;
                if ($user_id) {
                    $log_action = 'product_update';
                    $log_details = "Cập nhật sản phẩm ID: {$product_id}, Tên: {$name}";
                    $stmt_log = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
                    $stmt_log->bind_param("iss", $user_id, $log_action, $log_details);
                    $stmt_log->execute();
                    $stmt_log->close();
                }
                $response['message'] = 'Cập nhật sản phẩm thành công!';
            } else {
                // Thêm sản phẩm mới
                $sql_product = "INSERT INTO products (product_name, description, price, category_id, product_image, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_product = $conn->prepare($sql_product);
                if ($stmt_product === false) {
                    throw new Exception("Failed to prepare insert product statement: " . $conn->error);
                }
                $stmt_product->bind_param("ssdisi", $name, $description, $base_price, $category_id, $product_image_path, $is_active);
                $stmt_product->execute();
                $product_id = $conn->insert_id;
                $stmt_product->close();

                // Log thêm mới
                $user_id = $_SESSION['user_id'] ?? null;
                if ($user_id) {
                    $log_action = 'product_create';
                    $log_details = "Tạo sản phẩm mới ID: {$product_id}, Tên: {$name}";
                    $stmt_log = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
                    $stmt_log->bind_param("iss", $user_id, $log_action, $log_details);
                    $stmt_log->execute();
                    $stmt_log->close();
                }
                $response['message'] = 'Thêm sản phẩm mới thành công!';
            }

            // Xử lý biến thể sản phẩm
            if ($product_id > 0) {
                $existing_variant_ids_post = array_map('intval', $_POST['existing_variant_ids'] ?? []);
                $current_variants_in_db = [];
                $stmt_get_variants = $conn->prepare("SELECT id, variant_image FROM product_variants WHERE product_id = ?");
                $stmt_get_variants->bind_param("i", $product_id);
                $stmt_get_variants->execute();
                $result_get_variants = $stmt_get_variants->get_result();
                while ($row = $result_get_variants->fetch_assoc()) {
                    $current_variants_in_db[$row['id']] = $row['variant_image'];
                }
                $stmt_get_variants->close();

                $variants_to_delete = array_diff(array_keys($current_variants_in_db), $existing_variant_ids_post);
                if (!empty($variants_to_delete)) {
                    $placeholders = implode(',', array_fill(0, count($variants_to_delete), '?'));
                    $types_delete = str_repeat('i', count($variants_to_delete));
                    $stmt_delete_variants = $conn->prepare("DELETE FROM product_variants WHERE id IN ($placeholders)");
                    if ($stmt_delete_variants === false) {
                        throw new Exception("Failed to prepare delete product variants statement: " . $conn->error);
                    }
                    $stmt_delete_variants->bind_param($types_delete, ...$variants_to_delete);
                    $stmt_delete_variants->execute();
                    $stmt_delete_variants->close();

                    // Xóa liên kết thuộc tính
                    $stmt_delete_attrs = $conn->prepare("DELETE FROM product_variant_attribute_links WHERE variant_id IN ($placeholders)");
                    if ($stmt_delete_attrs === false) {
                        throw new Exception("Failed to prepare delete variant attribute links statement: " . $conn->error);
                    }
                    $stmt_delete_attrs->bind_param($types_delete, ...$variants_to_delete);
                    $stmt_delete_attrs->execute();
                    $stmt_delete_attrs->close();

                    foreach ($variants_to_delete as $del_variant_id) {
                        if (isset($current_variants_in_db[$del_variant_id])) {
                            deleteOldImage($current_variants_in_db[$del_variant_id]);
                        }
                    }
                }
            }

            if (isset($_POST['variants']) && is_array($_POST['variants'])) {
                foreach ($_POST['variants'] as $variant_index => $variant_data) {
                    $variant_id = intval($variant_data['id'] ?? 0);
                    $sku = trim($variant_data['sku'] ?? '');
                    $stock_quantity = intval($variant_data['stock_quantity'] ?? 0);
                    $variant_price = floatval($variant_data['price'] ?? 0);
                    $variant_status = isset($variant_data['status']) ? 1 : 0;
                    $existing_variant_image_url = $variant_data['existing_image_url'] ?? null;
                    $remove_variant_image = isset($variant_data['remove_variant_image']) ? 1 : 0;

                    if (empty($sku) || $stock_quantity < 0 || $variant_price <= 0) {
                        $errors[] = "Thông tin biến thể không hợp lệ (SKU, Số lượng, Giá) tại biến thể #{$variant_index}.";
                        continue;
                    }

                    // Kiểm tra trùng SKU
                    if (checkDuplicateSKU($conn, $sku, $variant_id, $product_id)) {
                        $errors[] = "SKU {$sku} đã tồn tại trong hệ thống.";
                        continue;
                    }

                    $variant_image_path = $existing_variant_image_url;
                    if ($remove_variant_image && $existing_variant_image_url) {
                        deleteOldImage($existing_variant_image_url);
                        $variant_image_path = null;
                    }

                    $variant_image_file_name = 'variants_image_' . $variant_index;
                    if (isset($_FILES[$variant_image_file_name]) && $_FILES[$variant_image_file_name]['error'] === UPLOAD_ERR_OK) {
                        try {
                            if ($existing_variant_image_url && !$remove_variant_image) {
                                deleteOldImage($existing_variant_image_url);
                            }
                            $variant_image_path = handleImageUpload($variant_image_file_name);
                        } catch (Exception $e) {
                            $errors[] = "Lỗi ảnh biến thể {$sku}: " . $e->getMessage();
                        }
                    }

                    if (empty($errors)) {
                        if ($variant_id > 0) {
                            // Cập nhật biến thể
                            $sql_variant = "UPDATE product_variants SET variant_code = ?, stock_quantity = ?, variant_price = ?, variant_image = ?, status = ? WHERE id = ? AND product_id = ?";
                            $stmt_variant = $conn->prepare($sql_variant);
                            if ($stmt_variant === false) {
                                throw new Exception("Failed to prepare update variant statement: " . $conn->error);
                            }
                            $stmt_variant->bind_param("sidsiii", $sku, $stock_quantity, $variant_price, $variant_image_path, $variant_status, $variant_id, $product_id);
                            $stmt_variant->execute();
                            $stmt_variant->close();
                        } else {
                            // Thêm biến thể mới
                            $sql_variant = "INSERT INTO product_variants (product_id, variant_code, stock_quantity, variant_price, variant_image, status) VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt_variant = $conn->prepare($sql_variant);
                            if ($stmt_variant === false) {
                                throw new Exception("Failed to prepare insert variant statement: " . $conn->error);
                            }
                            $stmt_variant->bind_param("isidis", $product_id, $sku, $stock_quantity, $variant_price, $variant_image_path, $variant_status);
                            $stmt_variant->execute();
                            $variant_id = $conn->insert_id;
                            $stmt_variant->close();
                        }

                        // Xử lý thuộc tính biến thể
                        if (isset($variant_data['attributes']) && is_array($variant_data['attributes'])) {
                            // Xóa thuộc tính cũ
                            $stmt_delete_attrs = $conn->prepare("DELETE FROM product_variant_attribute_links WHERE variant_id = ?");
                            if ($stmt_delete_attrs === false) {
                                throw new Exception("Failed to prepare delete variant attribute links statement: " . $conn->error);
                            }
                            $stmt_delete_attrs->bind_param("i", $variant_id);
                            $stmt_delete_attrs->execute();
                            $stmt_delete_attrs->close();

                            foreach ($variant_data['attributes'] as $attribute_id => $attribute_value_id) {
                                $attribute_id = intval($attribute_id);
                                $attribute_value_id = intval($attribute_value_id);

                                if ($attribute_id > 0 && $attribute_value_id > 0) {
                                    // Kiểm tra giá trị thuộc tính tồn tại
                                    $stmt_check_val = $conn->prepare("SELECT id FROM variant_attribute_values WHERE id = ? AND attribute_id = ?");
                                    $stmt_check_val->bind_param("ii", $attribute_value_id, $attribute_id);
                                    $stmt_check_val->execute();
                                    if ($stmt_check_val->get_result()->num_rows === 0) {
                                        $errors[] = "Giá trị thuộc tính ID {$attribute_value_id} không hợp lệ cho thuộc tính ID {$attribute_id}.";
                                        continue;
                                    }
                                    $stmt_check_val->close();

                                    $sql_insert_attr = "INSERT INTO product_variant_attribute_links (variant_id, attribute_value_id) VALUES (?, ?)";
                                    $stmt_insert_attr = $conn->prepare($sql_insert_attr);
                                    if ($stmt_insert_attr === false) {
                                        throw new Exception("Failed to prepare insert variant attribute link statement: " . $conn->error);
                                    }
                                    $stmt_insert_attr->bind_param("ii", $variant_id, $attribute_value_id);
                                    $stmt_insert_attr->execute();
                                    $stmt_insert_attr->close();
                                }
                            }
                        }
                    }
                }
            }

            if (empty($errors)) {
                $conn->commit();
                $response['success'] = true;
                // If it's an edit, return the updated product data to refresh the table row
                if ($product_id > 0) {
                    $updated_product_data = getProductsData($conn, '', 'created_at', 'desc', 1, 10, null); // Simplified fetch for updated row
                    foreach ($updated_product_data['products'] as $p) {
                        if ($p['id'] == $product_id) {
                            $response['product'] = $p;
                            break;
                        }
                    }
                }
            } else {
                $conn->rollback();
                $response['errors'] = $errors;
            }
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Product transaction failed: " . $e->getMessage());
            $response['errors'][] = 'Lỗi khi lưu sản phẩm: ' . $e->getMessage();
        }
    } else {
        $response['errors'] = $errors;
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        if ($response['success']) {
            $_SESSION['temp_message'] = ['content' => $response['message'], 'type' => 'success'];
        } else {
            $_SESSION['temp_message'] = ['content' => implode("<br>", $response['errors']), 'type' => 'error'];
        }
        header("Location: manage_products.php");
        exit;
    }
}

// --- Xử lý xóa/khôi phục sản phẩm ---
if (isset($_GET['action']) && ($_GET['action'] === 'delete' || $_GET['action'] === 'restore') && isset($_GET['id'])) {
    $response = ['success' => false, 'message' => ''];
    $product_id = intval($_GET['id']);
    $action_type = $_GET['action'];

    try {
        $user_id = $_SESSION['user_id'] ?? null;
        $status = ($action_type === 'restore') ? 1 : 0;
        $log_action = ($action_type === 'restore') ? 'product_restore' : 'product_delete';
        $log_details = ($action_type === 'restore') ? "Khôi phục sản phẩm ID: {$product_id}" : "Đánh dấu sản phẩm ID: {$product_id} là hết hàng.";
        $message = ($action_type === 'restore') ? 'Sản phẩm đã được khôi phục thành công!' : 'Sản phẩm đã được đánh dấu là hết hàng!';

        if ($user_id) {
            $stmt_log = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
            $stmt_log->bind_param("iss", $user_id, $log_action, $log_details);
            $stmt_log->execute();
            $stmt_log->close();
        }

        $stmt = $conn->prepare("UPDATE products SET status = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $product_id);
        $stmt->execute();
        $stmt->close();

        $stmt_variants = $conn->prepare("UPDATE product_variants SET status = ? WHERE product_id = ?");
        $stmt_variants->bind_param("ii", $status, $product_id);
        $stmt_variants->execute();
        $stmt_variants->close();

        $response['success'] = true;
        $response['message'] = $message;
        $response['product_id'] = $product_id;
        $response['new_status'] = $status;
    } catch (Exception $e) {
        error_log("Error " . $action_type . " product: " . $e->getMessage());
        $response['message'] = 'Lỗi khi ' . ($action_type === 'restore' ? 'khôi phục' : 'đánh dấu') . ' sản phẩm: ' . $e->getMessage();
    }

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        if ($response['success']) {
            $_SESSION['temp_message'] = ['content' => $response['message'], 'type' => 'success'];
        } else {
            $_SESSION['temp_message'] = ['content' => $response['message'], 'type' => 'error'];
        }
        header("Location: manage_products.php");
        exit;
    }
}


// --- Lấy dữ liệu cho trang ---
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search_query = trim($_GET['search'] ?? '');
$sort_by = $_GET['sort_by'] ?? 'created_at';
$sort_order = $_GET['sort_order'] ?? 'desc';
$filter_category_id = isset($_GET['category_id']) && $_GET['category_id'] !== '' ? intval($_GET['category_id']) : null;
$limit = 10;

$data = getProductsData($conn, $search_query, $sort_by, $sort_order, $current_page, $limit, $filter_category_id);
$products = $data['products'];
$total_products = $data['total_products'];
$total_pages = $data['total_pages'];

$categories = getCategories($conn);
$variant_attributes = getVariantAttributes($conn);

// Lấy dữ liệu sản phẩm để chỉnh sửa
$edit_product = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $product_id_to_edit = intval($_GET['id']);
    $stmt_edit = $conn->prepare("
        SELECT
            p.id,
            p.product_name,
            p.description,
            p.price AS base_price,
            p.product_image,
            p.status AS is_active,
            p.category_id,
            GROUP_CONCAT(
                DISTINCT CONCAT(
                    pv.id, ':', pv.variant_code, ':', pv.stock_quantity, ':', pv.variant_price, ':', COALESCE(pv.variant_image, ''), ':', pv.status, ':',
                    (SELECT GROUP_CONCAT(CONCAT(va.name, ':', vav.value, ':', vav.id) SEPARATOR ';')
                     FROM product_variant_attribute_links pval
                     JOIN variant_attribute_values vav ON pval.attribute_value_id = vav.id
                     JOIN variant_attributes va ON vav.attribute_id = va.id
                     WHERE pval.variant_id = pv.id)
                ) ORDER BY pv.id SEPARATOR '|||'
            ) AS variants_data
        FROM products p
        LEFT JOIN product_variants pv ON p.id = pv.product_id
        WHERE p.id = ?
        GROUP BY p.id
    ");
    $stmt_edit->bind_param("i", $product_id_to_edit);
    $stmt_edit->execute();
    $result_edit = $stmt_edit->get_result();
    $edit_product = $result_edit->fetch_assoc();
    $stmt_edit->close();

    if ($edit_product && !empty($edit_product['variants_data'])) {
        $variants = [];
        foreach (explode('|||', $edit_product['variants_data']) as $variant_str) {
            $parts = explode(':', $variant_str, 7);
            if (count($parts) >= 6) {
                $variant_id = $parts[0];
                $sku = $parts[1];
                $stock_quantity = $parts[2];
                $price = $parts[3];
                $image_url = $parts[4];
                $status = $parts[5];
                $attributes_str = $parts[6] ?? '';

                $attributes = [];
                if (!empty($attributes_str)) {
                    foreach (explode(';', $attributes_str) as $attr_pair) {
                        $attr_parts = explode(':', $attr_pair, 3);
                        if (count($attr_parts) === 3) {
                            list($attr_name, $attr_value, $attr_value_id) = $attr_parts;
                            foreach ($variant_attributes as $attr_def) {
                                if ($attr_def['name'] === $attr_name) {
                                    $attributes[$attr_def['id']] = ['value' => $attr_value, 'value_id' => $attr_value_id];
                                    break;
                                }
                            }
                        }
                    }
                }

                $variants[] = [
                    'id' => $variant_id,
                    'sku' => $sku,
                    'stock_quantity' => $stock_quantity,
                    'price' => $price,
                    'image_url' => $image_url,
                    'status' => $status,
                    'attributes' => $attributes
                ];
            }
        }
        $edit_product['variants'] = $variants;
        unset($edit_product['variants_data']);
    }
}

require_once 'admin_header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản phẩm | Anh Em Rọt Store</title>
    <link rel="icon" href="../assets/logo/logo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* General Body and Container */
        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
            background-color: var(--bg-secondary);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        /* Header Styles */
        .main-header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            margin: -20px -20px 20px -20px; /* Adjust to fit card padding */
            text-align: center;
        }

        .main-header h1 {
            margin: 0;
            font-size: 2em;
        }

        .main-header p {
            margin: 5px 0 0;
            opacity: 0.9;
        }

        /* Card Styles */
        .card {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--box-shadow);
        }

        /* Form Group Styles */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--text-secondary);
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            background-color: black;
            color: white;
            box-sizing: border-box; /* Ensures padding doesn't increase width */
        }

        .form-group input[type="file"] {
            padding: 5px 0;
        }

        .form-group textarea {
            resize: vertical; /* Allow vertical resizing */
        }

        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-check input[type="checkbox"] {
            margin-right: 10px;
            width: auto;
        }

        .form-check label {
            margin-bottom: 0;
            font-weight: normal;
        }

        /* Button Styles */
        .btn-primary, .btn-secondary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none; /* For anchor tags styled as buttons */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade of primary */
        }

        .btn-secondary {
            background-color: var(--bg-tertiary); /* Lighter background for secondary */
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #ddd;
        }

        .btn-primary i, .btn-secondary i {
            margin-right: 8px;
        }

        /* Variant Specific Styles */
        .variant-group {
            border: 1px dashed var(--border-color); /* Changed to dashed for visual distinction */
            padding: 15px;
            margin-bottom: 15px;
            border-radius: var(--border-radius);
            background-color: var(--bg-light); /* Slightly lighter background for variants */
        }

        .variant-group h4 {
            margin-top: 0;
            color: var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .variant-group .form-group {
            margin-bottom: 10px;
        }

        .add-variant-btn, .remove-variant-btn {
            background-color: var(--accent-color); /* Greenish for add */
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .remove-variant-btn {
            background-color: var(--error-color); /* Red for remove */
            margin-left: 10px;
        }

        .add-variant-btn:hover {
            background-color: #218838; /* Darker green */
        }

        .remove-variant-btn:hover {
            background-color: #c82333; /* Darker red */
        }

        .add-variant-btn i, .remove-variant-btn i {
            margin-right: 5px;
        }

        .variant-attributes-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Adjusted min-width for better display */
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .variant-attributes-section .form-group {
            margin-bottom: 0;
        }

        /* Image Upload and Preview */
        .image-upload-area {
            display: flex;
            align-items: flex-end; /* Align items to the bottom */
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .image-upload-area label {
            margin-bottom: 0;
        }

        .image-upload-area input[type="file"] {
            flex-grow: 1;
            min-width: 150px; /* Ensure file input is not too small */
        }

        .image-upload-area .form-check {
            margin-left: 10px;
            margin-bottom: 0; /* Remove bottom margin for alignment */
        }

        .current-image-preview, #current-variant-image-preview img {
            max-width: 120px; /* Increased preview size */
            max-height: 120px;
            margin-top: 0; /* Aligned with inputs */
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            padding: 3px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .current-image-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Ensure image fits without cropping */
        }


        /* Filter and Sort Section */
        .filter-sort-section {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: flex-end; /* Align buttons and inputs */
        }

        .filter-sort-section .form-group {
            margin-bottom: 0;
            flex: 1;
            min-width: 180px; /* Slightly wider minimum for better input display */
        }

        .filter-sort-section button,
        .filter-sort-section .btn-secondary {
            padding: 10px 15px;
            border-radius: var(--border-radius);
            border: none;
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            flex-grow: 0; /* Prevent buttons from growing */
            display: inline-flex;
            align-items: center;
        }

        .filter-sort-section button:hover {
            background-color: #0056b3;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto; /* Enable horizontal scroll on small screens */
            margin-top: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: var(--bg-secondary);
        }

        .data-table th, .data-table td {
            border: 1px solid var(--border-color);
            padding: 12px;
            text-align: left;
            vertical-align: top;
        }

        .data-table th {
            background-color: var(--bg-tertiary);
            font-weight: bold;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-size: 0.9em;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: var(--bg-light); /* Subtle striping */
        }

        .data-table tbody tr:hover {
            background-color: var(--hover-color);
        }

        /* Status Badges */
        .status-active {
            color: var(--success-color);
            font-weight: bold;
            background-color: #e6ffe6; /* Light green background */
            padding: 4px 8px;
            border-radius: 4px;
        }

        .status-inactive {
            color: var(--error-color);
            font-weight: bold;
            background-color: #ffe6e6; /* Light red background */
            padding: 4px 8px;
            border-radius: 4px;
        }

        /* Action Buttons in Table */
        .action-buttons {
            white-space: nowrap; /* Keep buttons on one line */
        }

        .action-btn {
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.85em;
            display: inline-flex;
            align-items: center;
            margin-right: 5px; /* Spacing between buttons */
            transition: background-color 0.3s ease;
        }

        .action-btn i {
            margin-right: 5px;
        }

        .edit-btn {
            background-color: var(--primary-color);
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: var(--error-color);
        }
        .delete-btn:hover {
            background-color: #c82333;
        }

        .restore-btn {
            background-color: var(--accent-color);
        }
        .restore-btn:hover {
            background-color: #218838;
        }

        /* Variant List in Table */
        .data-table ul {
            list-style-type: disc; /* Use discs for clarity */
            padding-left: 20px;
            margin: 0;
        }

        .data-table ul li {
            margin-bottom: 5px;
            border-bottom: 1px dotted var(--border-color);
            padding-bottom: 5px;
        }
        .data-table ul li:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        /* Pagination */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            margin: 0 4px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--primary-color);
            background-color: var(--bg-secondary);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .pagination a.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination a:hover:not(.active) {
            background-color: var(--hover-color);
            color: var(--primary-color);
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            color: white;
            font-weight: bold;
            box-shadow: var(--box-shadow);
            z-index: 1000;
            opacity: 0;
            animation: fadeInOut 5s forwards;
        }

        .notification.success {
            background-color: var(--success-color);
        }

        .notification.error {
            background-color: var(--error-color);
        }

        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-20px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(-20px); }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .filter-sort-section {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-sort-section .form-group {
                min-width: unset; /* Remove min-width to allow full width */
                width: 100%;
            }
            .filter-sort-section button,
            .filter-sort-section .btn-secondary {
                width: 100%;
                margin-top: 10px;
            }
            .data-table th, .data-table td {
                padding: 8px; /* Reduce padding for smaller screens */
                font-size: 0.9em;
            }
            .action-btn {
                margin-bottom: 5px;
                display: block;
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 10px;
            }
            .main-header {
                margin: -10px -10px 15px -10px;
                padding: 15px;
            }
            .main-header h1 {
                font-size: 1.5em;
            }
            .form-group label {
                font-size: 0.9em;
            }
            .form-group input, .form-group select, .form-group textarea {
                font-size: 0.9em;
                padding: 8px;
            }
            .btn-primary, .btn-secondary,
            .add-variant-btn, .remove-variant-btn {
                padding: 8px 12px;
                font-size: 0.9em;
            }
            .current-image-preview img, #current-variant-image-preview img {
                max-width: 80px;
                max-height: 80px;
            }
            .variant-attributes-section {
                grid-template-columns: 1fr; /* Stack attributes on small phones */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>Quản lý Sản phẩm</h1>
            <p>Thêm, sửa hoặc xóa sản phẩm và quản lý các biến thể của chúng.</p>
        </header>

        <div class="form-section card">
            <h2><?php echo $edit_product ? 'Chỉnh sửa Sản phẩm' : 'Thêm Sản phẩm mới'; ?></h2>
            <form id="product-form" action="manage_products.php" method="POST" enctype="multipart/form-data">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($edit_product['id']); ?>">
                    <input type="hidden" name="existing_image_url" id="existing-product-image" value="<?php echo htmlspecialchars($edit_product['product_image'] ?? ''); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">Tên sản phẩm:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($edit_product['product_name'] ?? ''); ?>" placeholder="Nhập tên sản phẩm" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả:</label>
                    <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả sản phẩm" required><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="base_price">Giá cơ bản:</label>
                    <input type="number" id="base_price" name="base_price" value="<?php echo htmlspecialchars($edit_product['base_price'] ?? ''); ?>" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Danh mục:</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['id']); ?>"
                                <?php echo ($edit_product && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product_image">Ảnh sản phẩm:</label>
                    <div class="image-upload-area">
                        <input type="file" id="product_image" name="product_image" accept="image/*">
                        <?php if ($edit_product && $edit_product['product_image']): ?>
                            <div class="current-image-preview">
                                <img src="<?php echo htmlspecialchars($edit_product['product_image']); ?>" alt="Current Product Image" style="width: 50px; height: 50px; object-fit: cover;">
                            </div>
                            <div class="form-check">
                                <input type="checkbox" id="remove-product-image" name="remove_product_image" value="1">
                                <label for="remove-product-image" id="remove-product-image-label">Xóa ảnh hiện tại</label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo ($edit_product === null || $edit_product['is_active']) ? 'checked' : ''; ?>>
                    <label for="is_active">Còn hàng</label>
                </div>

                <h3>Biến thể sản phẩm:</h3>
                <div id="variants-container">
                    <?php if ($edit_product && !empty($edit_product['variants'])): ?>
                        <?php foreach ($edit_product['variants'] as $index => $variant): ?>
                            <div class="variant-group" data-variant-index="<?php echo $index; ?>">
                                <input type="hidden" name="variants[<?php echo $index; ?>][id]" value="<?php echo htmlspecialchars($variant['id']); ?>">
                                <input type="hidden" name="variants[<?php echo $index; ?>][existing_image_url]" value="<?php echo htmlspecialchars($variant['image_url'] ?? ''); ?>">
                                <h4>Biến thể #<span class="variant-number"><?php echo $index + 1; ?></span> <button type="button" class="remove-variant-btn"><i class="fas fa-times"></i> Xóa</button></h4>
                                <div class="form-group">
                                    <label for="variants_<?php echo $index; ?>_sku">SKU:</label>
                                    <input type="text" id="variants_<?php echo $index; ?>_sku" name="variants[<?php echo $index; ?>][sku]" value="<?php echo htmlspecialchars($variant['sku']); ?>" placeholder="SKU biến thể" required>
                                </div>
                                <div class="form-group">
                                    <label for="variants_<?php echo $index; ?>_stock_quantity">Số lượng tồn kho:</label>
                                    <input type="number" id="variants_<?php echo $index; ?>_stock_quantity" name="variants[<?php echo $index; ?>][stock_quantity]" value="<?php echo htmlspecialchars($variant['stock_quantity']); ?>" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="variants_<?php echo $index; ?>_price">Giá biến thể:</label>
                                    <input type="number" id="variants_<?php echo $index; ?>_price" name="variants[<?php echo $index; ?>][price]" value="<?php echo htmlspecialchars($variant['price']); ?>" step="0.01" min="0" required>
                                </div>
                                <div class="form-group">
                                    <label for="variants_<?php echo $index; ?>_image">Ảnh biến thể:</label>
                                    <div class="image-upload-area">
                                        <input type="file" id="variants_<?php echo $index; ?>_image" name="variants_image_<?php echo $index; ?>" accept="image/*">
                                        <?php if ($variant['image_url']): ?>
                                            <div id="current-variant-image-preview_<?php echo $index; ?>">
                                                <img src="<?php echo htmlspecialchars($variant['image_url']); ?>" alt="Current Variant Image" style="max-width: 100px;">
                                            </div>
                                            <div class="form-check">
                                                <input type="checkbox" id="remove-variant-image_<?php echo $index; ?>" name="variants[<?php echo $index; ?>][remove_variant_image]" value="1">
                                                <label for="remove-variant-image_<?php echo $index; ?>" id="remove-variant-image-label_<?php echo $index; ?>">Xóa ảnh hiện tại</label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group form-check">
                                    <input type="checkbox" id="variants_<?php echo $index; ?>_status" name="variants[<?php echo $index; ?>][status]" value="1" <?php echo ($variant['status']) ? 'checked' : ''; ?>>
                                    <label for="variants_<?php echo $index; ?>_status">Còn hàng</label>
                                </div>
                                <div class="variant-attributes-section">
                                    <?php foreach ($variant_attributes as $attr): ?>
                                        <div class="form-group">
                                            <label for="variant_attr_<?php echo $index; ?>_<?php echo $attr['id']; ?>"><?php echo htmlspecialchars($attr['display_name']); ?>:</label>
                                            <select id="variant_attr_<?php echo $index; ?>_<?php echo $attr['id']; ?>"
                                                    name="variants[<?php echo $index; ?>][attributes][<?php echo $attr['id']; ?>]">
                                                <option value="">-- Chọn <?php echo htmlspecialchars($attr['display_name']); ?> --</option>
                                                <?php foreach ($attr['valuess'] as $value): ?>
                                                    <option value="<?php echo htmlspecialchars($value['id']); ?>"
                                                        <?php echo ($variant['attributes'][$attr['id']]['value_id'] ?? '') == $value['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($value['value']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php foreach ($edit_product['variants'] as $variant): ?>
                            <input type="hidden" name="existing_variant_ids[]" value="<?php echo htmlspecialchars($variant['id']); ?>">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="add-variant-btn" class="add-variant-btn"><i class="fas fa-plus"></i> Thêm biến thể</button>

                <button type="submit" class="btn-primary">
                    <i class="fas <?php echo $edit_product ? 'fa-save' : 'fa-plus'; ?>"></i>
                    <?php echo $edit_product ? 'Cập nhật Sản phẩm' : 'Thêm Sản phẩm'; ?>
                </button>
            </form>
        </div>

        <div class="list-section card">
            <h2>Danh sách Sản phẩm</h2>
            <div class="filter-sort-section">
                <form action="manage_products.php" method="GET" class="filter-sort-form" style="display: flex; width: 100%; gap: 15px; flex-wrap: wrap;">
                    <div class="form-group" style="flex: 2; min-width: 200px;">
                        <input type="text" name="search" placeholder="Tìm kiếm theo tên, mô tả, SKU..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 150px;">
                        <select name="category_id">
                            <option value="">Tất cả danh mục</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['id']); ?>" <?php echo ($filter_category_id == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 150px;">
                        <select name="sort_by">
                            <option value="created_at" <?php echo ($sort_by === 'created_at') ? 'selected' : ''; ?>>Mới nhất</option>
                            <option value="name" <?php echo ($sort_by === 'name') ? 'selected' : ''; ?>>Tên (A-Z)</option>
                            <option value="price_asc" <?php echo ($sort_by === 'price_asc') ? 'selected' : ''; ?>>Giá (Thấp - Cao)</option>
                            <option value="price_desc" <?php echo ($sort_by === 'price_desc') ? 'selected' : ''; ?>>Giá (Cao - Thấp)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary" style="flex-grow: 0;"><i class="fas fa-filter"></i> Lọc & Sắp xếp</button>
                    <a href="manage_products.php" class="btn-secondary" style="flex-grow: 0; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <i class="fas fa-sync-alt"></i> Reset
                    </a>
                </form>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Mô tả</th>
                            <th>Danh mục</th>
                            <th>Giá cơ bản</th>
                            <th>Trạng thái</th>
                            <th>Biến thể</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr id="product-row-<?php echo htmlspecialchars($product['id']); ?>">
                                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td>
                                        <?php if (!empty($product['product_image'])): ?>
                                            <img src="<?php echo htmlspecialchars($product['product_image']); ?>" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars(mb_substr($product['description'], 0, 100)) . (mb_strlen($product['description']) > 100 ? '...' : ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo number_format($product['base_price']); ?> VNĐ</td>
                                    <td>
                                        <span class="status-<?php echo $product['status'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $product['status'] ? 'Còn hàng' : 'hết hàng'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($product['variants'])): ?>
                                            <ul style="list-style-type: none; padding: 0;">
                                                <?php foreach ($product['variants'] as $variant): ?>
                                                    <li>
                                                        SKU: <?php echo htmlspecialchars($variant['sku']); ?><br>
                                                        Giá: <?php echo number_format($variant['price']); ?> VNĐ<br>
                                                        Tồn kho: <?php echo htmlspecialchars($variant['stock_quantity']); ?><br>
                                                        Thuộc tính: <?php echo implode(', ', array_map(function($attr){ return htmlspecialchars($attr['name'] . ': ' . $attr['value']); }, $variant['attributes'])); ?><br>
                                                        Trạng thái: <span class="status-<?php echo $variant['status'] ? 'active' : 'inactive'; ?>"><?php echo $variant['status'] ? 'Còn hàng' : 'hết hàng'; ?></span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            Không có biến thể
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="manage_products.php?action=edit&id=<?php echo htmlspecialchars($product['id']); ?>" class="action-btn edit-btn"><i class="fas fa-edit action-icon"></i> Sửa</a>
                                        <?php if ($product['status']): ?>
                                            <a href="manage_products.php?action=delete&id=<?php echo htmlspecialchars($product['id']); ?>" data-id="<?php echo htmlspecialchars($product['id']); ?>" class="action-btn delete-btn ajax-action-btn" data-action="delete" onclick="return confirm('Bạn có chắc chắn muốn đánh dấu sản phẩm này là hết hàng (sẽ không hiển thị trên cửa hàng)?');"><i class="fas fa-times action-icon"></i> Tắt</a>
                                        <?php else: ?>
                                            <a href="manage_products.php?action=restore&id=<?php echo htmlspecialchars($product['id']); ?>" data-id="<?php echo htmlspecialchars($product['id']); ?>" class="action-btn restore-btn ajax-action-btn" data-action="restore" onclick="return confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này (sẽ hiển thị lại trên cửa hàng)?');"><i class="fas fa-undo action-icon"></i> Khôi phục</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="9">Chưa có sản phẩm nào. Hãy thêm một sản phẩm mới!</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
<a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search_query); ?>&sort_by=<?php echo htmlspecialchars($sort_by ?? ''); ?>&category_id=<?php echo htmlspecialchars($filter_category_id ?? ''); ?>"
                        class="<?php echo $i == $current_page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Notification display
            function showNotification(message, type) {
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.textContent = message;
                document.body.appendChild(notification);
                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }

            // Display temp message from session if exists for non-AJAX requests
            <?php if (isset($_SESSION['temp_message'])): ?>
                showNotification('<?php echo htmlspecialchars($_SESSION['temp_message']['content']); ?>', '<?php echo htmlspecialchars($_SESSION['temp_message']['type']); ?>');
                <?php unset($_SESSION['temp_message']); ?>
            <?php endif; ?>

            // Variant management
            const variantsContainer = document.getElementById('variants-container');
            const addVariantBtn = document.getElementById('add-variant-btn');
            let variantIndex = <?php echo ($edit_product && !empty($edit_product['variants'])) ? count($edit_product['variants']) : 0; ?>;

            function htmlspecialchars(str) {
                if (typeof str != 'string') {
                    return str;
                }
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return str.replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            function addVariantGroup(variantData = null) {
                const newVariantGroup = document.createElement('div');
                newVariantGroup.className = 'variant-group';
                newVariantGroup.dataset.variantIndex = variantIndex;
                newVariantGroup.innerHTML = `
                    <input type="hidden" name="variants[${variantIndex}][id]" value="${variantData ? htmlspecialchars(variantData.id) : 0}">
                    <input type="hidden" name="variants[${variantIndex}][existing_image_url]" value="${variantData && variantData.image_url ? htmlspecialchars(variantData.image_url) : ''}">
                    <h4>Biến thể #<span class="variant-number">${variantIndex + 1}</span> <button type="button" class="remove-variant-btn"><i class="fas fa-times"></i> Xóa</button></h4>
                    <div class="form-group">
                        <label for="variants_${variantIndex}_sku">SKU:</label>
                        <input type="text" id="variants_${variantIndex}_sku" name="variants[${variantIndex}][sku]" value="${variantData ? htmlspecialchars(variantData.sku) : ''}" placeholder="SKU biến thể" required>
                    </div>
                    <div class="form-group">
                        <label for="variants_${variantIndex}_stock_quantity">Số lượng tồn kho:</label>
                        <input type="number" id="variants_${variantIndex}_stock_quantity" name="variants[${variantIndex}][stock_quantity]" value="${variantData ? htmlspecialchars(variantData.stock_quantity) : '0'}" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="variants_${variantIndex}_price">Giá biến thể:</label>
                        <input type="number" id="variants_${variantIndex}_price" name="variants[${variantIndex}][price]" value="${variantData ? htmlspecialchars(variantData.price) : '0'}" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="variants_${variantIndex}_image">Ảnh biến thể:</label>
                        <div class="image-upload-area">
                            <input type="file" id="variants_${variantIndex}_image" name="variants_image_${variantIndex}" accept="image/*">
                            ${variantData && variantData.image_url ? `
                                <div id="current-variant-image-preview_${variantIndex}">
                                    <img src="${htmlspecialchars(variantData.image_url)}" style="max-width: 100px;">
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" id="remove-variant-image_${variantIndex}" name="variants[${variantIndex}][remove_variant_image]" value="1">
                                    <label for="remove-variant-image_${variantIndex}" id="remove-variant-image-label_${variantIndex}">Xóa ảnh hiện tại</label>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" id="variants_${variantIndex}_status" name="variants[${variantIndex}][status]" value="1" ${variantData === null || variantData.status ? 'checked' : ''}>
                        <label for="variants_${variantIndex}_status">Còn hàng</label>
                    </div>
                    <div class="variant-attributes-section">
                        <?php foreach ($variant_attributes as $attr): ?>
                            <div class="form-group">
                                <label for="variant_attr_${variantIndex}_<?php echo $attr['id']; ?>"><?php echo htmlspecialchars($attr['display_name']); ?>:</label>
                                <select id="variant_attr_${variantIndex}_<?php echo $attr['id']; ?>"
                                        name="variants[${variantIndex}][attributes][<?php echo $attr['id']; ?>]">
                                    <option value="">-- Chọn <?php echo htmlspecialchars($attr['display_name']); ?> --</option>
                                    <?php foreach ($attr['valuess'] as $value): ?>
                                        <option value="<?php echo htmlspecialchars($value['id']); ?>"
                                            ${variantData && variantData.attributes[<?php echo $attr['id']; ?>]?.value_id == '<?php echo $value['id']; ?>' ? 'selected' : ''}>
                                            <?php echo htmlspecialchars($value['value']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
                `;
                variantsContainer.appendChild(newVariantGroup);

                // Add event listener for remove button
                newVariantGroup.querySelector('.remove-variant-btn').addEventListener('click', function() {
                    newVariantGroup.remove();
                    updateVariantNumbers();
                });

                // Add event listener for variant image upload and remove
                const variantImageInput = newVariantGroup.querySelector(`#variants_${variantIndex}_image`);
                if (variantImageInput) {
                    variantImageInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        const previewContainer = newVariantGroup.querySelector(`#current-variant-image-preview_${variantIndex}`);
                        const removeCheckbox = newVariantGroup.querySelector(`#remove-variant-image_${variantIndex}`);
                        const removeLabel = newVariantGroup.querySelector(`#remove-variant-image-label_${variantIndex}`);

                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                if (previewContainer) previewContainer.innerHTML = `<img src="${e.target.result}" style="max-width: 100px;">`;
                            };
                            reader.readAsDataURL(file);
                            if (removeCheckbox) {
                                removeCheckbox.checked = false;
                                removeCheckbox.style.display = 'none';
                                if (removeLabel) removeLabel.style.display = 'none';
                            }
                        } else {
                            if (previewContainer) previewContainer.innerHTML = '';
                            if (removeCheckbox) {
                                if (variantData && variantData.image_url) {
                                    removeCheckbox.style.display = 'inline-block';
                                    if (removeLabel) removeLabel.style.display = 'inline-block';
                                } else {
                                    removeCheckbox.style.display = 'none';
                                    if (removeLabel) removeLabel.style.display = 'none';
                                }
                            }
                        }
                    });
                }

                const removeVariantImageCheckbox = newVariantGroup.querySelector(`#remove-variant-image_${variantIndex}`);
                if (removeVariantImageCheckbox) {
                    removeVariantImageCheckbox.addEventListener('change', function() {
                        const previewContainer = newVariantGroup.querySelector(`#current-variant-image-preview_${variantIndex}`);
                        if (this.checked) {
                            if (previewContainer) previewContainer.innerHTML = '';
                        } else {
                            if (variantData && variantData.image_url && previewContainer) {
                                previewContainer.innerHTML = `<img src="${htmlspecialchars(variantData.image_url)}" style="max-width: 100px;">`;
                            }
                        }
                    });
                }
                variantIndex++;
            }

            function updateVariantNumbers() {
                document.querySelectorAll('.variant-group').forEach((group, index) => {
                    group.querySelector('.variant-number').textContent = index + 1;
                });
            }

            addVariantBtn.addEventListener('click', () => addVariantGroup());

            // Initial product image preview and remove checkbox logic
            const productImageInput = document.getElementById('product_image');
            if (productImageInput) {
                productImageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const previewContainer = document.querySelector('.current-image-preview');
                    const removeCheckbox = document.getElementById('remove-product-image');
                    const removeLabel = document.getElementById('remove-product-image-label');

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (previewContainer) previewContainer.innerHTML = `<img src="${e.target.result}" style="max-width: 100px;">`;
                        };
                        reader.readAsDataURL(file);
                        if (removeCheckbox) {
                            removeCheckbox.checked = false;
                            removeCheckbox.style.display = 'none';
                            if (removeLabel) removeLabel.style.display = 'none';
                        }
                    } else {
                        if (previewContainer) previewContainer.innerHTML = '';
                        const existingProductImage = document.getElementById('existing-product-image').value;
                        if (existingProductImage && previewContainer) {
                            previewContainer.innerHTML = `<img src="${htmlspecialchars(existingProductImage)}" style="max-width: 100px;">`;
                        }
                        if (removeCheckbox) {
                            if (existingProductImage) {
                                removeCheckbox.style.display = 'inline-block';
                                if (removeLabel) removeLabel.style.display = 'inline-block';
                            } else {
                                removeCheckbox.style.display = 'none';
                                if (removeLabel) removeLabel.style.display = 'none';
                            }
                        }
                    }
                });
            }

            const removeProductImageCheckbox = document.getElementById('remove-product-image');
            if (removeProductImageCheckbox) {
                removeProductImageCheckbox.addEventListener('change', function() {
                    const previewContainer = document.querySelector('.current-image-preview');
                    const existingProductImage = document.getElementById('existing-product-image').value;
                    if (this.checked) {
                        if (previewContainer) previewContainer.innerHTML = '';
                    } else {
                        if (existingProductImage && previewContainer) {
                            previewContainer.innerHTML = `<img src="${htmlspecialchars(existingProductImage)}" style="max-width: 100px;">`;
                        }
                    }
                });
            }

            // Initial load check for product image and remove checkbox display
            const initialExistingProductImage = document.getElementById('existing-product-image');
            if (initialExistingProductImage && initialExistingProductImage.value) {
                if (removeProductImageCheckbox) removeProductImageCheckbox.style.display = 'inline-block';
                const removeProductImageLabel = document.getElementById('remove-product-image-label');
                if (removeProductImageLabel) removeProductImageLabel.style.display = 'inline-block';
            } else {
                if (removeProductImageCheckbox) removeProductImageCheckbox.style.display = 'none';
                const removeProductImageLabel = document.getElementById('remove-product-image-label');
                if (removeProductImageLabel) removeProductImageLabel.style.display = 'none';
            }

            // Handle remove buttons for pre-existing variants
            document.querySelectorAll('.remove-variant-btn').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.variant-group').remove();
                    updateVariantNumbers();
                });
            });

            // AJAX form submission for product add/edit
            $('#product-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const form = $(this);
                const formData = new FormData(this); // Use FormData for file uploads

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false, // Don't process the data
                    contentType: false, // Don't set content type (FormData does it)
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }, // Indicate AJAX request
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.message, 'success');
                            // If product was edited, update its row in the table
                            if (formData.get('product_id') > 0) {
                                // For simplicity, we can reload the page to show updated data
                                // Or, implement more complex DOM manipulation to update the row
                                location.reload(); 
                            } else {
                                // For new product, reset form and potentially refresh table
                                form[0].reset(); // Reset the form
                                $('#variants-container').empty(); // Clear variants
                                variantIndex = 0; // Reset variant index
                                $('.current-image-preview').empty(); // Clear product image preview
                                $('#remove-product-image').prop('checked', false).hide();
                                $('#remove-product-image-label').hide();
                                // Consider re-fetching product list via AJAX or refreshing page
                                location.reload(); // Simple reload for now to show new product
                            }
                        } else {
                            let errorMessage = response.errors ? response.errors.join('<br>') : 'Có lỗi xảy ra.';
                            showNotification(errorMessage, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error, xhr.responseText);
                        showNotification('Lỗi kết nối hoặc lỗi server.', 'error');
                    }
                });
            });

            // AJAX for delete/restore actions
            $(document).on('click', '.ajax-action-btn', function(e) {
                e.preventDefault(); // Prevent default link behavior

                const btn = $(this);
                const productId = btn.data('id');
                const action = btn.data('action');
                const confirmMessage = btn.attr('onclick'); // Get original confirm message

                // Re-run the confirmation
                if (confirmMessage && !eval(confirmMessage.replace('return ', ''))) {
                    return; // If user cancels, stop
                }

                $.ajax({
                    url: `manage_products.php?action=${action}&id=${productId}`,
                    type: 'GET', // Delete/restore actions are typically GET
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.message, 'success');
                            const productRow = $(`#product-row-${response.product_id}`);
                            const statusSpan = productRow.find('.status-active, .status-inactive');
                            const actionButtons = productRow.find('.action-buttons');

                            if (response.new_status === 0) { // Product deactivated/deleted
                                statusSpan.removeClass('status-active').addClass('status-inactive').text('hết hàng');
                                actionButtons.html(`
                                    <a href="manage_products.php?action=edit&id=${response.product_id}" class="action-btn edit-btn"><i class="fas fa-edit action-icon"></i> Sửa</a>
                                    <a href="manage_products.php?action=restore&id=${response.product_id}" data-id="${response.product_id}" class="action-btn restore-btn ajax-action-btn" data-action="restore" onclick="return confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này (sẽ hiển thị lại trên cửa hàng)?');"><i class="fas fa-undo action-icon"></i> Khôi phục</a>
                                `);
                            } else { // Product restored/activated
                                statusSpan.removeClass('status-inactive').addClass('status-active').text('Còn hàng');
                                actionButtons.html(`
                                    <a href="manage_products.php?action=edit&id=${response.product_id}" class="action-btn edit-btn"><i class="fas fa-edit action-icon"></i> Sửa</a>
                                    <a href="manage_products.php?action=delete&id=${response.product_id}" data-id="${response.product_id}" class="action-btn delete-btn ajax-action-btn" data-action="delete" onclick="return confirm('Bạn có chắc chắn muốn đánh dấu sản phẩm này là hết hàng (sẽ không hiển thị trên cửa hàng)?');"><i class="fas fa-times action-icon"></i> Tắt</a>
                                `);
                            }
                        } else {
                            showNotification(response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error: ", status, error, xhr.responseText);
                        showNotification('Lỗi kết nối hoặc lỗi server khi thực hiện hành động.', 'error');
                    }
                });
            });
        });

        <?php ob_end_flush(); ?>
    </script>
</body>
</html>