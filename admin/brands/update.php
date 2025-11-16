<?php   
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

    include '../../includes/config.php';
    // print_r($_POST);
    $name = trim($_POST['brand_name']);
     // Check if brand name already exists
    $check_sql = "SELECT brand_id FROM brands WHERE name = '{$name}'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "Brand name already exists.";
        header("Location: index.php");
        exit;
    }

    $result = mysqli_query($conn, " UPDATE brands SET name='{$_POST['brand_name']}' WHERE brand_id = {$_POST['brand_id']}");
    // var_dump($result);
    if ($result) {
        $_SESSION['success'] = "Brand updated successfully.";
        header("Location: index.php");
        exit;
    }
    else {
        $_SESSION['error'] = "Update failed.";
        header("Location: edit.php?id={$_POST['brand_id']}");
        exit;
    }
    
?>
