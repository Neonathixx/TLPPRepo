<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(["error" => "Admin access required"]);
    exit();
}

$sql = $conn->query("
    SELECT o.OrderID, o.UserID, u.Name AS CustomerName, u.Email,
           p.ProductName, o.PriceInPHP, o.Quantity, o.TotalPrice,
           o.OrderStatus, o.OrderMade, o.OrderShip, o.OrderDestination
    FROM Orders o
    JOIN Users u ON u.UserID = o.UserID
    JOIN Products p ON p.ProductID = o.ProductID
    ORDER BY o.OrderMade DESC
");

$orders = [];
while ($row = $sql->fetch_assoc()) {
    $orders[] = $row;
}

echo json_encode($orders);
