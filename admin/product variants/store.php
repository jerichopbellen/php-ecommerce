<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

if (isset($_POST['submit'])) {
    $color =  trim($_POST['color']);
    $material = trim($_POST['material']);
    $price = trim($_POST['price']);
    $quantity = trim($_POST['quantity']);
    $product_id = trim($_POST['product']);

    if (empty($_POST['color']) && empty($_POST['material'])) {
        $_SESSION['error'] = 'Please input at least a color or a material.';
        header("Location: create.php");
        exit();
    }
    $sql = "INSERT INTO product_variants (color, material, price, product_id) VALUES('{$color}', '{$material}', '{$price}', '{$product_id}')";
    $result = mysqli_query($conn, $sql);

    $sql2 = "INSERT INTO stocks (quantity, variant_id) VALUES('{$quantity}', LAST_INSERT_ID())";
    $result2 = mysqli_query($conn, $sql2);

    if($result && $result2) {
        $_SESSION['success'] = "Product variant added successfully.";
        header("Location: index.php");
        exit;
    }
    else {
        $_SESSION['error'] = "Failed to add product variant. Please try again.";
        header("Location: create.php");
        exit;
    }
}