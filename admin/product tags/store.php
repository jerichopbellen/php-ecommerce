<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$product_id = intval($_POST['product_id']);
$tag_id = intval($_POST['tag_id']);

if ($product_id && $tag_id) {
    $result = mysqli_query($conn, "INSERT IGNORE INTO product_tags (product_id, tag_id) VALUES ($product_id, $tag_id)");
}

if ($result) {
    $_SESSION['success'] = "Product tag added successfully.";
    header("Location: index.php");
    exit;
}
else {
    $_SESSION['error'] = "Failed to add product tag. Please try again.";
    header("Location: create.php");
    exit;
}
?>