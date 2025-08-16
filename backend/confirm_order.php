<?php
include 'db_connection.php'; 

session_start();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$cart_query = $conn->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();

if ($cart_result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty.']);
    exit;
}

$conn->begin_transaction();

try {
    while ($row = $cart_result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $status = "Pending";
        $order_date = date('Y-m-d H:i:s');

        $price_query = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $price_query->bind_param("i", $product_id);
        $price_query->execute();
        $price_result = $price_query->get_result();
        $price_row = $price_result->fetch_assoc();
        $price = $price_row['price'] ?? 0;
        $total_price = $price * $quantity;

        $insert_order = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, total_price, status, order_date)
                                        VALUES (?, ?, ?, ?, ?, ?)");
        $insert_order->bind_param("iiidss", $user_id, $product_id, $quantity, $total_price, $status, $order_date);
        $insert_order->execute();
        $update_product = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
        $update_product->bind_param("ii", $quantity, $product_id);
        $update_product->execute();
    }
    $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $delete_cart->bind_param("i", $user_id);
    $delete_cart->execute();

    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Order placed with Pending status and total price.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage()]);
}
?>
