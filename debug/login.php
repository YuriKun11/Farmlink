<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "farmlink_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        // Set session using email
        $_SESSION['email'] = $row['email'];
        $_SESSION['username'] = $row['username']; // optional
        header("Location: welcome.html");
        exit;
    } else {
        echo "Invalid email or password.";
    }
} else {
    echo "User not found.";
}

$stmt->close();
$conn->close();
?>
