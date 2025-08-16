<?php
header("Content-Type: application/json");
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "student_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => $conn->connect_error]);
    exit();
}

$result = $conn->query("SELECT * FROM students");
$students = [];

while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode(["status" => "success", "data" => $students]);
$conn->close();
