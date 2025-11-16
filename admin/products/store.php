<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$brand = trim($_POST['brand'] ?? '');
$category = $_POST['category'] ?? '';
$length = $_POST['length'] ?? '';
$width = $_POST['width'] ?? '';
$height = $_POST['height'] ?? '';

// Construct dimension string
$dimension = floatval($length) . ' x ' . floatval($width) . ' x ' . floatval($height) . ' cm';

// Insert into database using prepared statement
$sql = "INSERT INTO products (name, description, brand_id, category_id, dimension) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ssiis', $name, $description, $brand, $category, $dimension);
$result = mysqli_stmt_execute($stmt);

// Clear session values on success
if ($result) {
    $_SESSION['success'] = "Product added successfully.";
    header("Location: index.php");
    exit;
} else {
    $_SESSION['error'] = 'Failed to create product. Please try again.';
    header("Location: create.php");
    exit;
}