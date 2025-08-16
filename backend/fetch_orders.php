<?php
//Display orders
header('Content-Type: application/json');

include 'db_connection.php';

$query = "SELECT 
            o.id, 
            p.title AS product_name, 
            o.quantity, 
            o.total_price, 
            o.status, 
            DATE(o.order_date) AS order_date, 
            u.username AS buyer_name
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN products p ON o.product_id = p.id
          ORDER BY o.order_date DESC"; 

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($order = $result->fetch_assoc()) {
    $orders[] = $order;
}

echo json_encode($orders);
?>
