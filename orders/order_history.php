<?php
session_start();
include('../includes/config.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to view your order history.";
    header("Location: ../user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all purchased products from completed or cancelled orders, including one image per product
$sql = "
    SELECT 
        p.product_id,
        pv.variant_id,
        p.name AS product_name,
        pv.color,
        pv.material,
        oi.quantity,
        oi.price,
        o.status,
        o.created_at,
        o.cancelled_at,
        o.order_id,
        (
            SELECT img_path 
            FROM product_images 
            WHERE product_id = p.product_id 
            ORDER BY image_id ASC 
            LIMIT 1
        ) AS img_path
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product_variants pv ON oi.variant_id = pv.variant_id
    JOIN products p ON pv.product_id = p.product_id
    WHERE o.user_id = ? AND o.status IN ('Received', 'Cancelled')
    ORDER BY o.created_at DESC
";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container my-5">
  <h2 class="text-center mb-4"><i class="bi bi-clock-history me-2 text-dark"></i>Order History</h2>
  <?php include('../includes/alert.php'); ?>
  <?php if (mysqli_num_rows($result) === 0): ?>
    <div class="alert alert-info text-center">You haven't purchased any products yet.</div>
  <?php else: ?>
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
      <?php
        $color = trim($row['color'] ?? '');
        $material = trim($row['material'] ?? '');
        if ($color && $material) {
            $variant = htmlspecialchars("$color / $material");
        } elseif ($color) {
            $variant = htmlspecialchars($color);
        } elseif ($material) {
            $variant = htmlspecialchars($material);
        } else {
            $variant = "N/A";
        }
        $subtotal = $row['quantity'] * $row['price'];

        // Check if review exists
        $review_check_sql = "
            SELECT review_id 
            FROM reviews 
            WHERE user_id = ? AND product_id = ? AND variant_id = ?
            LIMIT 1
        ";
        $review_check_stmt = mysqli_prepare($conn, $review_check_sql);
        mysqli_stmt_bind_param($review_check_stmt, "iii", $user_id, $row['product_id'], $row['variant_id']);
        mysqli_stmt_execute($review_check_stmt);
        $review_result = mysqli_stmt_get_result($review_check_stmt);
        $has_review = mysqli_fetch_assoc($review_result);
      ?>
      <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <div>
            <strong><?= htmlspecialchars($row['product_name']) ?></strong><br>
            <?php if ($row['status'] === 'Cancelled' && !empty($row['cancelled_at'])): ?>
              <small class="text-muted">Cancelled on <?= date('F j, Y', strtotime($row['cancelled_at'])) ?></small>
            <?php else: ?>
              <small class="text-muted">Purchased on <?= date('F j, Y', strtotime($row['created_at'])) ?></small>
            <?php endif; ?>
          </div>
          <span class="badge 
            <?= $row['status'] === 'Received' ? 'bg-secondary' : 'bg-danger' ?>">
            <?= htmlspecialchars($row['status']) ?>
          </span>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 text-center">
              <?php if (!empty($row['img_path'])): ?>
                <img src="<?= htmlspecialchars($row['img_path']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="img-fluid rounded" style="max-height: 180px;">
              <?php else: ?>
                <div class="text-muted small">No image available</div>
              <?php endif; ?>
            </div>
            <div class="col-md-8">
              <p><strong>Variant:</strong> <?= $variant ?></p>
              <p><strong>Quantity:</strong> <?= $row['quantity'] ?></p>
              <p><strong>Price:</strong> ₱<?= number_format($row['price'], 2) ?></p>
              <p><strong>Subtotal:</strong> ₱<?= number_format($subtotal, 2) ?></p>

              <div class="text-end">
                <?php if ($row['status'] === 'Received'): ?>
                    <form action="<?= $has_review ? 'edit_review.php' : 'write_review.php' ?>" method="GET" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <input type="hidden" name="variant_id" value="<?= $row['variant_id'] ?>">
                        <button type="submit" class="btn <?= $has_review ? 'btn-outline-warning' : 'btn-outline-primary' ?>">
                            <i class="bi <?= $has_review ? 'bi-pencil-fill' : 'bi-pencil-square' ?> me-1"></i>
                            <?= $has_review ? 'Update Review' : 'Write Review' ?>
                        </button>
                    </form>
                <?php else: ?>
                  <button class="btn btn-outline-secondary" disabled>
                    <i class="bi bi-x-square me-1"></i> Review Unavailable
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>