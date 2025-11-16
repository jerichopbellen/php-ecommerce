<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

    include '../../includes/config.php';

    $name =  trim($_POST['category_name']);

    $check_sql = "SELECT category_id FROM categories WHERE name = '{$name}'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Category name already exists.";
        header("Location: index.php");
        exit;
    }
    // print_r($_POST);
    $result = mysqli_query($conn, " UPDATE categories SET name='{$_POST['category_name']}' WHERE category_id = {$_POST['category_id']}");
    // var_dump($result);
    if ($result) {
        $_SESSION['success'] = "Category updated successfully.";
        header("Location: index.php");
        exit;
    }
    else {
        $_SESSION['error'] = "Update failed.";
        header("Location: edit.php?id={$_POST['category_id']}");
        exit;
    }
    
?>