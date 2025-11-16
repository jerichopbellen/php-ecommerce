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

$products = mysqli_query($conn, "SELECT product_id, name FROM products ORDER BY name ASC");
$tags = mysqli_query($conn, "SELECT tag_id, name FROM tags ORDER BY name ASC");
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-4"><i class="bi bi-bookmark-plus me-2 text-dark"></i>Assign Tag to Product</h4>
          <form method="POST" action="store.php">
            <div class="mb-3">
              <label for="product_id" class="form-label">Product</label>
              <select name="product_id" id="product_id" class="form-select" required>
                <option value="">Select Product</option>
                <?php while ($p = mysqli_fetch_assoc($products)) : ?>
                  <option value="<?= $p['product_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="tag_id" class="form-label">Tag</label>
              <select name="tag_id" id="tag_id" class="form-select" required>
                <option value="">Select Tag</option>
                <?php while ($t = mysqli_fetch_assoc($tags)) : ?>
                  <option value="<?= $t['tag_id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="d-flex justify-content-between">
              <button type="submit" class="btn btn-primary" name="submit" value="submit">
                <i class="bi bi-check-circle me-1"></i>Assign
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