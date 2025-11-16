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

$keyword = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$statusFilter = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : '';

$keyword = mysqli_real_escape_string($conn, $keyword);
$statusFilter = mysqli_real_escape_string($conn, $statusFilter);

$sql = "
    SELECT 
        order_id,
        created_at,
        status,
        tracking_number,
        user_id,
        customer_name,
        customer_email,
        SUM(subtotal) AS total_amount
    FROM view_order_transaction_details
";

$conditions = [];
if ($keyword) {
    $conditions[] = "(customer_name LIKE '%$keyword%' OR customer_email LIKE '%$keyword%' OR status LIKE '%$keyword%' OR tracking_number LIKE '%$keyword%' OR order_id LIKE '%$keyword%')";
}
if ($statusFilter && in_array($statusFilter, ['pending', 'processing', 'shipped', 'delivered', 'received', 'cancelled'])) {
    $conditions[] = "status = '$statusFilter'";
}
if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " GROUP BY order_id ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);
$itemCount = mysqli_num_rows($result);
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0"><i class="bi bi-receipt me-2"></i>Orders</h3>
    </div>

    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-6">
            <label for="search" class="form-label">Search</label>
            <input type="text" name="search" id="search" class="form-control" placeholder="Customer, email, status, tracking #, or ID..." value="<?= htmlspecialchars($keyword) ?>">
        </div>
        <div class="col-md-3">
            <label for="status" class="form-label">Filter by Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All</option>
                <?php
                $statuses = ['pending', 'processing', 'shipped', 'delivered', 'received', 'cancelled'];
                foreach ($statuses as $status) {
                    $selected = $statusFilter === $status ? 'selected' : '';
                    echo "<option value='$status' $selected>" . ucfirst($status) . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-secondary w-100" type="submit">
                <i class="bi bi-search me-1"></i>Filter
            </button>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Total Orders: <?= $itemCount ?></h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Tracking #</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td>#<?= $row['order_id'] ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php
                                        $status = strtolower($row['status']);
                                        $badge = match ($status) {
                                            'pending'    => 'warning',
                                            'processing' => 'info',
                                            'shipped'    => 'primary',
                                            'delivered'  => 'success',
                                            'received'   => 'secondary',
                                            'cancelled'  => 'danger',
                                            default      => 'dark text-white'
                                        };
                                        echo "<span class='badge bg-$badge text-capitalize'>$status</span>";
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($row['tracking_number'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['customer_name']) ?></td>
                                <td><?= htmlspecialchars($row['customer_email']) ?></td>
                                <td>₱<?= number_format($row['total_amount'], 2) ?></td>
                                <td class="text-center">
                                    <a href="view.php?id=<?= $row['order_id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($itemCount === 0): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>