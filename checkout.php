<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

// cart.js sends JSON body
$input = json_decode(file_get_contents('php://input'), true);
$items = $input['items'] ?? [];

if (!$items || count($items) === 0) {
    echo json_encode(["error" => "Cart is empty"]);
    exit();
}

$userID     = $_SESSION['user_id'];
$ordersMade = [];

foreach ($items as $item) {
    // cart.js stores items with: id, name, price, qty
    $productID   = (int)($item['id'] ?? 0);
    $quantity    = (int)($item['qty'] ?? 1);
    $priceInPHP  = (float)($item['price'] ?? 0);
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
