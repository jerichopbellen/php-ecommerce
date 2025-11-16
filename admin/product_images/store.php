<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';


if (isset($_POST['submit'])) {
    $product =  trim($_POST['product']);
    $img_path = trim($_POST['img_path']);
    $alt_text = trim($_POST['alt-text']);

    if (isset($_FILES['img_path'])) {
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    $fileType = $_FILES['img_path']['type'];

        if (in_array($fileType, $allowedTypes)) {
            $fileName = basename($_FILES['img_path']['name']);
            $targetDir = '../product_images/images/';
            $targetPath = $targetDir . $fileName;

            // Ensure the folder exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Move the file
            if (move_uploaded_file($_FILES['img_path']['tmp_name'], $targetPath)) {
                // ✅ This is the path you want to store in the database
                $dbPath = '/Furnitures/admin/product_images/images/' . $fileName;
            } else {
                die("Couldn't copy");
            }
        } 
        else {
            $_SESSION['error'] = "wrong file type";
            header("Location: create.php");
            exit;
        }
    }

    $sql = "INSERT INTO product_images (img_path, alt_text, product_id) VALUES('{$dbPath}', '{$alt_text}', '{$product}')";
    $result = mysqli_query($conn, $sql);

    if($result) {
        $_SESSION['success'] = "Product image added successfully.";
        header("Location: index.php");
        exit;
    }
    else {
        $_SESSION['error'] = "Failed to add product image. Please try again.";
        header("Location: create.php");
        exit;
    }
}
