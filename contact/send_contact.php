<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include('../includes/config.php');

$user_id = $_SESSION['user_id'];
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$subject || !$message) {
    $_SESSION['flash'] = "All fields are required.";
    header("Location: contact.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['flash'] = "Invalid email format.";
    header("Location: contact.php");
    exit;
}

$sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $subject, $message);
mysqli_stmt_execute($stmt);

$_SESSION['success'] = "Your message has been sent successfully.";
header("Location: index.php");
exit;