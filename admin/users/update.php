<?php

session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

    include '../../includes/config.php';
    // print_r($_POST);
    $result = mysqli_query($conn, " UPDATE users SET role = '{$_POST['role']}' WHERE user_id = {$_POST['user_id']}");
    // var_dump($result);
    if ($result) {
        $_SESSION['success'] = "User role updated successfully.";
        header("Location: index.php");
        exit;
    }
    
?>