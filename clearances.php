<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Pamanlinan | Clearance Services</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
  }

  body {
    background: linear-gradient(135deg, #025113ff, #1e1919b0);
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background-color: #0b6b29;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 5%;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  }

  header img {
    width: 65px;
    border-radius: 50%;
  }

  header h1 {
    font-size: 1.8rem;
    font-weight: 700;
  }

  /* ====== NEW MODERN LAYOUT ====== */
  .main-container {
    display: flex;
    flex: 1;
    padding: 40px 4%;
    gap: 30px;
  }

  /* 🌿 Sidebar - elegant glass design */
  .sidebar {
    width: 200px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    padding: 20px 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .sidebar h3 {
    color: #fff;
    font-size: 1rem;
    margin-bottom: 15px;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  .sidebar ul {
    list-style: none;
    width: 100%;
  }

  .sidebar ul li {
    margin: 10px 0;
  }

  .sidebar ul li a {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
    text-decoration: none;
    font-size: 0.95rem;
    padding: 10px 12px;
    border-radius: 10px;
    transition: 0.3s;
  }

  .sidebar ul li a:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateX(5px);
  }

  .sidebar ul li i {
    font-size: 18px;
  }

  /* 🌿 Cards area */
  .services {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
    justify-items: center;
  }

  .card {
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    text-align: center;
    padding: 2rem 1rem;
    text-decoration: none;
    color: #fff;
    transition: 0.3s;
    width: 220px;
    height: 220px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
  }

  .card:hover {
    transform: translateY(-6px);
    background: rgba(255,255,255,0.2);
  }

  .card i {
    font-size: 60px;
    margin-bottom: 15px;
  }

  .card h2 {
    font-size: 1.1rem;
  }

  footer {
    text-align: center;
    padding: 1rem;
    background: rgba(0,0,0,0.25);
    color: #ccc;
    font-size: 0.9rem;
    border-top: 2px solid rgba(255,255,255,0.1);
  }

  @media (max-width: 768px) {
    .main-container {
      flex-direction: column;
      align-items: center;
    }
    .sidebar {
      width: 100%;
      flex-direction: row;
      justify-content: space-around;
    }
    .sidebar ul {
      display: flex;
      justify-content: space-around;
      width: 100%;
    }
    .sidebar ul li a {
      flex-direction: column;
      font-size: 0.8rem;
    }
  }
</style>
</head>
<body>

<header>
  <div style="display:flex;align-items:center;gap:10px;">
    <img src="pamanlinan.png" alt="Barangay Logo">
    <div>
      <h1>Barangay Pamanlinan</h1>
      <div>City of Bislig, Surigao del Sur</div>
    </div>
  </div>
  <div>
    Philippine Standard Time:<br>
    <span id="clock"></span>
  </div>
</header>

<div class="main-container">
  <!-- 🌿 Modern Sidebar -->
  <aside class="sidebar">
    <h3>Quick Links</h3>
    <ul>
      <li><a href="pamanlinan.php"><i class="fa-solid fa-gauge"></i>Dashboard</a></li>
      <li><a href="all-forms-page.php"><i class="fa-solid fa-file-lines"></i>Forms</a></li>
      <li><a href="list.php"><i class="fa-solid fa-users"></i>Population</a></li>
      <li><a href="#"><i class="fa-solid fa-bullhorn"></i>Bulletin</a></li>
    </ul>
  </aside>

  <!-- 🌿 Services Cards -->
  <section class="services">
    <a href="barangay_clearance.php" class="card">
      <i class="fa-solid fa-file-circle-check"></i>
      <h2>Barangay Clearance</h2>
    </a>

    <a href="indigent_certificate.php" class="card">
      <i class="fa-solid fa-hand-holding-heart"></i>
      <h2>Certificate of Indigency</h2>
    </a>

    <a href="#" class="card">
      <i class="fa-solid fa-house-user"></i>
      <h2>Residency Certificate</h2>
    </a>
  </section>
</div>

<footer>
  &copy; 2025 Barangay Pamanlinan | All Rights Reserved
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
