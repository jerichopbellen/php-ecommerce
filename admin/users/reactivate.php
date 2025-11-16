<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

require_once '../../includes/config.php';

// Check if user ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "User ID not provided";
    header('Location: index.php');
    exit();
}

$user_id = $_GET['id'];

try {
    $stmt = $conn->prepare("UPDATE users SET is_active = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "User reactivated successfully.";
    } else {
        $_SESSION['error'] = "No user found with the provided ID.";
    }

    $stmt->close();

} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header('Location: index.php');
exit();
?>