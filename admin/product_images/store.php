<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';


if (isset($_POST['submit'])) {
    // Input sanitization
    $product = filter_var(trim($_POST['product']), FILTER_VALIDATE_INT);
    $alt_text = htmlspecialchars(trim($_POST['alt-text']), ENT_QUOTES, 'UTF-8');

    if (!$product) {
        $_SESSION['error'] = "Invalid product ID.";
        header("Location: create.php");
        exit;
    }

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
                $dbPath = '/Furnitures/admin/product_images/images/' . $fileName;
            } else {
                $_SESSION['error'] = "Failed to upload file.";
                header("Location: create.php");
                exit;
            }
        } 
        else {
            $_SESSION['error'] = "Wrong file type.";
            header("Location: create.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "No file uploaded.";
        header("Location: create.php");
        exit;
    }

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        // Prepared statement
        $stmt = mysqli_prepare($conn, "INSERT INTO product_images (img_path, alt_text, product_id) VALUES(?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssi", $dbPath, $alt_text, $product);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_commit($conn);
            $_SESSION['success'] = "Product image added successfully.";
            header("Location: index.php");
        } else {
            throw new Exception("Failed to execute statement.");
        }
        
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = "Failed to add product image. Please try again.";
        header("Location: create.php");
    }
    exit;
}
