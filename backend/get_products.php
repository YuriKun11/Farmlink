<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("127.0.0.1", "root", "", "farmlink_db");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

$result = $conn->query("SELECT * FROM products");

if ($result) {
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    echo json_encode(["status" => "success", "products" => $products]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Failed to fetch products: " . $conn->error]);
}

$conn->close();
?>