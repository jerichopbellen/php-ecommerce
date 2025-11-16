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


$result = mysqli_query($conn, "SELECT * FROM products ORDER BY name ASC");
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php include '../../includes/alert.php'; ?>
                    <h4 class="card-title mb-4"><i class="bi bi-image me-2"></i>Upload Product Image</h4>
                    <form method="POST" action="store.php" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="product" class="form-label">Product Name</label>
                            <select class="form-select" id="product" name="product" required>
                                <option value="" disabled selected>Select Product</option>
                                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                    <option value="<?= $row['product_id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Image File</label>
                            <input class="form-control" type="file" name="img_path" accept="image/*" required>
                        </div>

                        <div class="mb-3">
                            <label for="alt-text" class="form-label">Alt Text</label>
                            <input type="text" class="form-control" id="alt-text" name="alt-text" placeholder="Enter alt text" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary" name="submit" value="submit">
                                <i class="bi bi-upload me-1"></i>Submit
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