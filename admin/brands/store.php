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
    $name =  trim($_POST['name']);

     // Check if brand name already exists
    $check_sql = "SELECT brand_id FROM brands WHERE name = '{$name}'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Brand name already exists.";
        header("Location: index.php");
        exit;
    }

    $sql = "INSERT INTO brands (name) VALUES('{$name}')";
    $result = mysqli_query($conn, $sql);

    if($result) {
        $_SESSION['success'] = "Brand added successfully.";
        header("Location: index.php");
        exit;
    }else {
        $_SESSION['error'] = "Failed to add brand. Please try again.";
        header("Location: create.php");
        exit;
    }
}