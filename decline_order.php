<?php

require_once 'session_config.php';
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(401);
    echo json_encode(["error" => "Admin access required"]);
    exit();
}

$input   = json_decode(file_get_contents('php://input'), true);
$orderID = (int)($input['order_id'] ?? 0);
$reason  = $input['reason'] ?? '';

if (!$orderID || !$reason) {
    echo json_encode(["error" => "Order ID and reason are required", "message" => "Order ID and reason are required"]);
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
    echo json_encode(["error" => "Could not decline order.", "message" => "Could not decline order."]);
}

$sql->close();
