<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
    die('User not authenticated.');
    }
    
    include('../includes/config.php'); 
    $cart_item_id = $_GET['id'];  
    $result = mysqli_query($conn, " DELETE FROM cart_items WHERE cart_item_id = '$cart_item_id'");
    // var_dump($result);
    if ($result) {
        header("Location: view_cart.php");
    }
    
?>