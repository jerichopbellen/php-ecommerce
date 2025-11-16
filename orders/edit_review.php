<?php
session_start();
include('../includes/config.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to update your review.";
    header("Location: ../user/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['product_id']);
$order_id = intval($_GET['order_id']);
$variant_id = isset($_GET['variant_id']) ? intval($_GET['variant_id']) : 0;

// Fetch product, image, variant, and existing review
$query = mysqli_query($conn, "
    SELECT 
        p.name,
        (
            SELECT img_path 
            FROM product_images 
            WHERE product_id = p.product_id 
            ORDER BY image_id ASC 
            LIMIT 1
        ) AS img_path,
        pv.color,
        pv.material,
        r.rating,
        r.comment
    FROM products p
    LEFT JOIN product_variants pv ON pv.product_id = p.product_id AND pv.variant_id = $variant_id
    LEFT JOIN reviews r ON r.user_id = $user_id AND r.product_id = p.product_id AND r.variant_id = $variant_id
    WHERE p.product_id = $product_id
    LIMIT 1
");
$product = mysqli_fetch_assoc($query);

// Format variant label
$color = isset($product['color']) ? trim($product['color']) : '';
$material = isset($product['material']) ? trim($product['material']) : '';
if ($color && $material) {
    $variant_label = htmlspecialchars("$color / $material");
} elseif ($color) {
    $variant_label = htmlspecialchars($color);
} elseif ($material) {
    $variant_label = htmlspecialchars($material);
} else {
    $variant_label = "N/A";
}

// Pre-fill review data
$existing_rating = intval($product['rating'] ?? 0);
$existing_comment = htmlspecialchars($product['comment'] ?? '');
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-4">
            <i class="bi bi-pencil-fill me-2 text-dark"></i>Edit Your Review
          </h4>

          <?php if (!empty($product['img_path'])): ?>
            <div class="text-center mb-4">
              <img src="<?= htmlspecialchars($product['img_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded" style="max-height: 200px;">
            </div>
          <?php endif; ?>

          <form action="update_review.php" method="POST">
            <input type="hidden" name="product_id" value="<?= $product_id ?>">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">
            <input type="hidden" name="variant_id" value="<?= $variant_id ?>">
            <input type="hidden" name="rating" id="rating-value" value="<?= $existing_rating ?>">

            <div class="mb-3">
              <label class="form-label">Product</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" disabled>
            </div>

            <?php if ($color || $material): ?>
              <div class="mb-3">
                <label class="form-label">Variant</label>
                <input type="text" class="form-control" value="<?= $variant_label ?>" disabled>
              </div>
            <?php endif; ?>

            <div class="mb-3">
              <label class="form-label">Rating</label>
              <div id="star-picker" class="fs-4 text-warning">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                  <i class="bi <?= $i <= $existing_rating ? 'bi-star-fill' : 'bi-star' ?>" data-value="<?= $i ?>"></i>
                <?php endfor; ?>
              </div>
              <small class="text-muted">Click to adjust your rating</small>
            </div>

            <div class="mb-3">
              <label for="comment" class="form-label">Comment</label>
              <textarea name="comment" id="comment" rows="4" class="form-control"><?= $existing_comment ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-warning">
                <i class="bi bi-pencil-fill me-1"></i>Update Review
              </button>
              <a href="order_history.php" class="btn btn-secondary">
                <i class="bi bi-x-circle me-1"></i>Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  const stars = document.querySelectorAll('#star-picker i');
  const ratingInput = document.getElementById('rating-value');

  stars.forEach(star => {
    star.addEventListener('click', () => {
      const rating = star.getAttribute('data-value');
      ratingInput.value = rating;

      stars.forEach(s => {
        s.classList.remove('bi-star-fill');
        s.classList.add('bi-star');
      });

      for (let i = 0; i < rating; i++) {
        stars[i].classList.remove('bi-star');
        stars[i].classList.add('bi-star-fill');
      }
    });
  });
</script>

<?php include('../includes/footer.php'); ?>