<?php
session_start();
require_once '../connect/koneksi.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get filter parameters
$action_filter = isset($_GET['action']) ? mysqli_real_escape_string($conn, $_GET['action']) : '';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($conn, $_GET['date_to']) : '';

// Build query with filters
$where_conditions = [];
$where_conditions[] = "1=1"; // Base condition

if ($action_filter) {
    $where_conditions[] = "l.action = '$action_filter'";
}

if ($date_from) {
    $where_conditions[] = "DATE(l.log_time) >= '$date_from'";
}

if ($date_to) {
    $where_conditions[] = "DATE(l.log_time) <= '$date_to'";
}

$where_clause = implode(' AND ', $where_conditions);

$logs_query = "SELECT l.*, u.username, u.full_name 
               FROM log_aktivitas l 
               LEFT JOIN users u ON l.user_id = u.user_id 
               WHERE $where_clause
               ORDER BY l.log_time DESC";

$logs_result = mysqli_query($conn, $logs_query);

// Set headers for CSV download
$filename = 'log_aktivitas_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Create file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add CSV headers
fputcsv($output, array('No', 'Waktu', 'Username', 'Nama Lengkap', 'Aksi'));

// Add data rows
$no = 1;
while ($log = mysqli_fetch_assoc($logs_result)) {
    $row = array(
        $no++,
        date('d/m/Y H:i:s', strtotime($log['log_time'])),
        $log['username'] ?: 'Unknown',
        $log['full_name'] ?: 'N/A',
        $log['action']
    );
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
