<?php
// dashboard.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Attendance Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;600&display=swap" rel="stylesheet">
<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    :root {
        --bg: #0e1117;
        --text: #eee;
        --card-bg: #171b26;
        --sidebar-bg: #1c1f2a;
        --hover-bg: #2a2e42;
        --present-color: #48c774;
        --absent-color: #f14668;
    }
    body.light {
        --bg: #f5f5f5;
        --text: #111;
        --card-bg: #fff;
        --sidebar-bg: #ddd;
        --hover-bg: #bbb;
        --present-color: #2d7a2d;
        --absent-color: #c82333;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Fira Code', monospace; transition: all 0.3s ease; }
    body { background-color: var(--bg); color: var(--text); display: flex; min-height: 100vh; overflow-x: hidden; }

    /* Sidebar */
    #sidebar {
        position: fixed;
        top: 0; left: -240px;
        width: 240px; height: 100%;
        background-color: var(--sidebar-bg);
        transition: left 0.3s ease, background-color 0.3s ease;
        padding-top: 70px;
        z-index: 1000;
        border-right: 1px solid #2a2e42;
    }
    #sidebar.active { left: 0; }
    #sidebar ul { list-style: none; }
    #sidebar ul li {
        padding: 18px 30px;
        cursor: pointer;
        border-radius: 8px;
        margin: 5px 10px;
        transition: background 0.2s;
    }
    #sidebar ul li:hover { background-color: var(--hover-bg); }

    /* Burger */
    #burger {
        position: fixed;
        top: 20px; left: 20px;
        font-size: 26px;
        cursor: pointer;
        color: var(--present-color);
        z-index: 1100;
        transition: transform 0.2s;
    }
    #burger:hover { transform: scale(1.1); }

    #modeToggle {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--card-bg);
        border: none;
        border-radius: 50%;
        padding: 10px 12px;
        cursor: pointer;
        color: var(--text);
        font-size: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        transition: all 0.3s ease;
        z-index: 1100;
    }
    #modeToggle:hover {
        transform: scale(1.1);
    }

    /* Main content */
    #main {
        flex: 1;
        margin-left: 0;
        padding: 30px 40px;
        transition: margin-left 0.3s ease-in-out;
    }
    #sidebar.active ~ #main { margin-left: 240px; }

    #main h1 { margin-bottom: 20px; font-weight: 600; color: var(--present-color); text-shadow: 1px 1px 2px #000; transition: color 0.3s; }

    /* Summary cards */
    .summary-cards { display: flex; gap: 20px; margin-bottom: 40px; flex-wrap: wrap; }
    .card {
        background: var(--card-bg);
        padding: 25px 30px;
        border-radius: 12px;
        flex: 1 1 200px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.6);
        transition: transform 0.2s, box-shadow 0.2s, background-color 0.3s;
    }
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.7);
    }
    .card h3 { margin-bottom: 12px; color: var(--present-color); font-weight: 600; font-size: 16px; }
    .card p { font-size: 28px; font-weight: 700; }

    /* Chart */
    canvas { background: var(--card-bg); border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.5); transition: background 0.3s; }

    /* Modals */
    .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.75); animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from {opacity:0;} to{opacity:1;} }
    .modal-content {
        background-color: var(--card-bg);
        margin: 8% auto;
        padding: 25px;
        border-radius: 15px;
        width: 90%;
        max-width: 650px;
        color: var(--text);
        position: relative;
        animation: slideDown 0.3s ease-out;
        transition: background-color 0.3s, color 0.3s;
    }
    @keyframes slideDown { from { transform: translateY(-50px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .close { position: absolute; top: 12px; right: 18px; color: var(--text); font-size: 26px; font-weight: bold; cursor: pointer; transition: color 0.3s; }

    /* Table */
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { padding: 12px; border: 1px solid #2a2e42; text-align: left; transition: border-color 0.3s; }
    th { background-color: #2a2e42; }
    tbody tr:hover { background-color: var(--hover-bg); transition: background 0.2s; }

    /* QR Reader */
    #qr-reader { width: 100%; margin-top: 15px; border-radius: 12px; overflow: hidden; }

</style>
</head>
<body>

<!-- Burger button -->
<div id="burger">&#9776;</div>
<!-- Light/Dark mode toggle button -->
<button id="modeToggle" title="Toggle Dark/Light Mode">
    <i id="modeIcon" class="fa-solid fa-moon"></i>
</button>
<!-- Sidebar -->
<div id="sidebar">
    <ul>
        <li id="viewRecordsBtn">View Record</li>
        <li id="scanQrBtn">Scan QR Code</li>
    </ul>
</div>

<!-- Main content -->
<div id="main">
    <h1>Attendance Dashboard</h1>

    <!-- Summary cards -->
    <div class="summary-cards">
        <div class="card">
            <h3>Total Present</h3>
            <p id="totalPresent">0</p>
        </div>
        <div class="card">
            <h3>Total Absent</h3>
            <p id="totalAbsent">0</p>
        </div>
    </div>

    <!-- Attendance Chart -->
    <canvas id="attendanceChart" width="400" height="220"></canvas>
</div>

<!-- View Records Modal -->
<div id="viewRecordsModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeRecords">&times;</span>
        <h2>Attendance Records</h2>
        <table id="recordsTable">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                <tr><td>1</td><td>John Doe</td><td>2025-08-16</td><td>Present</td></tr>
                <tr><td>2</td><td>Jane Smith</td><td>2025-08-16</td><td>Absent</td></tr>
                    <tr><td>3</td><td>Mark Lee</td><td>2025-08-16</td><td>Present</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Scan QR Code Modal -->
<div id="scanQrModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeQr">&times;</span>
        <h2>Scan QR Code</h2>
        <div id="qr-reader"></div>
        <p>Scanned Code: <span id="qr-result">...</span>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
    // Sidebar toggle
    const burger = document.getElementById('burger');
    const sidebar = document.getElementById('sidebar');
    burger.addEventListener('click', () => sidebar.classList.toggle('active'));

    // Light/Dark mode
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    if (localStorage.getItem('mode') === 'light') {
        document.body.classList.add('light');
        modeIcon.classList.replace('fa-moon', 'fa-sun');
    }
    modeToggle.addEventListener('click', () => {
        document.body.classList.toggle('light');
        if (document.body.classList.contains('light')) {
            modeIcon.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('mode', 'light');
        } else {
            modeIcon.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('mode', 'dark');
        }
    });

    // Modal logic
    const viewRecordsBtn = document.getElementById('viewRecordsBtn');
    const scanQrBtn = document.getElementById('scanQrBtn');
    const viewRecordsModal = document.getElementById('viewRecordsModal');
    const scanQrModal = document.getElementById('scanQrModal');
    const closeRecords = document.getElementById('closeRecords');
    const closeQr = document.getElementById('closeQr');

    viewRecordsBtn.onclick = () => viewRecordsModal.style.display = 'block';
    closeRecords.onclick = () => viewRecordsModal.style.display = 'none';

    scanQrBtn.onclick = () => {
        scanQrModal.style.display = 'block';
        setTimeout(startQrScanner, 300); // delay so modal is fully shown
    };
    closeQr.onclick = () => {
        scanQrModal.style.display = 'none';
        stopQrScanner();
    };
    window.onclick = (e) => {
        if (e.target == viewRecordsModal) viewRecordsModal.style.display = 'none';
        if (e.target == scanQrModal) {
            scanQrModal.style.display = 'none';
            stopQrScanner();
        }
    };

    // Attendance summary
    const tableRows = document.querySelectorAll("#recordsTable tbody tr");
    let totalPresent = 0, totalAbsent = 0;
    tableRows.forEach(row => {
        const status = row.cells[3].innerText.toLowerCase();
        if (status === 'present') totalPresent++;
        else if (status === 'absent') totalAbsent++;
    });
    document.getElementById('totalPresent').innerText = totalPresent;
    document.getElementById('totalAbsent').innerText = totalAbsent;

    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Present', 'Absent'],
            datasets: [{
                data: [totalPresent, totalAbsent],
                backgroundColor: ['#48c774', '#f14668'],
                borderColor: ['var(--bg)', 'var(--bg)'],
                borderWidth: 2
            }]
        },
        options: {
            plugins: { legend: { labels: { color: 'var(--text)' } } },
            responsive: true
        }
    });
</script>
<script>
    // QR Scanner logic
    let html5QrCode = null;

    function startQrScanner() {
        const qrReaderId = "qr-reader";

        // Stop existing scanner if already running
        if (html5QrCode) {
            stopQrScanner();
        }

        html5QrCode = new Html5Qrcode(qrReaderId);

        html5QrCode.start(
            { facingMode: "environment" }, // back camera on phones
            { fps: 10, qrbox: { width: 250, height: 250 } },
            qrCodeMessage => {
                // ✅ Display scanned text
                document.getElementById("qr-result").innerText = qrCodeMessage;

                // (optional) stop scanner automatically after a successful scan
                // stopQrScanner();
                // scanQrModal.style.display = 'none';
            },
            errorMessage => {
                // You can log scanning errors here if needed
                // console.log("Scanning error:", errorMessage);
            }
        ).catch(err => {
            console.error("QR Scanner error:", err);
            alert("Unable to access camera. Please check permissions.");
        });
    }

    function stopQrScanner() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                html5QrCode.clear();
                html5QrCode = null;
            }).catch(err => {
                console.error("Failed to stop scanner:", err);
            });
        }
    }
</script>

</body>
</html>
