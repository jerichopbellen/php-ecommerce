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

$tag_id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM tags WHERE tag_id = $tag_id");
$tag = mysqli_fetch_assoc($result);
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-4">
            <i class="bi bi-bookmarks me-2 text-dark"></i>Edit Tag
          </h4>
          <form action="update.php" method="POST">
            <input type="hidden" name="tag_id" value="<?= $tag['tag_id'] ?>">
            <div class="mb-3">
              <label for="name" class="form-label">Tag Name</label>
              <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($tag['name']) ?>" required>
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