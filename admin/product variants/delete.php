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
    $result = mysqli_query($conn, "DELETE FROM product_variants WHERE variant_id = $id");


    $_SESSION['success'] = "Product variant deleted successfully.";
    header("Location: index.php");
    exit;
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = "Cannot delete product variant: It has existing orders and must remain for audit purposes.";   
    header("Location: index.php");
    exit;
}

?>