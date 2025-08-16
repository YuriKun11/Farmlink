<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['email'])) {
    echo json_encode([
        'status' => 'success',
        'user_id' => $_SESSION['user_id'], 
        'email' => $_SESSION['email'],
        'username' => $_SESSION['username']
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Not logged in'
    ]);
}
?>
