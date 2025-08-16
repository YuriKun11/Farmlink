<?php
session_start();
header('Content-Type: application/json');

require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$input_data = json_decode(file_get_contents('php://input'), true);

if (isset($input_data['id']) && isset($input_data['price']) && isset($input_data['quantity'])) {
    $user_id = $_SESSION['user_id']; 
    $product_id = $input_data['id']; 
    $quantity = $input_data['quantity'];
    $total_price = $input_data['price'] * $quantity; 
    $status = 'Pending'; 
    $order_date = date('Y-m-d H:i:s');
    
    $conn->begin_transaction();

    try {
        $query = "INSERT INTO orders (user_id, product_id, quantity, total_price, status, order_date)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiidss", $user_id, $product_id, $quantity, $total_price, $status, $order_date);

        if (!$stmt->execute()) {
            throw new Exception("Failed to save order");
        }
        $update_query = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ii", $quantity, $product_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update product quantity");
        }
        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'Order saved successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    $stmt->close();
    $update_stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
}

$conn->close();
?>
