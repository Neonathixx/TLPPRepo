<?php

session_start();
header('Content-Type: application/json');

// Hardcoded admin credentials — change these to something secure!
define('ADMIN_USERNAME', 'tlpp_admin');
define('ADMIN_PASSWORD', 'TLPPAdmin2024!');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
    $_SESSION['is_admin'] = true;
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Invalid admin credentials"]);
}
