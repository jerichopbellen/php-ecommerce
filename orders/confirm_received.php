<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['order_id'])) {
    header("Location: view_orders.php");
    exit;
}

$order_id = intval($_POST['order_id']);
$user_id = $_SESSION['user_id'];

// Check if order is delivered and belongs to user
$check_sql = "SELECT status FROM orders WHERE order_id = ? AND user_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($check_stmt);
$result = mysqli_stmt_get_result($check_stmt);
$order = mysqli_fetch_assoc($result);

if ($order && $order['status'] === 'Delivered') {
    $update_sql = "UPDATE orders SET status = 'Received' WHERE order_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "i", $order_id);
    mysqli_stmt_execute($update_stmt);
}

header("Location: order_history.php");
exit;