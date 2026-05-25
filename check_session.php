<?php
$domain = 'thelittlepawpatissier.shop';
session_set_cookie_params([
    'lifetime' => 86400,
    'path'     => '/',
    'domain'   => $domain,
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode(["logged_in" => true, "user_id" => $_SESSION['user_id']]);
} else {
    echo json_encode(["logged_in" => false]);
}