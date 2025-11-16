<?php
session_start();
include("../includes/config.php");

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    $_SESSION['redirect'] = 'Please log in to update your profile.';
    header("Location: ../user/login.php");
    exit();
}

// Update profile info
if (isset($_POST['submit_profile'])) {
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);

    $sql = "UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssi', $fname, $lname, $userId);
    mysqli_stmt_execute($stmt);

    $_SESSION['success'] = 'Profile updated successfully.';
    header("Location: profile.php");
    exit();
}

if (isset($_POST['remove_avatar'])) {
    $defaultPath = "/Furnitures/user/avatars/default-avatar.png";

    // Check current avatar
    $check_sql = "SELECT img_path FROM users WHERE user_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 'i', $userId);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $row = mysqli_fetch_assoc($check_result);

    if ($row && $row['img_path'] === $defaultPath) {
        // Already default avatar
        $_SESSION['info'] = 'You do not have a profile picture to remove.';
    } else {
        // Reset to default
        $sql = "UPDATE users SET img_path = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $defaultPath, $userId);
        mysqli_stmt_execute($stmt);

        $_SESSION['success'] = 'Profile picture removed.';
    }

    header("Location: profile.php");
    exit();
}

// Change password
if (isset($_POST['submit_password'])) {
    $current = sha1(trim($_POST['current_password']));
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    if ($new !== $confirm) {
        $_SESSION['error'] = 'New passwords do not match.';
        header("Location: profile.php");
        exit();
    }

    $check_sql = "SELECT user_id FROM users WHERE user_id = ? AND password_hash = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 'is', $userId, $current);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) === 1) {
        $update_sql = "UPDATE users SET password_hash = ? WHERE user_id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        $hashed_new = sha1($new);
        mysqli_stmt_bind_param($update_stmt, 'si', $hashed_new, $userId);
        mysqli_stmt_execute($update_stmt);
        $_SESSION['success'] = 'Password updated successfully.';
    } else {
        $_SESSION['error'] = 'Current password is incorrect.';
    }

    header("Location: profile.php");
    exit();
}

// Upload profile picture
if (isset($_POST['submit_avatar']) && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/png', 'image/jpg', 'image/jpeg'];
    $file = $_FILES['avatar'];

    if (in_array($file['type'], $allowedTypes)) {
        $source = $file['tmp_name'];
        $filename = basename($file['name']);
        $target = __DIR__ . "/avatars/" . $filename;
        $path = "/Furnitures/user/avatars/" . $filename;

        if (move_uploaded_file($source, $target)) {
            $update_sql = "UPDATE users SET img_path = ? WHERE user_id = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt, 'si', $path, $userId);
            mysqli_stmt_execute($stmt);

            $_SESSION['success'] = 'Profile picture updated.';
        } else {
            $_SESSION['error'] = "Couldn't save uploaded image.";
        }
    } else {
        $_SESSION['error'] = 'Invalid image type. Only PNG and JPG are allowed.';
    }

    header("Location: profile.php");
    exit();
}

// Add new address
if (isset($_POST['submit_address'])) {
    $recipient = trim($_POST['recipient']);
    $street    = trim($_POST['street']);
    $barangay  = trim($_POST['barangay']);
    $city      = trim($_POST['city']);
    $province  = trim($_POST['province']);
    $zipcode   = trim($_POST['zipcode']);
    $country   = trim($_POST['country']);
    $phone     = trim($_POST['phone']);

    $sql = "INSERT INTO addresses 
            (recipient, street, barangay, city, province, zipcode, country, phone, user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssssi', 
        $recipient, $street, $barangay, $city, $province, $zipcode, $country, $phone, $userId);
    mysqli_stmt_execute($stmt);

    $_SESSION['success'] = 'Address added successfully.';
    header("Location: profile.php");
    exit();
}

// Delete address
if (isset($_GET['delete_address'])) {
    $addressId = (int) $_GET['delete_address'];

    // Check if this address is linked to any active orders (except received/cancelled/returned)
    $check_sql = "
        SELECT 1 
        FROM orders 
        WHERE address_id = ? 
          AND user_id = ? 
        LIMIT 1
    ";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, 'ii', $addressId, $userId);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        // Address is in use by active orders
        $_SESSION['error'] = 'This address cannot be deleted because it is linked to your active and previous orders.';
        header("Location: profile.php");
        exit();
    }

    // Safe to delete if only linked to received/cancelled/returned orders or unused
    $sql = "DELETE FROM addresses WHERE address_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ii', $addressId, $userId);
    mysqli_stmt_execute($stmt);

    $_SESSION['success'] = 'Address removed successfully.';
    header("Location: profile.php");
    exit();
}

// Fallback
header("Location: profile.php");
exit();