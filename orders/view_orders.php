<?php
session_start();
include('../includes/config.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to view your orders.";
    header("Location: ../user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders
$order_sql = "SELECT * FROM orders WHERE user_id = ? AND status != 'Cancelled' AND status != 'Received' ORDER BY created_at DESC";
$order_stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($order_stmt, "i", $user_id);
mysqli_stmt_execute($order_stmt);
$order_result = mysqli_stmt_get_result($order_stmt);
?>

<div class="container my-5">
    <h2 class="text-center mb-4"><i class="bi bi-box-seam me-2"></i>Your Orders</h2>
    <?php include('../includes/alert.php'); ?>
    <?php if (mysqli_num_rows($order_result) === 0): ?>
        <div class="alert alert-info text-center">You haven't placed any orders yet.</div>
    <?php else: ?>
        <?php while ($order = mysqli_fetch_assoc($order_result)): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Tracking #:</strong> <?= htmlspecialchars($order['tracking_number']) ?><br>
                        <small class="text-muted">Placed on <?= date('F j, Y \a\t H:i', strtotime($order['created_at'])) ?></small>
                    </div>
                    <?php
                    $status = $order['status'];
                    switch ($status) {
                        case 'Pending':
                            $badge_class = 'bg-warning text-dark';
                            break;
                        case 'Processing':
                            $badge_class = 'bg-info text-dark';
                            break;
                        case 'Shipped':
                            $badge_class = 'bg-primary text-white';
                            break;
                        case 'Delivered':
                            $badge_class = 'bg-success text-white';
                            break;
                        case 'Received':
                            $badge_class = 'bg-secondary text-white';
                            break;
                        case 'Cancelled':
                            $badge_class = 'bg-danger text-white';
                            break;
                        default:
                            $badge_class = 'bg-dark text-white';
                    }
                    ?>
                    <span class="badge <?= $badge_class ?>"><?= htmlspecialchars($status) ?></span>
                </div>
                <div class="card-body">
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars(ucwords($order['payment_method'])) ?></p>

                    <?php
                    $order_id = $order['order_id'];
                    $items_sql = "
                        SELECT oi.quantity, oi.price, pv.color, pv.material, p.name AS product_name
                        FROM order_items oi
                        JOIN product_variants pv ON pv.variant_id = oi.variant_id
                        JOIN products p ON p.product_id = pv.product_id
                        WHERE oi.order_id = ?
                    ";
                    $items_stmt = mysqli_prepare($conn, $items_sql);
                    mysqli_stmt_bind_param($items_stmt, "i", $order_id);
                    mysqli_stmt_execute($items_stmt);
                    $items_result = mysqli_stmt_get_result($items_stmt);
                    ?>

                    <table class="table table-bordered text-center mt-3">
                        <thead class="table-secondary">
                            <tr>
                                <th>Product</th>
                                <th>Variant</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            while ($item = mysqli_fetch_assoc($items_result)):
                                $qty = (int)$item['quantity'];
                                $price = (float)$item['price'];
                                $subtotal = $qty * $price;
                                $total += $subtotal;
                                $color = $item['color'];
                                $material = $item['material'];
                                if ($color && $material) {
                                    $variant = htmlspecialchars("$color / $material");
                                } elseif ($color) {
                                    $variant = htmlspecialchars($color);
                                } elseif ($material) {
                                    $variant = htmlspecialchars($material);
                                } else {
                                    $variant = "N/A";
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= htmlspecialchars($variant ?: 'N/A') ?></td>
                                <td><?= $qty ?></td>
                                <td>₱<?= number_format($price, 2) ?></td>
                                <td>₱<?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr class="table-light">
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>₱<?= number_format($total, 2) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-end mt-3">
                        <?php if (in_array($order['status'], ['Pending', 'Processing'])): ?>
                            <form action="cancel_order.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i> Cancel Order
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary me-2" disabled>
                                <i class="bi bi-lock me-1"></i> Cancel
                            </button>
                        <?php endif; ?>

                        <?php if ($order['status'] === 'Delivered'): ?>
                            <form action="confirm_received.php" method="POST" onsubmit="return confirm('Confirm you have received this order?');" class="d-inline">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i> Order Received
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>