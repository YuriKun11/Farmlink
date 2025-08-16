<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "farmlink_db";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users from the database
$sql = "SELECT id, username, email, role, created_at FROM users";
$result = $conn->query($sql);

$users = [];

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} 

// Set the content-type to JSON and output the results
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'data' => $users]);

// Close the connection
$conn->close();
?>
