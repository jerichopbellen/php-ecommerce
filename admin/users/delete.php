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
    // Check if user is an admin
    $adminCheckStmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
    $adminCheckStmt->bind_param("i", $user_id);
    $adminCheckStmt->execute();
    $adminResult = $adminCheckStmt->get_result();
    $userData = $adminResult->fetch_assoc();
    $adminCheckStmt->close();

    if ($userData && $userData['role'] === 'admin') {
        $_SESSION['error'] = "Cannot delete admin users.";
        header('Location: index.php');
        exit();
    }

    // Check if user has active orders
    $checkStmt = $conn->prepare("
        SELECT COUNT(*) as order_count 
        FROM orders 
        WHERE user_id = ? AND status NOT IN ('Received', 'Cancelled')
    ");
    $checkStmt->bind_param("i", $user_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    $checkStmt->close();

    if ($row['order_count'] > 0) {
        $_SESSION['error'] = "Cannot delete user with active orders.";
        header('Location: index.php');
        exit();
    }

    // Soft delete: flag user, timestamp, anonymize email and name
    $stmt = $conn->prepare("
        UPDATE users 
        SET 
            is_deleted = 1, 
            deleted_at = NOW(),
            is_active = 0,
            email = CONCAT('deleted_', user_id, '@example.com'),
            first_name = 'Deleted',
            last_name = 'User'
        WHERE user_id = ?;
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "User deleted successfully.";
    } else {
        $_SESSION['error'] = "No user found with the provided ID.";
    }

    $stmt->close();

    // Optional: delete cart items
    $cartStmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $cartStmt->bind_param("i", $user_id);
    $cartStmt->execute();
    $cartStmt->close();

} catch (Exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header('Location: index.php');
exit();
?>