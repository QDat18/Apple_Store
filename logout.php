<?php
session_start();

require_once 'config/db.php';
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $log_stmt = $conn->prepare("INSERT INTO logs (user_id, action, details) VALUES (?, 'logout', ?)");
    $details = "Admin logged out";
    $log_stmt->bind_param("is", $user_id, $details);
    $log_stmt->execute();
    $log_stmt->close();
}

session_unset();
session_destroy();
header("Location: login.php");
exit;
?>