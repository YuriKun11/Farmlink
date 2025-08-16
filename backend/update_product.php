<?php
//update feature pero walang image update kasi mahirap
include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"));

$id = $data->id;
$title = $data->title;
$description = $data->description;
$price = $data->price;
$quantity = $data->quantity;

$query = "UPDATE products SET title = ?, description = ?, price = ?, quantity = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ssdii', $title, $description, $price, $quantity, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update product']);
}

$conn->close();
?>


