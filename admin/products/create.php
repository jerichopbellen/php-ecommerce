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
include '../../includes/alert.php';

// Use prepared statements for fetching categories and brands
$stmt1 = mysqli_prepare($conn, "SELECT category_id, name FROM categories ORDER BY name ASC");
mysqli_stmt_execute($stmt1);
$result1 = mysqli_stmt_get_result($stmt1);

$stmt2 = mysqli_prepare($conn, "SELECT brand_id, name FROM brands ORDER BY name ASC");
mysqli_stmt_execute($stmt2);
$result2 = mysqli_stmt_get_result($stmt2);
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-box-seam me-2"></i>Create New Product
                    </h4>

                    <form method="POST" action="store.php" enctype="multipart/form-data">
                        <!-- Product Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter product name" maxlength="255" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Enter product description" maxlength="1000" required>
                        </div>

                        <!-- Brand -->
                        <div class="mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select" id="brand" name="brand" required>
                                <option value="" disabled selected>Select Brand</option>
                                <?php while ($row = mysqli_fetch_assoc($result2)) : ?>
                                    <option value="<?=(int)$row['brand_id'] ?>"><?=htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="" disabled selected>Select Category</option>
                                <?php while ($row = mysqli_fetch_assoc($result1)) : ?>
                                    <option value="<?=(int)$row['category_id'] ?>"><?=htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Dimensions -->
                        <div class="mb-3">
                            <label class="form-label">Dimensions (cm)</label>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="number" step="0.01" min="0" max="9999.99" class="form-control" name="length" placeholder="Length" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" min="0" max="9999.99" class="form-control" name="width" placeholder="Width" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" min="0" max="9999.99" class="form-control" name="height" placeholder="Height" required>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary" name="submit" value="submit">
                                <i class="bi bi-check-circle me-1"></i>Submit
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

<?php 
mysqli_stmt_close($stmt1);
mysqli_stmt_close($stmt2);
include '../../includes/footer.php'; 
?>