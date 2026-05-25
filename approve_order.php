<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(["error" => "Admin access required"]);
    exit();
}

$orderID = (int)($_POST['order_id'] ?? 0);

if (!$orderID) {
    echo json_encode(["error" => "No order ID provided"]);
    exit();
}

$sql = $conn->prepare("
    UPDATE Orders SET OrderStatus = 'Pending Payment'
    WHERE OrderID = ? AND OrderStatus = 'Pending Approval'
");
$sql->bind_param("i", $orderID);

if ($sql->execute() && $sql->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Could not approve order."]);
}

$sql->close();
