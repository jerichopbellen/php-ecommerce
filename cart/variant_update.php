<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    die('User not authenticated.');
}

$user_id = $_SESSION['user_id'];

if (!empty($_POST['variant_id'])) {
    foreach ($_POST['variant_id'] as $cart_item_id => $variant_id) {
        $variant_id = intval($variant_id);
        $sql = "UPDATE cart_items SET variant_id = ? WHERE cart_item_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $variant_id, $cart_item_id, $user_id);
        mysqli_stmt_execute($stmt);
    }
}

header('Location: view_cart.php');
exit;
?>
