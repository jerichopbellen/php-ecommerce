<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

// Sanitize and validate inputs
$product_id = intval($_POST['product_id'] ?? 0);
$name = trim($_POST['productName'] ?? '');
$description = trim($_POST['description'] ?? '');
$brand_id = intval($_POST['brand_id'] ?? 0);
$category_id = intval($_POST['category_id'] ?? 0);

$length = floatval($_POST['length'] ?? 0);
$width  = floatval($_POST['width'] ?? 0);
$height = floatval($_POST['height'] ?? 0);
$dimension = "{$length} x {$width} x {$height} cm";

// Update query using prepared statement
$sql = "UPDATE products 
        SET name = ?, description = ?, brand_id = ?, category_id = ?, dimension = ?
        WHERE product_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssiisi', $name, $description, $brand_id, $category_id, $dimension, $product_id);
$result = mysqli_stmt_execute($stmt);

if ($result) {
    $_SESSION['success'] = "Product updated successfully.";
    header("Location: index.php");
    exit;
} else {
    $_SESSION['error'] = "Update failed.";
    header("Location: edit.php?id={$product_id}");
    exit;
}