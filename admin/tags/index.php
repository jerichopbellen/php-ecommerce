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

$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';
$keywordEscaped = mysqli_real_escape_string($conn, $keyword);

$sql = "SELECT * FROM tags";
if ($keyword) {
    $sql .= " WHERE name LIKE '%$keywordEscaped%'";
}
$sql .= " ORDER BY name ASC";

$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-bookmarks me-2"></i>Tags</h3>
        <a href="create.php" class="btn btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i> New Tag
        </a>
    </div>

    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search tags..." value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-outline-secondary" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Total Tags: <?= $count ?></h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $row['tag_id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="delete.php?id=<?= $row['tag_id'] ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this tag?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($count === 0): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No tags found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>