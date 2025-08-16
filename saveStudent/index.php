<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "student_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "data" => [
            "title" => "Connection Failed",
            "message" => $conn->connect_error
        ]
    ]);
    exit();
}

$input = json_decode(file_get_contents("php://input"), true);

$requiredFields = ['idNumber', 'lastName', 'firstName', 'middleName', 'contactNumber', 'email'];
foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        echo json_encode([
            "status" => "error",
            "data" => [
                "title" => "Missing Fields",
                "message" => "Please fill in all required fields. Missing: $field"
            ]
        ]);
        exit();
    }
}

$id = $conn->real_escape_string($input['idNumber']);
$lname = $conn->real_escape_string($input['lastName']);
$fname = $conn->real_escape_string($input['firstName']);
$mname = $conn->real_escape_string($input['middleName']);
$contact = $conn->real_escape_string($input['contactNumber']);
$email = $conn->real_escape_string($input['email']);

$sql = "INSERT INTO students (id_number, last_name, first_name, middle_name, contact_number, email)
        VALUES ('$id', '$lname', '$fname', '$mname', '$contact', '$email')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        "status" => "success",
        "data" => [
            "message" => "Student saved successfully!"
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "data" => [
            "title" => "Database Error",
            "message" => $conn->error
        ]
    ]);
}

$conn->close();
