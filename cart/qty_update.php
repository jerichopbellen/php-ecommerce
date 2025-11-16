<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
   echo die( 'User not authenticated.');
}

if (!isset($_POST['cart_item_id'], $_POST['action'], $_POST['product_qty'])) {
    echo die('Missing required data.');
}

$user_id = (int) $_SESSION['user_id'];
$cart_item_id = (int) $_POST['cart_item_id'];
$current_qty = (int) $_POST['product_qty'];
$action = $_POST['action'];

$new_qty = $current_qty; // default fallback

if ($action === 'increase') {
    $new_qty = $current_qty + 1;
} elseif ($action === 'decrease' && $current_qty > 1) {
    $new_qty = $current_qty - 1;
}

// Only update if quantity actually changed
if ($new_qty !== $current_qty) {
    $sql = 'UPDATE cart_items SET quantity = ? WHERE cart_item_id = ? AND user_id = ?';
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'iii', $new_qty, $cart_item_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        die('Database error: ' . mysqli_error($conn));
    }
}

// Redirect back to cart view
header('Location: view_cart.php');
exit;
?>
