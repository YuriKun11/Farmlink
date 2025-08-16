<?php
session_start();
include 'db_connection.php';
include 'cors.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
//EMAIL AND PASSWORD CHECKER
if (empty($data['email']) || empty($data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit();
}

$email = $data['email'];
$password = $data['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    if (password_verify($password, $user['password'])) {
        //STORE SESSIONS
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];  
        echo json_encode([
            'status' => 'success',
            'role' => $user['role'],
            'username' => $user['username'] 
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid password.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
}

$stmt->close();
$conn->close();
?>
