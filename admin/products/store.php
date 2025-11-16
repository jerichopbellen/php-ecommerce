<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

// Input sanitization
$name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
$brand = filter_var($_POST['brand'] ?? '', FILTER_VALIDATE_INT);
$category = filter_var($_POST['category'] ?? '', FILTER_VALIDATE_INT);
$length = filter_var($_POST['length'] ?? '', FILTER_VALIDATE_FLOAT);
$width = filter_var($_POST['width'] ?? '', FILTER_VALIDATE_FLOAT);
$height = filter_var($_POST['height'] ?? '', FILTER_VALIDATE_FLOAT);

// Validate required fields
if (empty($name) || !$brand || !$category || $length === false || $width === false || $height === false) {
    $_SESSION['error'] = 'Invalid input. Please check all fields.';
    header("Location: create.php");
    exit;
}

// Construct dimension string
$dimension = $length . ' x ' . $width . ' x ' . $height . ' cm';

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert into database using prepared statement
    $sql = "INSERT INTO products (name, description, brand_id, category_id, dimension) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssiis', $name, $description, $brand, $category, $dimension);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to insert product');
    }
    
    mysqli_stmt_close($stmt);
    
    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['success'] = "Product added successfully.";
    header("Location: index.php");
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    
    $_SESSION['error'] = 'Failed to create product. Please try again.';
    header("Location: create.php");
    exit;
}