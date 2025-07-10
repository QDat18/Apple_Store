<?php
$host = 'localhost';
$user = 'root';
$pass = '26a4040725Dat@';
$dbname = 'apple_store';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>