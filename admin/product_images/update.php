<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$image_id = (int) $_POST['image_id'];
$alt_text = trim(htmlspecialchars($_POST['alt_text'], ENT_QUOTES, 'UTF-8'));
$path = trim($_POST['existingImage']); // fallback to existing image path
echo htmlspecialchars($path, ENT_QUOTES, 'UTF-8');

if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $allowed_types = ['image/png', 'image/jpg', 'image/jpeg'];
    $file_type = $_FILES['image']['type'];
    
    if (in_array($file_type, $allowed_types)) {
        $source = $_FILES['image']['tmp_name'];
        $safe_filename = basename($_FILES['image']['name']);
        $target = "../product_images/images/" . $safe_filename;
        $path = '/Furnitures/admin/product_images/images/' . $safe_filename;
        
        if (!move_uploaded_file($source, $target)) {
            $_SESSION['error'] = "File upload failed.";
            header("Location: edit.php?id={$image_id}");
            exit;
        }
    }
    else{
        $_SESSION['error'] = "wrong file type";
        header("Location: edit.php?id={$image_id}");
        exit;
    }
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    $stmt = mysqli_prepare($conn, "UPDATE product_images SET alt_text = ?, img_path = ? WHERE image_id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $alt_text, $path, $image_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_commit($conn);
        $_SESSION['success'] = "Product image updated successfully.";
        header("Location: index.php");
    } else {
        throw new Exception("Update failed.");
    }
    
    mysqli_stmt_close($stmt);
} catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = "Update failed.";
    header("Location: edit.php?id={$image_id}");
    exit;
}
?>