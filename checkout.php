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

$userID   = $_SESSION['user_id'];
$cartJSON = $_POST['cart'] ?? null;

if (!$cartJSON) {
    echo json_encode(["error" => "No cart data received"]);
    exit();
}

$cart = json_decode($cartJSON, true);

if (!$cart || count($cart) === 0) {
    echo json_encode(["error" => "Cart is empty"]);
    exit();
}

$ordersMade = [];

foreach ($cart as $item) {
    $productID   = (int)$item['ProductID'];
    $quantity    = (int)$item['Quantity'];
    $priceInPHP  = (float)$item['PriceInPHP'];
    $totalPrice  = $priceInPHP * $quantity;
    $orderStatus = "Filing Form";
    $orderMade   = date("Y-m-d H:i:s");

    $sql = $conn->prepare("
        INSERT INTO Orders (UserID, ProductID, PriceInPHP, Quantity, OrderStatus, OrderMade, TotalPrice)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $sql->bind_param("iidissd", $userID, $productID, $priceInPHP, $quantity, $orderStatus, $orderMade, $totalPrice);

    if ($sql->execute()) {
        $ordersMade[] = $conn->insert_id;
    } else {
        echo json_encode(["error" => "Failed to create order: " . $sql->error]);
        exit();
    }

    $sql->close();
}

echo json_encode(["success" => true, "order_ids" => $ordersMade]);
