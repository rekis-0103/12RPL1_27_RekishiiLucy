<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has hrd role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'hrd') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$jenjang_id = isset($_GET['jenjang_id']) ? (int)$_GET['jenjang_id'] : 0;

if ($jenjang_id > 0) {
    $query = "SELECT id_jurusan, nama_jurusan 
              FROM jurusan_pendidikan 
              WHERE id_jenjang = $jenjang_id AND status = 1 
              ORDER BY nama_jurusan ASC";
    
    $result = mysqli_query($conn, $query);
    $jurusan_list = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $jurusan_list[] = $row;
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($jurusan_list);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid jenjang_id']);
}
?>