<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$userID       = $_SESSION['user_id'];
$orderID      = (int)($_POST['order_id'] ?? 0);
$pickupDate   = $_POST['pickup_date'] ?? null;
$instructions = $_POST['instructions'] ?? '';
$destination  = $_POST['destination'] ?? '';

if (!$orderID || !$pickupDate) {
    echo json_encode(["error" => "Missing order ID or pickup date"]);
    exit();
}

$sql = $conn->prepare("
    UPDATE Orders
    SET OrderStatus = 'Pending Approval',
        OrderShip = ?,
        OrderDestination = ?
    WHERE OrderID = ? AND UserID = ? AND OrderStatus = 'Filing Form'
");
$sql->bind_param("ssii", $pickupDate, $destination, $orderID, $userID);

if ($sql->execute() && $sql->affected_rows > 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Could not update order. It may have already been submitted."]);
}

$sql->close();
