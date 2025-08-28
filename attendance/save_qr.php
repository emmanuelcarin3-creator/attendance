<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Your DB password
$db = 'qr_attendance';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'DB connection failed']));
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['name'], $data['id'], $data['class'], $data['date'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
    exit;
}

$name = $conn->real_escape_string($data['name']);
$id = $conn->real_escape_string($data['id']);
$class = $conn->real_escape_string($data['class']);
$date = $conn->real_escape_string($data['date']);

$sql = "INSERT INTO qr_records (instructor_name, instructor_id, subject, generated_date)
        VALUES ('$name', '$id', '$class', '$date')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error]);
}

$conn->close();
?>
