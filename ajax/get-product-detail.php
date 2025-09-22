<?php
// get-product-detail.php
header('Content-Type: application/json; charset=utf-8');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Log all received parameters
$debug_info = [
    'GET_params' => $_GET,
    'POST_params' => $_POST,
    'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
    'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? '',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? ''
];

// Include database connection
require_once __DIR__ . '/../connect/koneksi.php';

// Check if connection exists
if (!isset($conn)) {
    echo json_encode([
        'success' => false, 
        'error' => 'Database connection failed',
        'debug' => $debug_info
    ]);
    exit;
}

// Validate input - check both GET and POST
$product_id = null;

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
} elseif (isset($_POST['id'])) {
    $product_id = $_POST['id'];
} elseif (isset($_GET['key'])) {
    // Fallback for old parameter name
    $product_id = $_GET['key'];
}

if (!$product_id) {
    echo json_encode([
        'success' => false, 
        'error' => 'Product ID required',
        'debug' => $debug_info
    ]);
    exit;
}

$product_id = (int) $product_id;
if ($product_id <= 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Invalid product id: ' . $_GET['id'] ?? $_POST['id'] ?? 'null',
        'debug' => $debug_info
    ]);
    exit;
}

try {
    // Prepared statement to get product detail
    $sql = "
        SELECT
            p.product_id,
            p.name AS name,
            p.description AS description,
            p.image AS image,
            p.category_id,
            p.status,
            p.created_by,
            p.created_at,
            p.updated_at,
            pc.category_name AS category_name,
            pc.category_key AS category_key
        FROM products p
        LEFT JOIN product_categories pc ON p.category_id = pc.category_id
        WHERE p.product_id = ? AND p.status = 'active'
        LIMIT 1
    ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Make sure all fields have values, set defaults for null values
            $product = array(
                'product_id' => $row['product_id'],
                'name' => $row['name'] ?? 'Nama Produk Tidak Tersedia',
                'description' => $row['description'] ?? 'Deskripsi tidak tersedia',
                'image' => $row['image'] ?? '',
                'category_id' => $row['category_id'],
                'category_name' => $row['category_name'] ?? 'Kategori Tidak Tersedia',
                'category_key' => $row['category_key'] ?? '',
                'status' => $row['status'],
                'created_by' => $row['created_by'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            );
            
            echo json_encode(['success' => true, 'product' => $product]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Product not found with ID: ' . $product_id]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database prepare failed: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
exit;
?>