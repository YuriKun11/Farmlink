<?php
//DB Config for fetching username
//IDK why i need to separate this, but it works this way haha XXX
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "farmlink_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => $conn->connect_error]);
    exit();
}

?>