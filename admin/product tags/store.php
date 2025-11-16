<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

// Input sanitization
$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$tag_id = filter_input(INPUT_POST, 'tag_id', FILTER_VALIDATE_INT);

if ($product_id && $tag_id) {
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Prepared statement
        $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO product_tags (product_id, tag_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $tag_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($result) {
            // Commit transaction
            mysqli_commit($conn);
            $_SESSION['success'] = "Product tag added successfully.";
            header("Location: index.php");
            exit;
        } else {
            throw new Exception("Failed to execute statement");
        }
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        $_SESSION['error'] = "Failed to add product tag. Please try again.";
        header("Location: create.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Invalid input data.";
    header("Location: create.php");
    exit;
}
?>