<?php

require_once 'session_config.php';
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(401);
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
    $status = $row['OrderStatus'];
    // Keep full status string for display but normalize for admin.js action buttons
    $orders[] = [
        "id"            => $row['OrderID'],
        "customer_name" => $row['CustomerName'],
        "email"         => $row['Email'],
        "product"       => $row['ProductName'],
        "quantity"      => $row['Quantity'],
        "total"         => number_format($row['TotalPrice'], 2),
        "status"        => $status,
        "date"          => date("F j, Y", strtotime($row['OrderMade'])),
        "pickup_date"   => $row['OrderShip'] ? date("F j, Y", strtotime($row['OrderShip'])) : null,
        "destination"   => $row['OrderDestination']
    ];
}

echo json_encode($orders);
?>