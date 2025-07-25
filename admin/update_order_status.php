<?php
session_start(); // Bắt đầu session để kiểm tra quyền admin và có thể sử dụng thông báo

require_once '../config/db.php'; // Đảm bảo đường dẫn đúng đến file kết nối CSDL

// Kiểm tra quyền truy cập admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error_message'] = 'Bạn không có quyền truy cập chức năng này.';
    header("Location: ../login.php"); // Chuyển hướng về trang đăng nhập nếu không phải admin
    exit;
}

// Kiểm tra nếu yêu cầu là POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy order_id và status từ dữ liệu POST
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $new_status = isset($_POST['status']) ? trim($_POST['status']) : '';

    // Danh sách các trạng thái hợp lệ
    $valid_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];

    // Kiểm tra tính hợp lệ của dữ liệu
    if ($order_id > 0 && in_array($new_status, $valid_statuses)) {
        global $conn; // Sử dụng biến $conn đã được thiết lập bởi db.php
        // Đảm bảo rằng biến $conn đã được thiết lập đúng cách sau khi require '../config/db.php'
        if (!$conn) {
            // Xử lý lỗi nếu kết nối không thành công
            $_SESSION['error_message'] = 'Không thể kết nối đến cơ sở dữ liệu. Vui lòng thử lại sau.';
            error_log("Failed to get database connection in update_order_status.php: \$conn is not set.");
            // Chuyển hướng hoặc dừng thực thi tùy theo luồng ứng dụng của bạn
            if ($order_id > 0) {
                header("Location: view_order.php?id=" . $order_id);
            } else {
                header("Location: manage_orders.php");
            }
            exit;
        }

        // Chuẩn bị câu lệnh SQL để cập nhật trạng thái đơn hàng
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");

        if ($stmt) {
            // Gán tham số và thực thi câu lệnh
            $stmt->bind_param("si", $new_status, $order_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Cập nhật trạng thái đơn hàng thành công.';
            } else {
                $_SESSION['error_message'] = 'Lỗi khi cập nhật trạng thái đơn hàng: ' . $stmt->error;
                error_log("Error updating order status for order_id $order_id: " . $stmt->error);
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error;
            error_log("Error preparing update order status statement: " . $conn->error);
        }
        $conn->close();
    } else {
        $_SESSION['error_message'] = 'Dữ liệu cập nhật không hợp lệ.';
    }
} else {
    $_SESSION['error_message'] = 'Yêu cầu không hợp lệ.';
}

// Chuyển hướng về trang xem chi tiết đơn hàng hoặc trang quản lý đơn hàng
// Bạn có thể chọn chuyển hướng về manage_orders.php nếu muốn
if ($order_id > 0) {
    header("Location: view_order.php?id=" . $order_id);
} else {
    header("Location: manage_orders.php"); // Chuyển hướng đến trang quản lý đơn hàng nếu order_id không hợp lệ
}
exit;
?>