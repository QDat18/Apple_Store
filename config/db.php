<?php
$host = 'localhost';
$dbname = 'apple_store';
$username = 'root';
$password = '';

// Suppress errors in browser, log to file
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/Apple_Shop/error.log');

// Prevent premature output
ob_start();

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL']);
        exit;
    }
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    error_log("Exception in db.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL']);
    exit;
}
?>