<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to place an order.";
    header("Location: ../user/login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$address_id = $_POST['address_id'] ?? '';
$payment_method = $_POST['payment_method'] ?? '';

// Validate required fields
if (empty($payment_method)) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Missing required fields.</div></div>";
    exit;
}

// Handle new address
if ($address_id === 'new') {
    $recipient = trim($_POST['recipient'] ?? '');
    $street    = trim($_POST['street'] ?? '');
    $barangay  = trim($_POST['barangay'] ?? '');
    $city      = trim($_POST['city'] ?? '');
    $province  = trim($_POST['province'] ?? '');
    $zipcode   = trim($_POST['zipcode'] ?? '');
    $country   = trim($_POST['country'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');


    $insert_address_sql = "
        INSERT INTO addresses 
        (user_id, recipient, street, barangay, city, province, zipcode, country, phone)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = mysqli_prepare($conn, $insert_address_sql);
    mysqli_stmt_bind_param(
        $stmt,
        "issssssss",
        $user_id,
        $recipient,
        $street,
        $barangay,
        $city,
        $province,
        $zipcode,
        $country,
        $phone
    );
    mysqli_stmt_execute($stmt);
    $address_id = mysqli_insert_id($conn);
}

// Create order
$status = 'Pending';
$order_sql = "INSERT INTO orders (user_id, address_id, payment_method, status) VALUES (?, ?, ?, ?)";
$order_stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($order_stmt, "iiss", $user_id, $address_id, $payment_method, $status);
mysqli_stmt_execute($order_stmt);
$order_id = mysqli_insert_id($conn);

// Fetch cart items
$cart_sql = "
    SELECT ci.variant_id, ci.quantity, pv.price
    FROM cart_items ci
    INNER JOIN product_variants pv ON pv.variant_id = ci.variant_id
    WHERE ci.user_id = ?
";
$cart_stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($cart_stmt, "i", $user_id);
mysqli_stmt_execute($cart_stmt);
$cart_result = mysqli_stmt_get_result($cart_stmt);

// Insert order items
$order_item_sql = "INSERT INTO order_items (order_id, variant_id, quantity, price) VALUES (?, ?, ?, ?)";
$order_item_stmt = mysqli_prepare($conn, $order_item_sql);

while ($item = mysqli_fetch_assoc($cart_result)) {
    $variant_id = $item['variant_id'];
    $quantity = max(1, intval($item['quantity']));
    $price = floatval($item['price']);
    mysqli_stmt_bind_param($order_item_stmt, "iiid", $order_id, $variant_id, $quantity, $price);
    mysqli_stmt_execute($order_item_stmt);
}

// Clear cart
$clear_cart_sql = "DELETE FROM cart_items WHERE user_id = ?";
$clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
mysqli_stmt_bind_param($clear_cart_stmt, "i", $user_id);
mysqli_stmt_execute($clear_cart_stmt);


$_SESSION['success'] = 'Order placed successfully.';
header('Location: ../orders/view_orders.php');
exit();
?>