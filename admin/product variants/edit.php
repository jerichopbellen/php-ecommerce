<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash'] = "Please log in to access this page.";
    header("Location: ../../user/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    echo "
    <html>
    <head>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
        <title>Access Denied</title>
    </head>
    <body class='bg-light'>
        <div class='container py-5'>
            <div class='alert alert-danger text-center'>
                Access denied. This page is restricted to administrators.
            </div>
        </div>
    </body>
    </html>";
    exit;
}

include '../../includes/adminHeader.php';
include '../../includes/config.php';

$id = intval($_GET['id']);

$product_variant = mysqli_query($conn, "
    SELECT 
        v.variant_id,
        v.color,
        v.material,
        v.price,
        s.quantity, 
        p.product_id, 
        p.name AS product_name
    FROM product_variants v
    INNER JOIN products p ON v.product_id = p.product_id
    INNER JOIN stocks s ON v.variant_id = s.variant_id
    WHERE v.variant_id = {$id}
    LIMIT 1
");

$product_variant = mysqli_fetch_assoc($product_variant);
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <?php include '../../includes/alert.php'; ?>
          <h4 class="card-title mb-4">
            <i class="bi bi-palette me-2 text-dark"></i>Edit Product Variant
          </h4>
          <form action="update.php" method="POST">
            <input type="hidden" name="variant_id" value="<?= $product_variant['variant_id'] ?>">

            <div class="mb-3">
              <label for="product" class="form-label">Product Name</label>
              <select class="form-select" id="product" name="product_id" disabled>
                <option value="<?= $product_variant['product_id'] ?>" selected><?= htmlspecialchars($product_variant['product_name']) ?></option>
              </select>
            </div>

            <div class="mb-3">
              <label for="color" class="form-label">Color</label>
              <input type="text" class="form-control" id="color" name="color" value="<?= htmlspecialchars($product_variant['color']) ?>">
            </div>

            <div class="mb-3">
              <label for="material" class="form-label">Material</label>
              <input type="text" class="form-control" id="material" name="material" value="<?= htmlspecialchars($product_variant['material']) ?>">
            </div>

            <div class="mb-3">
              <label for="sell_price" class="form-label">Sell Price</label>
              <input type="text" class="form-control" id="sell_price" name="sell_price" value="<?= htmlspecialchars($product_variant['price']) ?>" required>
            </div>

            <div class="mb-3">
              <label for="quantity" class="form-label">Quantity</label>
              <input type="number" class="form-control" id="quantity" name="quantity" value="<?= htmlspecialchars($product_variant['quantity']) ?>" required>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary" name="submit" value="submit">
                <i class="bi bi-check-circle me-1"></i>Update
              </button>
              <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-x-circle me-1"></i>Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include '../../includes/footer.php'; ?>