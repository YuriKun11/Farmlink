<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$updates = $data['updates'];

foreach ($updates as $update) {
    $cart_id = intval($update['cart_id']);
    $quantity = intval($update['quantity']);

    // Get price
    $stmt = $conn->prepare("SELECT product_id FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $stmt->bind_result($product_id);
    $stmt->fetch();
    $stmt->close();

    $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($price);
    $stmt->fetch();
    $stmt->close();

    $total_price = $price * $quantity;

    $stmt = $conn->prepare("UPDATE cart SET quantity = ?, total_price = ? WHERE id = ?");
    $stmt->bind_param("idi", $quantity, $total_price, $cart_id);
    $stmt->execute();
    $stmt->close();
}

echo json_encode(['status' => 'success']);
?>
