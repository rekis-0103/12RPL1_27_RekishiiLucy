<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

if (isset($_POST['get_jurusan']) && isset($_POST['id_jenjang'])) {
    $id_jenjang = (int)$_POST['id_jenjang'];
    
    $query = "SELECT id_jurusan, nama_jurusan 
              FROM jurusan_pendidikan 
              WHERE id_jenjang = $id_jenjang AND status = 1 
              ORDER BY nama_jurusan";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $jurusan_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $jurusan_list[] = $row;
        }
        echo json_encode($jurusan_list);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
}
?>