<?php
//Fetch Products details only
include 'db_connection.php';

$id = $_GET['id'];

$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();

    if (!empty($product['image'])) {
        $product['image_url'] = 'uploads/' . $product['image']; 
    }

    echo json_encode(['status' => 'success', 'product' => $product]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Product not found']);
}

$conn->close();
?>
