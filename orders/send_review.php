<?php
session_start();
include('../includes/config.php');

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect'] = "Please log in to submit a review.";
    header("Location: ../user/login.php");
    exit;
}

$user_id    = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$order_id   = intval($_POST['order_id']);
$variant_id = intval($_POST['variant_id']);
$rating     = intval($_POST['rating']);
$comment    = trim($_POST['comment']);

//Define foul words list
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

$stmt = mysqli_prepare($conn, "
    INSERT INTO reviews (user_id, product_id, variant_id, rating, comment)
    VALUES (?, ?, ?, ?, ?)
");
mysqli_stmt_bind_param($stmt, "iiiis", $user_id, $product_id, $variant_id, $rating, $comment);
mysqli_stmt_execute($stmt);

$_SESSION['success'] = "Thank you! Your review has been submitted.";
header("Location: order_history.php");
exit;