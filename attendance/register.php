<?php
$host = "localhost";
$user = "root";      // change if needed
$pass = "";          // your MySQL password
$db   = "qr_attendance"; // your DB name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

if(isset($_POST['username'], $_POST['password'], $_POST['role'])){
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role     = $conn->real_escape_string($_POST['role']);

    // Prevent duplicate usernames
    $check = $conn->query("SELECT id FROM users WHERE username='$username'");
    if($check->num_rows > 0){
        echo "Username already exists!";
        exit;
    }

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username','$password','$role')";
    if($conn->query($sql) === TRUE){
        echo "Account created successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
$conn->close();
?>
