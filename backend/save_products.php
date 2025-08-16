<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if (
    isset($_SESSION['user_id'], $_POST['title'], $_POST['description'], $_POST['status'],
          $_POST['category'], $_POST['unit'], $_POST['price'], $_POST['quantity']) &&
    isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK
) {
    $conn = new mysqli("localhost", "root", "", "farmlink_db");

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Database connection failed: " . $conn->connect_error
        ]);
        exit;
    }

    $userId = $_SESSION['user_id'];

    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileType = $_FILES['image']['type'];

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(["status" => "error", "message" => "Invalid image file type."]);
        exit;
    }

    $newFileName = uniqid('product_', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
    $uploadDir = 'uploads/products/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $uploadPath)) {
        $imagePath = $uploadPath;

        $stmt = $conn->prepare("INSERT INTO products (title, description, status, category, unit, price, quantity, image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "ssssssdsi",
            $_POST['title'],
            $_POST['description'],
            $_POST['status'],
            $_POST['category'],
            $_POST['unit'],
            $_POST['price'],
            $_POST['quantity'],
            $imagePath,
            $userId
        );

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Product added successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add product."]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to upload image."]);
        exit;
    }

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing required fields or not logged in."]);
}
?>