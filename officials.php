<?php
session_start();

// ✅ PDO Connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=pamanlinan_db', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ✅ Optional: Require user to be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Pamanlinan | City of Bislig</title>
  <link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background-color: #62cd827d;
      color: #222;
    }

    header {
      background-color: #0b6b29;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 5%;
      flex-wrap: wrap;
    }

    header img {
      height: 60px;
    }

    header h1 {
      font-size: 1.4rem;
      font-weight: 600;
      margin: 0;
    }

    .time {
      font-size: 0.9rem;
      text-align: right;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 2rem;
      padding: 40px 5%;
      flex-wrap: wrap;
    }

    .sidebar {
      flex: 1 1 250px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 20px;
    }

    .sidebar h3 {
      background: #0b6b29;
      color: white;
      padding: 10px;
      border-radius: 6px;
      margin-top: 0;
      font-size: 1rem;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 10px 0 0 0;
    }

    .sidebar ul li {
      border-bottom: 1px solid #eee;
    }

    .sidebar ul li a {
      text-decoration: none;
      color: #0b6b29;
      display: block;
      padding: 10px 0;
      transition: 0.3s;
    }

    .sidebar ul li a:hover {
      color: #037a2b;
      font-weight: 500;
    }

    .content {
      flex: 2 1 600px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 30px;
      position: relative;
    }

    .content h2 {
      font-size: 1.8rem;
      color: #0b6b29;
      border-bottom: 3px solid #0b6b29;
      display: inline-block;
      margin-bottom: 20px;
    }

    .logo {
      position: absolute;
      top: 30px;
      right: 30px;
      width: 120px;
      height: auto;
    }

    .info {
      margin-bottom: 20px;
    }

    .info p {
      margin: 5px 0;
      font-weight: 500;
    }

    .officials h3 {
      color: #0b6b29;
      margin-top: 20px;
    }

    .officials p {
      margin: 3px 0;
    }

    .social {
      margin-top: 25px;
    }

    .social a {
      display: inline-block;
      margin-right: 10px;
      background: #ffcc00;
      color: #333;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      font-size: 18px;
      transition: 0.3s;
    }

    .social a:hover {
      background: #ffdb4d;
    }

    footer {
      text-align: center;
      font-size: 0.9rem;
      color: #110404ff;
      margin: 30px 0;
    }

    @media (max-width: 768px) {
      .content h2 {
        font-size: 1.5rem;
      }
      .logo {
        position: static;
        display: block;
        margin: 0 auto 20px auto;
      }
      .container {
        flex-direction: column;
      }
    }

    .back-btn {
      display:inline-block;
      margin-bottom:15px;
      background:#0b6b29;
      color:white;
      padding:8px 14px;
      border-radius:8px;
      text-decoration:none;
      font-weight:500;
    }
  </style>
</head>
<body>

  <header>
    <div style="display:flex;align-items:center;gap:10px;">
      <img src="pamanlinan.png" alt="City Seal">
      <div>
        <h1>City of Bislig, Surigao del Sur</h1>
        <div>Barangay Pamanlinan</div>  
      </div>
    </div>
    <div class="time">
      Philippine Standard Time:<br>
      <span id="clock"></span>
    </div>
  </header>

  <div class="container">
    <!-- Sidebar -->
    <!-- Sidebar -->
    <aside class="sidebar">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="pamanlinan.php">Dashboard</a></li>
        <li><a href="all-forms-page.php">Forms</a></li>
        <li><a href="list.php">Population Records</a></li>
        <li><a href="#">Bulletin Board</a></li>
      </ul>
    </aside>

    <!-- Main Content -->
    <main class="content">

      <h2>Barangay Pamanlinan</h2>
      <img src="pamanlinan-logo.png" alt="Barangay Logo" class="logo">

      <div class="officials">
        <h3>Barangay Officials:</h3>
        <p><strong>Jennifer M. Magno</strong><br>Punong Barangay</p>

        <h3>Sangguniang Barangay:</h3>
        <p>1. Demonteverde, Bernadeth Pancho</p>
        <p>2. Sampayan, Jerry Tubo</p>
        <p>3. Otugay, Limuel Delos Santos</p>
        <p>4. Penande, Michelle Rodilla</p>
        <p>5. Magno, Arvin De Castro</p>
        <p>6. Delos Santos, Roque Sr. Corteza</p>
        <p>7. Josafat, Florito Salazar</p>

        <h3>Appointed Officials:</h3>
      
        <p><strong>Emmanuel J. Layupan</strong> – IPMR</p>
        <p><strong>Darwin M. Rebuta</strong> – SK Chairperson</p>
        <p><strong>Mrs. Sherlita T. Ramos</strong> – Barangay Secretary</p>
        <p><strong>Mrs. Avelina F. Penande</strong> – Barangay Treasurer</p>
      </div>
    </main>
  </div>

  <footer>
    © 2025 Barangay Pamanlinan, City of Bislig | All Rights Reserved
  </footer>

  <script>
    function updateClock() {
      const now = new Date();
      document.getElementById('clock').textContent =
        now.toLocaleString('en-PH', { timeZone: 'Asia/Manila', dateStyle: 'full', timeStyle: 'medium' });
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>

</body>
</html>
