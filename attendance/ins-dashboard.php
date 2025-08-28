
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --bg: #121212;
    --text: #eee;
    --sidebar-bg: #1f1f1f;
    --hover-bg: #2a2e42;
    --card-bg: #1c1f26;
}
body.light {
    --bg: #f5f5f5;
    --text: #111;
    --sidebar-bg: #ddd;
    --hover-bg: #bbb;
    --card-bg: #fff;
}
* { box-sizing:border-box; margin:0; padding:0; font-family:'Fira Code', monospace; transition: all 0.3s ease; }
body { background:var(--bg); color:var(--text); display:flex; min-height:100vh; overflow-x:hidden; }

/* Sidebar */
#sidebar {
    position: fixed; top:0; left:-240px; width:240px; height:100%; background:var(--sidebar-bg); padding-top:70px; transition:0.3s; z-index:1000; border-right:1px solid #2a2e42;
}
#sidebar.active { left:0; }
#sidebar ul { list-style:none; }
#sidebar ul li { padding:18px 25px; cursor:pointer; border-radius:8px; margin:5px 10px; transition:0.2s; }
#sidebar ul li:hover { background:var(--hover-bg); }

/* Burger */
#burger { position:fixed; top:20px; left:20px; font-size:26px; cursor:pointer; z-index:1100; color: var(--text);}
#burger:hover{ transform:scale(1.1); }

/* Main content */
#main { flex:1; margin-left:0; padding:30px; transition:0.3s; }
#sidebar.active ~ #main { margin-left:240px; }

/* Button toggle for dark/light */
#modeToggle { position:fixed; top:20px; right:20px; background:var(--card-bg); border:none; border-radius:50%; padding:10px 12px; cursor:pointer; color:var(--text); font-size:20px; box-shadow:0 4px 12px rgba(0,0,0,0.4); z-index:1100; transition:0.3s; }
#modeToggle:hover{ transform:scale(1.1); }

/* Modals */
.modal { display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.75); }
.modal-content { background:var(--card-bg); margin:8% auto; padding:25px; border-radius:15px; width:90%; max-width:600px; color:var(--text); position:relative; }
.close { position:absolute; top:12px; right:18px; font-size:26px; font-weight:bold; cursor:pointer; }

/* Simple form styles for demo */
.modal-content input, .modal-content button { padding:10px; margin:5px 0; width:100%; border-radius:5px; border:none; }
.modal-content button { background:#48c774; color:#fff; cursor:pointer; font-weight:bold; transition:0.2s; }
.modal-content button:hover{ opacity:0.9; }
/* Make form look cleaner */
  #registerStudentForm {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  #registerStudentForm label {
    font-weight: bold;
    margin-top: 8px;
  }

  #registerStudentForm input,
  #registerStudentForm select {
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #ccc;
  }

  #registerStudentForm button {
    margin-top: 15px;
    padding: 10px;
    background: #007bff;
    border: none;
    color: white;
    border-radius: 5px;
    cursor: pointer;
  }

  #registerStudentForm button:hover {
    background: #0056b3;
  }

</style>
</head>
<body>

<!-- Burger -->
<div id="burger">&#9776;</div>

<!-- Light/Dark mode toggle -->
<button id="modeToggle"><i id="modeIcon" class="fa-solid fa-moon"></i></button>

<!-- Sidebar --><!-- Sidebar -->
<div id="sidebar">
    <ul>
        <li id="btnGenerate">Generate QR Code</li>
        <li id="btnAttendance">Register Students</li>
        <li id="btnRecords">Manage Records</li>
        <!-- ✅ Logout inside sidebar -->
        <li id="logoutBtn" style="color:#ff4d4d; font-weight:bold;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </li>
    </ul>
</div>


<!-- Main content -->
<div id="main">
    
        <h2 id="instructorGreeting" style="margin-top:15px; color:#48c774;">HELLO WELCOME INSTRUCTOR!</h2>
<!-- 👇 Replace your PH Time placeholder -->
<p id="phTime" 
   style="margin-top:20px; 
          font-size:22px; 
          font-weight:bold; 
          color:#00d4ff; 
          background:rgba(255,255,255,0.05); 
          padding:12px 18px; 
          border-radius:12px; 
          display:inline-block; 
          box-shadow:0 4px 12px rgba(0,0,0,0.3);">
</p>


</div>

<!-- Generate QR Code Modal -->
<div id="modalGenerate" class="modal">
    <div class="modal-content">
        <span class="close" id="closeGenerate">&times;</span>
        <h2 style="text-align:center; margin-bottom:20px; color:#48c774;">Generate QR Code</h2>
        <div style="display:flex; flex-direction:column; align-items:center; gap:15px;">

            <!-- Input Fields -->
            <input id="studentName" type="text" placeholder="Instructor Name" style="width:80%; padding:12px 15px; border-radius:8px; border:none; box-shadow:0 4px 12px rgba(0,0,0,0.3); font-size:16px;">
            <input id="studentID" type="text" placeholder="Instructor ID" style="width:80%; padding:12px 15px; border-radius:8px; border:none; box-shadow:0 4px 12px rgba(0,0,0,0.3); font-size:16px;">
            <input id="classSection" type="text" placeholder="Subject" style="width:80%; padding:12px 15px; border-radius:8px; border:none; box-shadow:0 4px 12px rgba(0,0,0,0.3); font-size:16px;">

            <!-- Generate Button -->
            <button id="generateBtn" style="width:80%; padding:12px; border:none; border-radius:8px; background:linear-gradient(90deg,#48c774,#1b9b4f); color:#fff; font-weight:bold; font-size:16px; cursor:pointer; box-shadow:0 4px 12px rgba(0,0,0,0.3); transition:0.2s;">Generate QR</button>

            <!-- Display QR Code -->
            <div id="qrCode" style="margin-top:20px; background:#fff; padding:15px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.3); display:flex; justify-content:center;"></div>

            <!-- Display Info -->
            <div id="qrInfo" style="margin-top:15px; background:var(--card-bg); padding:12px 15px; border-radius:8px; width:80%; text-align:left; color:var(--text); box-shadow:0 4px 12px rgba(0,0,0,0.3);"></div>

        </div>
    </div>
</div>


<!-- Manage Attendance Modal -->
<div id="modalAttendance" class="modal">
  <div class="modal-content">
    <span class="close" id="closeAttendance">&times;</span>
    <h2>Register Student</h2>

    <form id="registerStudentForm">
      <!-- Full Name -->
      <label for="studentName">Full Name:</label>
      <input type="text" id="studentName" name="studentName" required>

      <!-- Username -->
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>

      <!-- Password -->
      <label for="password">Password:</label>
      <div style="position:relative;">
        <input type="password" id="password" name="password" required style="width:100%; padding-right:35px;">
        <span id="togglePassword" 
              style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer; font-size:18px;">
          🙈
        </span>
      </div>

      <!-- Year Level -->
      <label for="yearLevel">Year Level:</label>
      <select id="yearLevel" name="yearLevel" required>
        <option value="1st Year">1st Year</option>
        <option value="2nd Year">2nd Year</option>
        <option value="3rd Year">3rd Year</option>
        <option value="4th Year">4th Year</option>
      </select>

      <!-- Course (default BSIT) -->
      <label for="course">Course:</label>
      <select id="course" name="course" required>
        <option value="BSIT" selected>BSIT</option>
        <option value="BSCRIM">BSCRIM</option>
        <option value="BAELS">BAELS</option>
        <option value="BSSW">BSSW</option>
      </select>

      <!-- Submit Button -->
      <button type="submit">Register</button>
    </form>
  </div>
</div>


<!-- Manage Records Modal -->
<div id="modalRecords" class="modal">
    <div class="modal-content">
        <span class="close" id="closeRecords">&times;</span>
        <h2>Manage Records</h2>
        <p>Attendance records management content goes here.</p>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
// ✅ Logout confirmation
document.getElementById("logoutBtn").addEventListener("click", function() {
    const confirmLogout = confirm("Are you sure you want to Logout?");
    if (confirmLogout) {
        // 👉 Redirect to logout page (change 'logout.php' if needed)
        window.location.href = "login.php";
    }
});
</script>   

<script>
// Sidebar toggle
const burger = document.getElementById('burger');
const sidebar = document.getElementById('sidebar');
burger.addEventListener('click', () => sidebar.classList.toggle('active'));

// Dark/Light mode toggle with localStorage
const modeToggle = document.getElementById('modeToggle');
const modeIcon = document.getElementById('modeIcon');
if(localStorage.getItem('mode')==='light'){ document.body.classList.add('light'); modeIcon.classList.replace('fa-moon','fa-sun'); }
modeToggle.addEventListener('click', () => {
    document.body.classList.toggle('light');
    if(document.body.classList.contains('light')){ modeIcon.classList.replace('fa-moon','fa-sun'); localStorage.setItem('mode','light'); }
    else{ modeIcon.classList.replace('fa-sun','fa-moon'); localStorage.setItem('mode','dark'); }
});

// Modal logic
function setupModal(buttonId, modalId, closeId){
    const btn = document.getElementById(buttonId);
    const modal = document.getElementById(modalId);
    const close = document.getElementById(closeId);
    btn.onclick = ()=> modal.style.display='block';
    close.onclick = ()=> modal.style.display='none';
    window.onclick = e=>{ if(e.target==modal) modal.style.display='none'; }
}

setupModal('btnGenerate','modalGenerate','closeGenerate');
setupModal('btnAttendance','modalAttendance','closeAttendance');
setupModal('btnRecords','modalRecords','closeRecords');
const generateBtn = document.getElementById('generateBtn');
const qrCodeDiv = document.getElementById('qrCode');
const qrInfoDiv = document.getElementById('qrInfo');

generateBtn.addEventListener('click', () => {
    const name = document.getElementById('studentName').value.trim();
    const id = document.getElementById('studentID').value.trim();
    const cls = document.getElementById('classSection').value.trim();
    const date = new Date().toLocaleDateString();

    if(!name || !id || !cls){
        alert('Please fill in all fields');
        return;
    }

    // Combine info for QR code
    const qrData = `Name: ${name}\nID: ${id}\nClass: ${cls}\nDate: ${date}`;

    // Clear previous QR code
    qrCodeDiv.innerHTML = '';

    // Generate QR code
    new QRCode(qrCodeDiv, {
        text: qrData,
        width: 200,
        height: 200,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    // Display info below QR
    qrInfoDiv.innerHTML = `<strong>Name:</strong> ${name}<br>
                           <strong>ID:</strong> ${id}<br>
                           <strong>Class:</strong> ${cls}<br>
                           <strong>Date:</strong> ${date}`;

    // ✅ Send data to PHP to store in DB
    fetch('save_qr.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            name: name,
            id: id,
            class: cls,
            date: new Date().toISOString().split('T')[0] // format: YYYY-MM-DD
        })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success'){
            console.log("QR data saved successfully.");
        } else {
            console.error("Failed to save QR data:", data.message);
        }
    });
});


</script>

<script>
  // Toggle Password Visibility with Icon
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("password");

  togglePassword.addEventListener("click", function () {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      this.textContent = "👁️"; // Change icon when visible
    } else {
      passwordInput.type = "password";
      this.textContent = "🙈"; // Change icon back
    }
  });
</script>
<script>
document.getElementById("registerStudentForm").addEventListener("submit", function(e) {
  e.preventDefault(); // prevent reload

  const formData = {
    full_name: document.getElementById("studentName").value,
    username: document.getElementById("username").value,
    password: document.getElementById("password").value,
    year_level: document.getElementById("yearLevel").value,
    course: document.getElementById("course").value
  };

  fetch("register_student.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(formData)
  })
  .then(res => res.json())
  .then(data => {
    if(data.status === "success"){
      alert("✅ Student registered successfully!");
      document.getElementById("registerStudentForm").reset();
    } else {
      alert("❌ Error: " + data.message);
    }
  })
  .catch(err => console.error("Fetch Error:", err));
});
</script>

<script>
// Aesthetic Real-time PH Clock
function updatePHClock() {
    const options = {
        timeZone: "Asia/Manila",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true
    };
    const dateOptions = {
        timeZone: "Asia/Manila",
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric"
    };

    const time = new Date().toLocaleTimeString("en-PH", options);
    const date = new Date().toLocaleDateString("en-PH", dateOptions);

    document.getElementById("phTime").innerHTML = `
        <span style="font-size:26px; letter-spacing:2px;">🕒 ${time}</span><br>
        <span style="font-size:16px; opacity:0.8;">${date}</span>
    `;
}
// Update every second
setInterval(updatePHClock, 1000);
updatePHClock();
</script>



</body>
</html>
