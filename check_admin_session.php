<?php

require_once 'session_config.php';
header('Content-Type: application/json');

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    echo json_encode(["is_admin" => true]);
} else {
    echo json_encode(["is_admin" => false]);
}
