<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

if (isset($_POST['submit'])) {
    // Input sanitization
    $color = trim($_POST['color']);
    $material = trim($_POST['material']);
    $price = filter_var(trim($_POST['price']), FILTER_VALIDATE_FLOAT);
    $quantity = filter_var(trim($_POST['quantity']), FILTER_VALIDATE_INT);
    $product_id = filter_var(trim($_POST['product']), FILTER_VALIDATE_INT);

    // Validation
    if (empty($color) && empty($material)) {
        $_SESSION['error'] = 'Please input at least a color or a material.';
        header("Location: create.php");
        exit();
    }

    if ($price === false || $price < 0) {
        $_SESSION['error'] = 'Invalid price value.';
        header("Location: create.php");
        exit();
    }

    if ($quantity === false || $quantity < 0) {
        $_SESSION['error'] = 'Invalid quantity value.';
        header("Location: create.php");
        exit();
    }

    if ($product_id === false) {
        $_SESSION['error'] = 'Invalid product selected.';
        header("Location: create.php");
        exit();
    }

    // Start transaction
    mysqli_begin_transaction($conn);

    try {
        // Prepared statement for product_variants
        $stmt1 = mysqli_prepare($conn, "INSERT INTO product_variants (color, material, price, product_id) VALUES(?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt1, "ssdi", $color, $material, $price, $product_id);
        $result = mysqli_stmt_execute($stmt1);

        if (!$result) {
            throw new Exception("Failed to insert product variant");
        }

        $variant_id = mysqli_insert_id($conn);

        // Prepared statement for stocks
        $stmt2 = mysqli_prepare($conn, "INSERT INTO stocks (quantity, variant_id) VALUES(?, ?)");
        mysqli_stmt_bind_param($stmt2, "ii", $quantity, $variant_id);
        $result2 = mysqli_stmt_execute($stmt2);

        if (!$result2) {
            throw new Exception("Failed to insert stock");
        }

        // Commit transaction
        mysqli_commit($conn);

        mysqli_stmt_close($stmt1);
        mysqli_stmt_close($stmt2);

        $_SESSION['success'] = "Product variant added successfully.";
        header("Location: index.php");
        exit;

    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        
        $_SESSION['error'] = "Failed to add product variant. Please try again.";
        header("Location: create.php");
        exit;
    }
}