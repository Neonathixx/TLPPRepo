<?php

require_once 'session_config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$name    = htmlspecialchars($_POST['name'] ?? '');
$email   = htmlspecialchars($_POST['email'] ?? '');
$subject = htmlspecialchars($_POST['subject'] ?? 'Message from Contact Form');
$message = htmlspecialchars($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(["error" => "Name, email, and message are required."]);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email address."]);
    exit();
}

$to      = "thelittlepawpatissier@gmail.com"; // change to Tita's actual email
$headers = "From: $name <$email>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
$body    = "Name: $name\nEmail: $email\n\nMessage:\n$message";

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(["success" => true, "message" => "Your message has been sent!"]);
} else {
    echo json_encode(["error" => "Failed to send message. Please try again later."]);
}
?>