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

$keyword = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$keyword = mysqli_real_escape_string($conn, $keyword);

$statusFilter = $_GET['status'] ?? 'active';

$sql = "
    SELECT 
        user_id,
        email,
        first_name,
        last_name,
        is_active,
        is_deleted,
        role
    FROM users
    WHERE 1
";

// Apply keyword filter
if ($keyword) {
    $sql .= " AND (
        email LIKE '%$keyword%'  
        OR first_name LIKE '%$keyword%'  
        OR last_name LIKE '%$keyword%'  
        OR role LIKE '%$keyword%'
    )";
}

// Apply status filter
switch ($statusFilter) {
    case 'active':
        $sql .= " AND is_active = 1 AND is_deleted = 0";
        break;
    case 'deactivated':
        $sql .= " AND is_active = 0 AND is_deleted = 0";
        break;
    case 'deleted':
        $sql .= " AND is_deleted = 1";
        break;
    case 'all':
    default:
        // No additional filter
        break;
}

$sql .= " ORDER BY user_id ASC";

$result = mysqli_query($conn, $sql);
$itemCount = mysqli_num_rows($result);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-people me-2 text-dark"></i>User Management</h3>
    </div>

    <form method="GET" class="mb-4">
        <div class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, or role..." value="<?= htmlspecialchars($keyword) ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <?php
                    $statusOptions = [
                        'all' => 'All Users',
                        'active' => 'Active',
                        'deactivated' => 'Deactivated',
                        'deleted' => 'Deleted'
                    ];
                    foreach ($statusOptions as $key => $label) {
                        $selected = ($statusFilter === $key) ? 'selected' : '';
                        echo "<option value='$key' $selected>$label</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-1">
                <button class="btn btn-outline-secondary w-100" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Total Users: <?= $itemCount ?></h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>User ID</th>
                            <th>Email</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td><?= $row['user_id'] ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['first_name']) ?></td>
                                <td><?= htmlspecialchars($row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td>
                                    <?php
                                        if ($row['is_deleted'] == 1) {
                                            echo "<span class='badge bg-secondary'>Deleted</span>";
                                        } elseif ($row['is_active'] == 1) {
                                            echo "<span class='badge bg-success'>Active</span>";
                                        } else {
                                            echo "<span class='badge bg-danger'>Inactive</span>";
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-3">
                                        <?php if ($row['is_deleted'] == 1): ?>
                                            <i class="fa-regular fa-pen-to-square text-muted opacity-50" title="Edit"></i>
                                            <i class="fa-solid fa-user-slash text-muted opacity-50" title="Deactivate"></i>
                                            <i class="fa-solid fa-trash text-muted opacity-50" title="Delete"></i>
                                        <?php else: ?>
                                            <a href="edit.php?id=<?= $row['user_id'] ?>" title="Edit">
                                                <i class="fa-regular fa-pen-to-square text-primary"></i>
                                            </a>
                                            <?php if ($row['is_active'] == 0): ?>
                                                <a href="reactivate.php?id=<?= $row['user_id'] ?>" title="Reactivate" onclick="return confirm('Are you sure you want to reactivate this user?');">
                                                    <i class="fa-solid fa-user-check text-success"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="deactivate.php?id=<?= $row['user_id'] ?>" title="Deactivate" onclick="return confirm('Are you sure you want to deactivate this user?');">
                                                    <i class="fa-solid fa-user-slash text-warning"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="delete.php?id=<?= $row['user_id'] ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.');">
                                                <i class="fa-solid fa-trash text-danger"></i>
                                            </a>
                                            <a href="orders.php?user_id=<?= $row['user_id'] ?>" title="View Orders">
                                                <i class="fa-solid fa-box text-info"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($itemCount === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No users found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>   