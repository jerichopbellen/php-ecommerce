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
    $result = mysqli_query($conn, "DELETE FROM tags WHERE tag_id = $id");


    $_SESSION['success'] = "Tag deleted successfully.";
    header("Location: index.php");
    exit;
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = "Error deleting tag: Cannot delete tag with products assiged to it.";
    header("Location: index.php");
    exit;
}

?>