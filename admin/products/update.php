<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

// Sanitize and validate inputs
$product_id = filter_var($_POST['product_id'] ?? 0, FILTER_VALIDATE_INT);
$name = htmlspecialchars(trim($_POST['productName'] ?? ''), ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
$brand_id = filter_var($_POST['brand_id'] ?? 0, FILTER_VALIDATE_INT);
$category_id = filter_var($_POST['category_id'] ?? 0, FILTER_VALIDATE_INT);

$length = filter_var($_POST['length'] ?? 0, FILTER_VALIDATE_FLOAT);
$width = filter_var($_POST['width'] ?? 0, FILTER_VALIDATE_FLOAT);
$height = filter_var($_POST['height'] ?? 0, FILTER_VALIDATE_FLOAT);

// Validate required fields
if (!$product_id || empty($name) || !$brand_id || !$category_id) {
    $_SESSION['error'] = "Invalid input data.";
    header("Location: edit.php?id={$product_id}");
    exit;
}

$dimension = "{$length} x {$width} x {$height} cm";

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update query using prepared statement
    $sql = "UPDATE products 
            SET name = ?, description = ?, brand_id = ?, category_id = ?, dimension = ?
            WHERE product_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssiisi', $name, $description, $brand_id, $category_id, $dimension, $product_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Failed to update product.");
    }
    
    mysqli_stmt_close($stmt);
    
    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['success'] = "Product updated successfully.";
    header("Location: index.php");
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    
    $_SESSION['error'] = "Update failed.";
    header("Location: edit.php?id={$product_id}");
    exit;
}