<?php

session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$variant_id = intval($_POST['variant_id']);

// Sanitize inputs
$color = trim($_POST['color'] ?? '');
$material = trim($_POST['material'] ?? '');
$sell_price = filter_var($_POST['sell_price'] ?? 0, FILTER_VALIDATE_FLOAT);
$quantity = filter_var($_POST['quantity'] ?? 0, FILTER_VALIDATE_INT);

if (empty($color) && empty($material)) {
    $_SESSION['error'] = 'Please input at least a color or a material.';
    header("Location: edit.php?id={$variant_id}");
    exit();
}

// Validate numeric inputs
if ($sell_price === false || $quantity === false || $sell_price < 0 || $quantity < 0) {
    $_SESSION['error'] = 'Invalid price or quantity.';
    header("Location: edit.php?id={$variant_id}");
    exit();
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Update product_variants table with prepared statement
    $stmt1 = mysqli_prepare($conn, "UPDATE product_variants SET color=?, material=?, price=? WHERE variant_id=?");
    mysqli_stmt_bind_param($stmt1, "ssdi", $color, $material, $sell_price, $variant_id);
    $result1 = mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    // Update stocks table with prepared statement
    $stmt2 = mysqli_prepare($conn, "UPDATE stocks SET quantity=? WHERE variant_id=?");
    mysqli_stmt_bind_param($stmt2, "ii", $quantity, $variant_id);
    $result2 = mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);

    if ($result1 && $result2) {
        mysqli_commit($conn);
        $_SESSION['success'] = "Product variant updated successfully.";
        header("Location: index.php");
        exit;
    } else {
        throw new Exception("Update failed.");
    }
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = "Update failed.";
    header("Location: edit.php?id={$variant_id}");
    exit;
}

?>