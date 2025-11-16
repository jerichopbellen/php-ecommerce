<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to update your review.";
    header("Location: ../user/login.php");
    exit;
}

$user_id    = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$order_id   = intval($_POST['order_id']);
$variant_id = intval($_POST['variant_id']);
$rating     = intval($_POST['rating']);
$comment    = trim($_POST['comment']);

// Define foul words
$badWords = [
    'fuck', 'shit', 'bitch', 'asshole', 'bastard', 'damn', 'crap',
    'tangina', 'putangina', 'bobo', 'tanga', 'gago', 'ulol'
];

// Apply regex masking (case-insensitive with /i flag)
foreach ($badWords as $word) {
    $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
    $comment = preg_replace_callback($pattern, function($matches) {
        return str_repeat('*', strlen($matches[0]));
    }, $comment);
}

// Check if review exists
$check_sql = "SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ? AND variant_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "iii", $user_id, $product_id, $variant_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$existing = mysqli_fetch_assoc($check_result);

if ($existing) {
    // Update review
    $update_sql = "UPDATE reviews SET rating = ?, comment = ?, updated_at = NOW() WHERE review_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "isi", $rating, $comment, $existing['review_id']);
    mysqli_stmt_execute($update_stmt);

    $_SESSION['success'] = "Your review has been updated.";
} else {
    $_SESSION['info'] = "No existing review found to update.";
}

header("Location: order_history.php");
exit;