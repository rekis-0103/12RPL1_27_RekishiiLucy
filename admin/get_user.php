<?php
session_start();
require_once '../connect/koneksi.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit();
}

$user_id = (int)$_GET['id'];

// Get user data
$query = "SELECT user_id, username, email, full_name, role, status FROM users WHERE user_id = $user_id AND hapus = 0";
$result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit();
}

if (mysqli_num_rows($result) == 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

$user = mysqli_fetch_assoc($result);

// Return user data
echo json_encode([
    'success' => true,
    'user' => $user
]);
?>