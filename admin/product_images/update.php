<?php
session_start();
    
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

include '../../includes/config.php';

$image_id = (int) $_POST['image_id'];
$alt_text = trim($_POST['alt_text']);
$path = $_POST['existingImage']; // fallback to existing image path
echo $path;

if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    if ($_FILES['image']['type'] == "image/png" || $_FILES['image']['type'] == "image/jpg" || $_FILES['image']['type'] == "image/jpeg") {
        $source = $_FILES['image']['tmp_name'];
        $target = "../product_images/images/" . $_FILES['image']['name'];
        $path = '/Furnitures/admin/product_images/images/' . $_FILES['image']['name'];
        move_uploaded_file($source, $target) or die("Couldn't copy");
    }
    else{
        $_SESSION['error'] = "wrong file type";
        header("Location: edit.php?id={$image_id}");
        exit;
    }
}

$sql = "UPDATE product_images SET alt_text = '{$alt_text}', img_path = '{$path}' WHERE image_id = $image_id";
$result = mysqli_query($conn, $sql);
if ($result) {
    $_SESSION['success'] = "Product image updated successfully.";
    header("Location: index.php");
}
else {
    $_SESSION['error'] = "Update failed.";
    header("Location: edit.php?id={$image_id}");
    exit;
}
?>