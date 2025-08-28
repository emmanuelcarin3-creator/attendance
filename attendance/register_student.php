<?php
header("Content-Type: application/json");

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "qr_attendance";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(["status"=>"error","message"=>"DB Connection failed: " . $conn->connect_error]);
    exit;
}

// Get JSON data from fetch
$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["status"=>"error","message"=>"Invalid input"]);
    exit;
}

// Sanitize input
$full_name = $conn->real_escape_string($data['full_name']);
$username  = $conn->real_escape_string($data['username']);
$password  = password_hash($data['password'], PASSWORD_BCRYPT);
$year      = $conn->real_escape_string($data['year_level']);
$course    = $conn->real_escape_string($data['course']);

// Insert into DB
$sql = "INSERT INTO students (full_name, username, password, year_level, course) 
        VALUES ('$full_name','$username','$password','$year','$course')";

if($conn->query($sql) === TRUE){
    echo json_encode(["status"=>"success"]);
} else {
    echo json_encode(["status"=>"error","message"=>$conn->error]);
}

$conn->close();
