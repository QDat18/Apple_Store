<?php
session_start();
header('Content-Type: application/json');
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập!']);
    exit;
}
$user_id = $_SESSION['user_id'];
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (!$old_password || !$new_password || !$confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin!']);
    exit;
}
if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu mới không khớp!']);
    exit;
}
if (strlen($new_password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự!']);
    exit;
}
// Lấy mật khẩu cũ từ DB
$stmt = $conn->prepare('SELECT password FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($hashed);
if ($stmt->fetch()) {
    if (!password_verify($old_password, $hashed)) {
        echo json_encode(['success' => false, 'message' => 'Mật khẩu cũ không đúng!']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy tài khoản!']);
    exit;
}
$stmt->close();
// Cập nhật mật khẩu mới
$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
$stmt->bind_param('si', $new_hashed, $user_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật mật khẩu!']);
}
$stmt->close(); 