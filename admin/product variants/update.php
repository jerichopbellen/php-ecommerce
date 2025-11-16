<?php

session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

    include '../../includes/config.php';

    $variant_id = intval($_POST['variant_id']);

    if (empty($_POST['color']) && empty($_POST['material'])) {
        $_SESSION['error'] = 'Please input at least a color or a material.';
        header("Location: edit.php?id={$variant_id}");
        exit();
    }   
    // Update product_variants table
    $result1 = mysqli_query($conn, "UPDATE product_variants SET 
        color='{$_POST['color']}', 
        material='{$_POST['material']}', 
        price='{$_POST['sell_price']}' 
        WHERE variant_id={$variant_id}");

    // Update stocks table
    $result2 = mysqli_query($conn, "UPDATE stocks SET 
        quantity='{$_POST['quantity']}' 
        WHERE variant_id={$variant_id}");

    if ($result1 && $result2) {
        $_SESSION['success'] = "Product variant updated successfully.";
        header("Location: index.php");
        exit;
    }
    else {
        $_SESSION['error'] = "Update failed.";
        header("Location: edit.php?id={$variant_id}");
        exit;
    }
    
?>