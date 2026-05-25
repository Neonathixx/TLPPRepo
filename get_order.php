<?php

require_once 'session_config.php';
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$userID  = $_SESSION['user_id'];
$orderID = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$orderID) {
    echo json_encode(["error" => "No order ID provided"]);
    exit();
}

$sql = $conn->prepare("
    SELECT o.OrderID, o.ProductID, p.ProductName, o.PriceInPHP, o.Quantity,
           o.TotalPrice, o.OrderStatus, o.OrderMade, o.OrderShip, o.OrderDestination
    FROM Orders o
    JOIN Products p ON p.ProductID = o.ProductID
    WHERE o.OrderID = ? AND o.UserID = ?
");
$sql->bind_param("ii", $orderID, $userID);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Order not found"]);
    exit();
}

$row = $result->fetch_assoc();

// Parse decline reason out of status if present e.g. "Declined: Too busy that day"
$status = $row['OrderStatus'];
$declineReason = null;
if (str_starts_with($status, 'Declined:')) {
    $declineReason = trim(substr($status, strlen('Declined:')));
    $status = 'Declined';
}

// Map to what order-details.js expects
echo json_encode([
    "id"             => $row['OrderID'],
    "date"           => date("F j, Y", strtotime($row['OrderMade'])),
    "status"         => $status,
    "total"          => number_format($row['TotalPrice'], 2),
    "pickup_date"    => $row['OrderShip'] ? date("F j, Y", strtotime($row['OrderShip'])) : null,
    "decline_reason" => $declineReason,
    "items"          => [
        [
            "name"  => $row['ProductName'],
            "qty"   => $row['Quantity'],
            "price" => $row['PriceInPHP'],
            "image" => ""
        ]
    ]
]);

$sql->close();
