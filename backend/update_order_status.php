<?php
//Update sa status ng order if Delivered, Pending, etc...
require 'db_connection.php'; 

$data = json_decode(file_get_contents("php://input"), true);
$id = intval($data['id']);
$status = $data['status'];

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error"]);
}
?>
