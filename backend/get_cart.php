<?php
//FETCH CART ITEMS
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

require_once 'db_connection.php'; 

$user_id = $_SESSION['user_id'];

$query = "
    SELECT c.id AS cart_id, c.quantity, c.total_price, 
           p.id AS product_id, p.title, p.price, p.image AS image_url
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ? AND c.status = 'Pending'
";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

echo json_encode(['status' => 'success', 'cart_items' => $cart_items]);

$stmt->close();
$conn->close();
