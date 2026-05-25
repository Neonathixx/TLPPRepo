<?php

require_once 'session_config.php';
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$userID = $_SESSION['user_id'];

// Get user info + order count
$sql = $conn->prepare("
    SELECT u.Name, u.Email, u.Username,
           COUNT(o.OrderID) AS orders_count
    FROM Users u
    LEFT JOIN Orders o ON o.UserID = u.UserID
    WHERE u.UserID = ?
    GROUP BY u.UserID
");
$sql->bind_param("i", $userID);
$sql->execute();
$result = $sql->get_result();
$user = $result->fetch_assoc();

echo json_encode([
    "name"         => $user['Name'],
    "username"     => $user['Username'],
    "email"        => $user['Email'],
    "orders_count" => $user['orders_count'],
    "points"       => 0  // points system not yet in schema; placeholder
]);

$sql->close();
?>