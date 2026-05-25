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

if (!$orderID) {
    echo json_encode(["error" => "No order ID provided"]);
    exit();
}

$details = $conn->prepare("
    SELECT o.OrderID, o.TotalPrice, o.OrderShip, o.OrderDestination,
           u.Name, u.Email, p.ProductName, o.Quantity
    FROM Orders o
    JOIN Users u ON u.UserID = o.UserID
    JOIN Products p ON p.ProductID = o.ProductID
    WHERE o.OrderID = ?
");
$details->bind_param("i", $orderID);
$details->execute();
$order = $details->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(["error" => "Order not found"]);
    exit();
}

$sql = $conn->prepare("
    UPDATE Orders SET OrderStatus = 'Payment Approved'
    WHERE OrderID = ? AND OrderStatus = 'Pending Confirmation'
");
$sql->bind_param("i", $orderID);

if ($sql->execute() && $sql->affected_rows > 0) {
    echo json_encode([
        "success"        => true,
        "receipt_type"   => "approved",
        "order_id"       => $order['OrderID'],
        "customer"       => $order['Name'],
        "email"          => $order['Email'],
        "product"        => $order['ProductName'],
        "quantity"       => $order['Quantity'],
        "total_paid"     => $order['TotalPrice'],
        "pickup_date"    => $order['OrderShip'] ? date("F j, Y", strtotime($order['OrderShip'])) : null,
        "message"        => "Please pick up by: " . ($order['OrderShip'] ? date("F j, Y", strtotime($order['OrderShip'])) : 'the agreed date')
    ]);
} else {
    echo json_encode(["error" => "Could not confirm payment.", "message" => "Could not confirm payment."]);
}

$sql->close();
$details->close();
