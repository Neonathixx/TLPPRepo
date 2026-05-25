<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
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

echo json_encode($result->fetch_assoc());
$sql->close();
