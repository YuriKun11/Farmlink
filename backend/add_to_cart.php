<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

require_once 'db_connection.php'; 

$data = json_decode(file_get_contents('php://input'), true);

$product_id = intval($data['product_id']);
$quantity = intval($data['quantity']);
$total_price = floatval($data['total_price']);
$user_id = $_SESSION['user_id'];

if ($product_id && $quantity > 0 && $total_price >= 0) {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $user_id, $product_id, $quantity, $total_price);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DB insert failed']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
}

$conn->close();
