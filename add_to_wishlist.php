<?php
session_start();
require_once 'config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thêm vào wishlist']);
    exit;
}
$user_id = $_SESSION['user_id'];
$variant_id = isset($_POST['variant_id']) ? (int)$_POST['variant_id'] : 0;
if (!$variant_id) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin sản phẩm']);
    exit;
}
// Kiểm tra đã có trong wishlist chưa
$stmt = $conn->prepare("SELECT id FROM wishlists WHERE user_id = ? AND variant_id = ?");
$stmt->bind_param("ii", $user_id, $variant_id);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Sản phẩm đã có trong wishlist']);
    exit;
}
$stmt->close();
// Thêm mới
$stmt = $conn->prepare("INSERT INTO wishlists (user_id, variant_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $variant_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Đã thêm vào wishlist!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm vào wishlist']);
}
$stmt->close();
$conn->close();
?>