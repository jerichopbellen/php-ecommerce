<?php
require __DIR__ . '/../includes/mail.php';

// Replace these with your Mailtrap sandbox credentials
$recipientEmail = 'customer@example.com'; // or any email, Mailtrap will catch it
$recipientName  = 'Customer Name'; // your name or test recipient

$subject = 'Mailtrap Test';
$body = '<p>If you see this in Mailtrap, PHPMailer is configured correctly.</p>';

$ok = sendMail($recipientEmail, $recipientName, $subject, $body, $mailConfig);

echo $ok ? "Mail sent" : "Mail failed";