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
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "DELETE FROM product_tags WHERE tag_id = $id");


    $_SESSION['success'] = "Product Tag deleted successfully.";
    header("Location: index.php");
    exit;
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = "Error deleting product tag: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

?>