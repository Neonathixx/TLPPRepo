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
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}