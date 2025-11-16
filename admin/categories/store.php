<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}
include '../../includes/config.php';

$_SESSION['name'] = trim($_POST['name']);

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);

    // Check if category name already exists
    $check_sql = "SELECT category_id FROM categories WHERE name = '{$name}'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Category name already exists.";
        header("Location: index.php");
        exit;
    }

    $sql = "INSERT INTO categories (name) VALUES('{$name}')";
    $result = mysqli_query($conn, $sql);

    if($result) {
        $_SESSION['success'] = "Category added successfully.";
        header("Location: index.php");
        exit;
    }
    else {
        $_SESSION['error'] = "Failed to add category. Please try again.";
        header("Location: create.php");
        exit;
    }
}