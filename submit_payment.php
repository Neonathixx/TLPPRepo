<?php

session_start();
header('Content-Type: application/json');
include 'connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Not logged in"]);
    exit();
}

$userID  = $_SESSION['user_id'];
$orderID = (int)($_POST['order_id'] ?? 0);

if (!$orderID) {
    echo json_encode(["error" => "No order ID provided"]);
    exit();
}

// Handle receipt file upload
if (!isset($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["error" => "No receipt file uploaded"]);
    exit();
}

$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
$fileType     = $_FILES['receipt']['type'];

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(["error" => "Invalid file type. Only JPG, PNG, or PDF allowed."]);
    exit();
}

// Save the file
$uploadDir  = 'uploads/receipts/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$fileName   = $orderID . '_' . time() . '_' . basename($_FILES['receipt']['name']);
$uploadPath = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES['receipt']['tmp_name'], $uploadPath)) {
    echo json_encode(["error" => "Failed to save receipt file"]);
    exit();
}

// Update order status
$sql = $conn->prepare("
    UPDATE Orders
    SET OrderStatus = 'Pending Confirmation'
    WHERE OrderID = ? AND UserID = ? AND OrderStatus = 'Pending Payment'
");
$sql->bind_param("ii", $orderID, $userID);

if ($sql->execute() && $sql->affected_rows > 0) {
    echo json_encode(["success" => true, "receipt_file" => $fileName]);
} else {
    echo json_encode(["error" => "Could not update order status."]);
}

$sql->close();
