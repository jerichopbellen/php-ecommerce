<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$id = (int) ($_POST['id'] ?? 0);
$reply = trim($_POST['reply'] ?? '');

if ($id <= 0 || !$reply) {
    header("Location: admin_contact_messages.php?msg=invalid");
    exit;
}

$sql = "UPDATE contact_messages SET reply = ?, replied_at = NOW() WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $reply, $id);
$result = mysqli_stmt_execute($stmt);


if ($result) {
    $_SESSION['success'] = "Reply sent successfully.";
    header("Location: index.php?msg=replied");
    exit;
}
else {
    $_SESSION['error'] = "Failed to send reply. Please try again.";
    header("Location: index.php?msg=error");
    exit;
}