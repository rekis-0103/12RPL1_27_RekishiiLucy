<?php
session_start();
require_once 'connect/koneksi.php';

if (isset($_SESSION['user_id'])) {
    // Log logout activity
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $log_query = "INSERT INTO log_aktivitas (user_id, action) VALUES (?, 'Logout')";
    $log_stmt = mysqli_prepare($conn, $log_query);
    mysqli_stmt_bind_param($log_stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($log_stmt);
    
    // Destroy session
    session_destroy();
}

// Redirect to home page
header('Location: index.php');
exit();
?>
