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

$product = mysqli_query($conn, "
    SELECT 
        p.product_id, 
        p.name AS product_name, 
        p.description, 
        p.brand_id, 
        b.name AS brand_name,
        p.category_id,
        c.name AS category_name,
        p.dimension
    FROM products p
    INNER JOIN brands b ON p.brand_id = b.brand_id
    INNER JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = {$id}
    LIMIT 1
");
$product = mysqli_fetch_assoc($product);

$brands = mysqli_query($conn, "SELECT * FROM brands WHERE brand_id != {$product['brand_id']} ORDER BY name");
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE category_id != {$product['category_id']} ORDER BY name");

// Parse dimension into length, width, height
$length = $width = $height = '';
if (!empty($product['dimension']) && preg_match('/(\d+(\.\d+)?)\s*x\s*(\d+(\.\d+)?)\s*x\s*(\d+(\.\d+)?)/', $product['dimension'], $matches)) {
    $length = $matches[1];
    $width  = $matches[3];
    $height = $matches[5];
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-box-seam me-2"></i>Edit Product
                    </h4>

                    <form action="update.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="productName" value="<?= htmlspecialchars($product['product_name']) ?>" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($product['description']) ?>" required>
                        </div>

                        <!-- Brand -->
                        <div class="mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select" id="brand" name="brand_id" required>
                                <option value="<?= $product['brand_id'] ?>" selected><?= htmlspecialchars($product['brand_name']) ?></option>
                                <?php while ($row = mysqli_fetch_assoc($brands)) : ?>
                                    <option value="<?= $row['brand_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category_id" required>
                                <option value="<?= $product['category_id'] ?>" selected><?= htmlspecialchars($product['category_name']) ?></option>
                                <?php while ($row = mysqli_fetch_assoc($categories)) : ?>
                                    <option value="<?= $row['category_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Dimensions -->
                        <div class="mb-3">
                            <label class="form-label">Dimensions (cm)</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="number" step="0.01" class="form-control" name="length" placeholder="Length" value="<?= $length ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" class="form-control" name="width" placeholder="Width" value="<?= $width ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" class="form-control" name="height" placeholder="Height" value="<?= $height ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
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