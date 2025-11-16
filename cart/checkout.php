<?php
session_start();
include('../includes/header.php');
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to proceed to checkout.";
    header("Location: ../user/login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

// Fetch cart items with product & variant info
$sql = "
    SELECT 
        ci.cart_item_id,
        ci.quantity,
        pv.variant_id,
        pv.product_id,
        pv.price AS variant_price,
        pv.color,
        pv.material,
        p.name AS product_name
    FROM cart_items ci
    INNER JOIN product_variants pv ON pv.variant_id = ci.variant_id
    INNER JOIN products p ON p.product_id = pv.product_id
    WHERE ci.user_id = ?
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Calculate total
$total = 0;
$cart_items = [];
while ($row = mysqli_fetch_assoc($result)) {
    $qty = max(1, (int)$row['quantity']);
    $subtotal = $row['variant_price'] * $qty;
    $total += $subtotal;
    $cart_items[] = $row;
}

// Fetch saved addresses
$addresses = [];
$address_sql = "SELECT * FROM addresses WHERE user_id = ?";
$address_stmt = mysqli_prepare($conn, $address_sql);
mysqli_stmt_bind_param($address_stmt, "i", $user_id);
mysqli_stmt_execute($address_stmt);
$address_result = mysqli_stmt_get_result($address_stmt);
while ($addr = mysqli_fetch_assoc($address_result)) {
    $addresses[] = $addr;
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4"><i class="bi bi-bag-check me-2"></i>Checkout</h2>

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info text-center">Your cart is empty. <a href="../index.php">Go shopping</a></div>
    <?php else: ?>
        <div class="row g-4">
            <!-- Cart Summary -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3"><i class="bi bi-receipt me-2"></i>Order Summary</h4>
                        <table class="table table-bordered text-center">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): 
                                    $qty = (int)$item['quantity'];
                                    $price = (float)$item['variant_price'];
                                    $subtotal = $price * $qty;
                                    $color = trim($item['color'] ?? '');
                                    $material = trim($item['material'] ?? '');

                                    if ($color && $material) {
                                        $variant_label = htmlspecialchars("$color / $material");
                                    } elseif ($color) {
                                        $variant_label = htmlspecialchars($color);
                                    } elseif ($material) {
                                        $variant_label = htmlspecialchars($material);
                                    } else {
                                        $variant_label = "N/A";
                                    }                                
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                                    <td><?= htmlspecialchars($variant_label ?: 'N/A') ?></td>
                                    <td><?= $qty ?></td>
                                    <td>₱<?= number_format($price, 2) ?></td>
                                    <td>₱<?= number_format($subtotal, 2) ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="table-secondary">
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>₱<?= number_format($total, 2) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Checkout Form -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="mb-3"><i class="bi bi-truck me-2"></i>Shipping & Payment</h4>
                        <form action="place_order.php" method="POST">
                            <div class="mb-3">
                                <label for="address_select" class="form-label">Select Address</label>
                                <select id="address_select" name="address_id" class="form-select" required onchange="toggleNewAddressForm(this)">
                                    <option value="">-- Select saved address --</option>
                                    <?php foreach ($addresses as $addr): ?>
                                        <option value="<?= $addr['address_id'] ?>">
                                            <?= htmlspecialchars(
                                                $addr['recipient'] . ', ' .
                                                $addr['street'] . ', ' .
                                                $addr['barangay'] . ', ' .
                                                $addr['city'] . ', ' .
                                                $addr['province'] . ', ' .
                                                $addr['country'] . ', ' .
                                                $addr['zipcode'] . ', ' .
                                                $addr['phone']
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="new">Add New Address</option>
                                </select>
                            </div>

                            <!-- New Address Fields -->
                            <div id="new_address_form" style="display:none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="recipient" class="form-label">Recipient Name</label>
                                        <input type="text" id="recipient" name="recipient" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" name="phone" id="phone" class="form-control" required
                                            pattern="^\d+$"
                                            title="Enter numbers only">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="street" class="form-label">Street</label>
                                        <input type="text" id="street" name="street" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="barangay" class="form-label">Barangay</label>
                                        <input type="text" id="barangay" name="barangay" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" id="city" name="city" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="province" class="form-label">Province</label>
                                        <input type="text" id="province" name="province" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="zipcode" class="form-label">Zipcode</label>
                                        <input type="text" name="zipcode" id="zipcode" class="form-control" required
                                            pattern="^\d+$"
                                            title="Enter numbers only">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="country" class="form-label">Country</label>
                                        <input type="text" id="country" name="country" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mt-4">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select id="payment_method" name="payment_method" class="form-select" required>
                                    <option value="">Select</option>
                                    <option value="cod">Cash on Delivery</option>
                                </select>
                            </div>

                            <input type="hidden" name="total_amount" value="<?= $total ?>">
                            <button type="submit" class="btn btn-outline-success w-100">
                                <i class="bi bi-bag-check"></i> Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleNewAddressForm(select) {
    const form = document.getElementById('new_address_form');
    if (select.value === 'new') {
        form.style.display = 'block';
        form.querySelectorAll('input, textarea').forEach(input => input.required = true);
    } else {
        form.style.display = 'none';
        form.querySelectorAll('input, textarea').forEach(input => input.required = false);
    }
}
</script>

<?php include('../includes/footer.php'); ?>
