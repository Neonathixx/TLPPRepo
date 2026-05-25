<?php

require_once 'session_config.php';
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
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
    // Map DB fields to what orders.js expects
    $orders[] = [
        "id"     => $row['OrderID'],
        "date"   => date("F j, Y", strtotime($row['OrderMade'])),
        "status" => $row['OrderStatus'],
        "total"  => number_format($row['TotalPrice'], 2),
        "items"  => [
            [
                "name"  => $row['ProductName'],
                "qty"   => $row['Quantity'],
                "price" => $row['PriceInPHP'],
                "image" => "" // no image path in DB; can be mapped later
            ]
        ]
    ];
}

echo json_encode($orders);
$sql->close();
?>