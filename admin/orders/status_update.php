<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$order_id = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['status'])) {
    header("Location: view.php?id=$order_id");
    exit;
}

$newStatus = mysqli_real_escape_string($conn, $_POST['status']);
$allowed = ['pending', 'processing', 'shipped', 'delivered', 'received', 'cancelled'];
if (!in_array($newStatus, $allowed)) {
    header("Location: view.php?id=$order_id&msg=invalid_status");
    exit;
}

function redirect_with_msg($order_id, $msg) {
    header("Location: view.php?id=$order_id&msg=$msg");
    exit;
}

function generateTrackingNumber($conn) {
    $prefix = 'ORD-' . date('Ymd') . '-';
    do {
        $suffix = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $tracking_number = $prefix . $suffix;
        $check_sql = "SELECT 1 FROM orders WHERE tracking_number = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $tracking_number);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
    } while (mysqli_stmt_num_rows($check_stmt) > 0);
    return $tracking_number;
}

// Fetch current status
$res = mysqli_query($conn, "SELECT status FROM orders WHERE order_id = $order_id");
if (!$res || mysqli_num_rows($res) === 0) {
    redirect_with_msg($order_id, 'order_not_found');
}
$currentStatus = strtolower(mysqli_fetch_assoc($res)['status']);

// Define transition that requires stock deduction
$requiresStockDeduction = ($currentStatus === 'processing' && $newStatus === 'shipped');

// If no stock deduction needed, just update
if (!$requiresStockDeduction) {
    mysqli_query($conn, "UPDATE orders SET status = '$newStatus' WHERE order_id = $order_id");
    $_SESSION['success'] = "Order status updated successfully.";   
    redirect_with_msg($order_id, 'status_updated');
}

// Begin stock deduction transaction
mysqli_begin_transaction($conn);

$items = mysqli_query($conn, "
    SELECT variant_id, quantity 
    FROM view_order_transaction_details 
    WHERE order_id = $order_id
");

if (!$items || mysqli_num_rows($items) === 0) {
    mysqli_rollback($conn);
    redirect_with_msg($order_id, 'no_items');
}

$ok = true;
while ($row = mysqli_fetch_assoc($items)) {
    $vid = (int) $row['variant_id'];
    $qty = (int) $row['quantity'];
    if ($vid <= 0 || $qty <= 0) {
        $ok = false;
        break;
    }

    $q = mysqli_query($conn, "
        UPDATE stocks 
        SET quantity = quantity - $qty 
        WHERE variant_id = $vid AND quantity >= $qty
    ");

    if (!$q || mysqli_affected_rows($conn) === 0) {
        $ok = false;
        break;
    }
}

if ($ok) {
    // Generate tracking number and update order
    $tracking_number = generateTrackingNumber($conn);
    $update_sql = "
        UPDATE orders 
        SET status = '$newStatus', tracking_number = '$tracking_number' 
        WHERE order_id = $order_id
    ";
    mysqli_query($conn, $update_sql);
    mysqli_commit($conn);
    $_SESSION['success'] = "Order status updated successfully. Stock deducted.";   
    redirect_with_msg($order_id, 'shipped_and_stock_deducted');
}

mysqli_rollback($conn);
redirect_with_msg($order_id, 'stock_deduction_failed');