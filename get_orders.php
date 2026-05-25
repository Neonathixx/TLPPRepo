<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$userID = $_SESSION['user_id'];

$sql = $conn->prepare("
    SELECT o.OrderID, o.ProductID, p.ProductName, o.PriceInPHP, o.Quantity,
           o.TotalPrice, o.OrderStatus, o.OrderMade, o.OrderShip, o.OrderDestination
    FROM Orders o
    JOIN Products p ON p.ProductID = o.ProductID
    WHERE o.UserID = ?
    ORDER BY o.OrderMade DESC
");
$sql->bind_param("i", $userID);
$sql->execute();
$result = $sql->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
$sql->close();
