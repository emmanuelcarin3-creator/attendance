<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "qr_attendance";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashedPassword, $role);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            // set session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            // redirect based on role
            if ($role === "admin") {
                header("Location: dashboard.php?success=1");
            } elseif ($role === "instructor") {
                header("Location: ins-dashboard.php?success=1");
            }
            exit;
        }
    }

    // login failed
    header("Location: index.html?error=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance System Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="style.css">
<style>
/* Modal styles */
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
}
.modal-content {
    background: #1e293b;
    padding: 20px;
    border-radius: 12px;
    width: 350px;
    color: #fff;
}
.modal-content h3 {margin-bottom: 15px;}
.modal-content label {display:block;margin:10px 0 5px;}
.modal-content input, .modal-content select {
    width: 100%; padding: 10px;
    border-radius: 6px; border: none;
    background: #334155; color: white;
}
.modal-content button {
    margin-top: 15px; padding: 12px;
    width: 100%; background: linear-gradient(to right,#10b981,#059669);
    border: none; border-radius: 8px; color: white; cursor: pointer;
}
.close-btn {float:right;font-size:20px;cursor:pointer;}
.create-link {margin-top:15px;display:block;color:#3b82f6;cursor:pointer;text-decoration:underline;}
</style>
</head>
<body>

<!-- Blurred gradient shapes -->
<div class="blurred-shape shape1"></div>
<div class="blurred-shape shape2"></div>

<div class="login-box">
    <h2>Attendance System Login</h2>
    <form id="loginForm" action="login.php" method="POST">
        <div class="input-group">
            <i class="fa fa-user icon-left"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
            <i class="fa fa-lock icon-left"></i>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <i class="fa fa-eye icon-right" id="togglePassword"></i>
        </div>

        <button type="submit">Login</button>
    </form>
    <span class="create-link" id="openModal">Create Account</span>
</div>

<!-- Modal -->
<div class="modal" id="registerModal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h3>Create Account</h3>
        <label>Username</label>
        <input type="text" id="regUsername">
        
        <label>Password</label>
        <input type="password" id="regPassword">
        
        <label>Confirm Password</label>
        <input type="password" id="regConfirmPassword">
        
        <label>Role</label>
        <select id="regRole">
            <option value="admin">Admin</option>
            <option value="instructor">Instructor</option>
        </select>
        
        <button id="registerBtn">Register</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
const togglePassword = document.querySelector("#togglePassword");
const password = document.querySelector("#password");

togglePassword.addEventListener("click", function () {
    const isPassword = password.getAttribute("type") === "password";
    password.setAttribute("type", isPassword ? "text" : "password");
    this.classList.toggle("fa-eye");
    this.classList.toggle("fa-eye-slash");
});

const params = new URLSearchParams(window.location.search);
if (params.get("success") === "1") {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Successfully Logged In',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: '#0e1a29',
        color: '#48c774',
        didClose: () => {
            window.location.href = "dashboard.php";
        }
    });
} 
else if (params.get("error") === "1") {
    Swal.fire({
        title: 'Login Failed',
        text: 'Invalid username or password.',
        icon: 'error',
        confirmButtonColor: '#d33',
        background: '#0e1a29',
        color: '#fff'
    });
}

// Modal open/close
$("#openModal").click(() => $("#registerModal").fadeIn());
$("#closeModal").click(() => $("#registerModal").fadeOut());

// Register AJAX
$("#registerBtn").click(function(){
    let username = $("#regUsername").val();
    let password = $("#regPassword").val();
    let confirmPass = $("#regConfirmPassword").val();
    let role = $("#regRole").val();

    if(username === "" || password === "" || confirmPass === ""){
        Swal.fire("Error","All fields are required!","error");
        return;
    }
    if(password !== confirmPass){
        Swal.fire("Error","Passwords do not match!","error");
        return;
    }

    $.ajax({
        url: "register.php",
        type: "POST",
        data: {username: username, password: password, role: role},
        success: function(response){
            Swal.fire("Info", response, "success");
            $("#registerModal").fadeOut();
        }
    });
});
</script>

</body>
</html>
