<?php
session_start();
include('../includes/config.php');
include('../includes/mail.php');
include('../includes/order_email_helpers.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to place an order.";
    header("Location: ../user/login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$address_id = filter_input(INPUT_POST, 'address_id', FILTER_SANITIZE_STRING) ?? '';
$payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING) ?? '';

// Validate required fields
if (empty($payment_method)) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Missing required fields.</div></div>";
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Handle new address
    if ($address_id === 'new') {
        $recipient = filter_input(INPUT_POST, 'recipient', FILTER_SANITIZE_STRING) ?? '';
        $street    = filter_input(INPUT_POST, 'street', FILTER_SANITIZE_STRING) ?? '';
        $barangay  = filter_input(INPUT_POST, 'barangay', FILTER_SANITIZE_STRING) ?? '';
        $city      = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING) ?? '';
        $province  = filter_input(INPUT_POST, 'province', FILTER_SANITIZE_STRING) ?? '';
        $zipcode   = filter_input(INPUT_POST, 'zipcode', FILTER_SANITIZE_STRING) ?? '';
        $country   = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING) ?? '';
        $phone     = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING) ?? '';

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
        mysqli_stmt_close($stmt);
    } else {
        $address_id = intval($address_id);
    }

    // Create order
    $status = 'Pending';
    $order_sql = "INSERT INTO orders (user_id, address_id, payment_method, status) VALUES (?, ?, ?, ?)";
    $order_stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($order_stmt, "iiss", $user_id, $address_id, $payment_method, $status);
    mysqli_stmt_execute($order_stmt);
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($order_stmt);

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
        $variant_id = intval($item['variant_id']);
        $quantity = max(1, intval($item['quantity']));
        $price = floatval($item['price']);
        mysqli_stmt_bind_param($order_item_stmt, "iiid", $order_id, $variant_id, $quantity, $price);
        mysqli_stmt_execute($order_item_stmt);
    }
    mysqli_stmt_close($cart_stmt);
    mysqli_stmt_close($order_item_stmt);

    // Clear cart
    $clear_cart_sql = "DELETE FROM cart_items WHERE user_id = ?";
    $clear_cart_stmt = mysqli_prepare($conn, $clear_cart_sql);
    mysqli_stmt_bind_param($clear_cart_stmt, "i", $user_id);
    mysqli_stmt_execute($clear_cart_stmt);
    mysqli_stmt_close($clear_cart_stmt);

    // Commit transaction
    mysqli_commit($conn);

    // Fetch user info with prepared statement
    $user_sql = "SELECT email, first_name FROM users WHERE user_id = ?";
    $user_stmt = mysqli_prepare($conn, $user_sql);
    mysqli_stmt_bind_param($user_stmt, "i", $user_id);
    mysqli_stmt_execute($user_stmt);
    $user_result = mysqli_stmt_get_result($user_stmt);
    $user = mysqli_fetch_assoc($user_result);
    mysqli_stmt_close($user_stmt);
    
    $user_email = $user['email'];
    $user_name  = $user['first_name'];

    // Build email content
    $details = buildOrderDetailsHtml($conn, $order_id);
    $meta    = $details['meta'];
    $items   = $details['html'];
    $address = buildAddressBlock($meta);

    $subject = "Order Confirmation #{$meta['order_id']}";
    $body = "
        <h2>Thank you for your order, " . htmlspecialchars($user_name) . "!</h2>
        <p>Your order <strong>#{$meta['order_id']}</strong> has been placed on " . htmlspecialchars($meta['created_at']) . ".</p>
        <p><strong>Ship to:</strong> {$address}</p>
        <p><strong>Status:</strong> " . htmlspecialchars($meta['status'] ?? 'Pending') . "</p>
        <h3>Order Details</h3>
        {$items}
        <p>We will notify you when the status changes.</p>
    ";

    // Send confirmation email
    sendMail($user_email, $user_name, $subject, $body, $mailConfig);

    // Redirect
    $_SESSION['success'] = 'Order placed successfully.';
    header('Location: ../orders/view_orders.php');
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    $_SESSION['error'] = 'Failed to place order. Please try again.';
    header('Location: ../cart/checkout.php');
    exit();
}
?>