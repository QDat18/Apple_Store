<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/Apple_Shop/config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (strlen($query) >= 2) {
    $stmt = $conn->prepare("SELECT id, name, image, price FROM products WHERE name LIKE ?");
    $likeQuery = "%$query%";
    $stmt->bind_param("s", $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}

echo json_encode($results);
?>