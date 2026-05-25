<?php

session_start();
header('Content-Type: application/json');

define('ADMIN_USERNAME', 'tlpp_admin');
define('ADMIN_PASSWORD', 'TLPPAdmin2024!');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

// admin-login.js sends JSON body
$input    = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
    $_SESSION['is_admin'] = true;
    echo json_encode(["success" => true]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid admin credentials", "message" => "Invalid admin credentials"]);
}
