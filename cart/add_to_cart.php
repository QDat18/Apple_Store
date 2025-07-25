<?php
session_start();
include('../config/db.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $variant_id = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($variant_id <= 0 || $quantity <= 0) {
        $response['message'] = 'Dữ liệu không hợp lệ.';
        echo json_encode($response);
        exit();
    }

    // Kiểm tra biến thể sản phẩm có tồn tại và còn hàng không
    $stmt = $conn->prepare("
        SELECT pv.stock_quantity, pv.variant_price, p.category_id 
        FROM product_variants pv 
        JOIN products p ON pv.product_id = p.id 
        WHERE pv.id = ? AND pv.status = 1
    ");
    if (!$stmt) {
        $response['message'] = 'Lỗi truy vấn: ' . $conn->error;
        echo json_encode($response);
        exit();
    }
    $stmt->bind_param("i", $variant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $variant = $result->fetch_assoc();
    $stmt->close();

    if (!$variant) {
        $response['message'] = 'Biến thể sản phẩm không tồn tại hoặc không có sẵn.';
        echo json_encode($response);
        exit();
    }

    $stock_quantity = $variant['stock_quantity'];
    $variant_price = in_array($variant['category_id'], [1, 2]) ? $variant['variant_price'] * 0.9 : $variant['variant_price'];

    if ($quantity > $stock_quantity) {
        $response['message'] = 'Số lượng yêu cầu vượt quá số lượng tồn kho. Chỉ còn ' . $stock_quantity . ' sản phẩm.';
        echo json_encode($response);
        exit();
    }

    if ($user_id) {
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND variant_id = ?");
        $stmt->bind_param("ii", $user_id, $variant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_item = $result->fetch_assoc();
        $stmt->close();

        if ($cart_item) {
            $new_quantity = $quantity; // Sửa: chỉ lấy số lượng mới, không cộng dồn
            if ($new_quantity > $stock_quantity) {
                $response['message'] = 'Số lượng yêu cầu vượt quá số lượng tồn kho. Chỉ còn ' . $stock_quantity . ' sản phẩm.';
                echo json_encode($response);
                exit();
            }
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Cập nhật số lượng sản phẩm trong giỏ hàng thành công.';
            } else {
                $response['message'] = 'Lỗi khi cập nhật giỏ hàng: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, variant_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $variant_id, $quantity);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Thêm sản phẩm vào giỏ hàng thành công.';
            } else {
                $response['message'] = 'Lỗi khi thêm vào giỏ hàng: ' . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$variant_id])) {
            $new_quantity = $_SESSION['cart'][$variant_id]['quantity'] + $quantity;
            if ($new_quantity > $stock_quantity) {
                $response['message'] = 'Số lượng yêu cầu vượt quá số lượng tồn kho. Chỉ còn ' . $stock_quantity . ' sản phẩm.';
                echo json_encode($response);
                exit();
            }
            $_SESSION['cart'][$variant_id]['quantity'] = $new_quantity;
        } else {
            $_SESSION['cart'][$variant_id] = [
                'variant_id' => $variant_id,
                'quantity' => $quantity,
                'variant_price' => $variant_price
            ];
        }
        $response['success'] = true;
        $response['message'] = 'Thêm sản phẩm vào giỏ hàng (session) thành công.';
    }
} else {
    $response['message'] = 'Phương thức yêu cầu không hợp lệ.';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>