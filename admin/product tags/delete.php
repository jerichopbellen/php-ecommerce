<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Input sanitization
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception("Invalid tag ID.");
    }
    
    $id = intval($_GET['id']);
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    // Prepared statement
    $stmt = mysqli_prepare($conn, "DELETE FROM product_tags WHERE tag_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    
    // Check if any rows were affected
    if (mysqli_stmt_affected_rows($stmt) === 0) {
        throw new Exception("Product tag not found.");
    }
    
    mysqli_stmt_close($stmt);
    
    // Commit transaction
    mysqli_commit($conn);
    
    $_SESSION['success'] = "Product Tag deleted successfully.";
    header("Location: index.php");
    exit;
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    $_SESSION['error'] = "Error deleting product tag: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

?>