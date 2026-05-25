<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(["error" => "Admin access required"]);
    exit();
}

$orderID = (int)($_POST['order_id'] ?? 0);
$reason  = $_POST['reason'] ?? '';

if (!$orderID || !$reason) {
    echo json_encode(["error" => "Order ID and reason are required"]);
    exit();
}

$status = "Declined: " . $reason;

$sql = $conn->prepare("
    UPDATE Orders SET OrderStatus = ?
    WHERE OrderID = ? AND OrderStatus = 'Pending Approval'
");
$sql->bind_param("si", $status, $orderID);

if ($sql->execute() && $sql->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Could not decline order."]);
}

$sql->close();
