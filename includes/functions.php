<?php
// includes/functions.php

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Hàm để ghi nhật ký hành động vào bảng `logs` - ĐÃ ĐIỀU CHỈNH CHO MYSQLI
function log_action($user_id, $action, $details = null) {
    global $conn; // Sử dụng biến kết nối MySQLi toàn cục

    // Kiểm tra xem $conn có phải là đối tượng mysqli hợp lệ không
    if (!($conn instanceof mysqli)) {
        error_log("Error: Database connection is not a valid mysqli object in log_action.");
        return;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, ?, ?)");
        // 'iss' nghĩa là: i (integer) cho user_id, s (string) cho action, s (string) cho details
        $stmt->bind_param('iss', $user_id, $action, $details);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        // Ghi lỗi vào log file của server thay vì hiển thị ra màn hình công khai
        error_log("Error logging action: " . $e->getMessage());
    }
}
?>