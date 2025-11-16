<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$tag_id = intval($_POST['tag_id']);
$name = trim($_POST['name']);
$nameEscaped = mysqli_real_escape_string($conn, $name);

$check_sql = "SELECT tag_id FROM tags WHERE name = '{$name}'";
$check_result = mysqli_query($conn, $check_sql);
    
if (mysqli_num_rows($check_result) > 0) {
    $_SESSION['error'] = "Tag name already exists.";
    header("Location: index.php");
    exit;
}

if ($tag_id && $nameEscaped) {
    mysqli_query($conn, "UPDATE tags SET name = '$nameEscaped' WHERE tag_id = $tag_id");
}

$_SESSION['success'] = "Tag updated successfully.";
header("Location: index.php");
exit;