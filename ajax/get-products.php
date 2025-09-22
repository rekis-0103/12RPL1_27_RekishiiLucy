<?php
// ajax/get-products.php
header('Content-Type: application/json');
require_once '../connect/koneksi.php';

if (!isset($_GET['category'])) {
    echo json_encode(['success' => false, 'error' => 'Category parameter required']);
    exit;
}

$category_key = mysqli_real_escape_string($conn, $_GET['category']);

// Get category info
$category_query = "SELECT * FROM product_categories WHERE category_key = '$category_key'";
$category_result = mysqli_query($conn, $category_query);

if (!$category_result || mysqli_num_rows($category_result) == 0) {
    echo json_encode(['success' => false, 'error' => 'Category not found']);
    exit;
}

$category = mysqli_fetch_assoc($category_result);

// Get products in this category
$products_query = "
    SELECT p.*, pc.category_name, pc.category_key 
    FROM products p 
    JOIN product_categories pc ON p.category_id = pc.category_id 
    WHERE pc.category_key = '$category_key' 
    AND p.status = 'active'
    ORDER BY p.name ASC
";

$products_result = mysqli_query($conn, $products_query);

if (!$products_result) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

$products = [];
while ($product = mysqli_fetch_assoc($products_result)) {
    $products[] = $product;
}

echo json_encode([
    'success' => true,
    'category' => $category,
    'products' => $products
]);
?>