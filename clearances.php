<?php
// --- Start session and connect to your database ---
session_start();

// Optional: Redirect to login if not logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Connect to your database
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

<!-- Font Awesome for modern icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  /* Reset */
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Poppins", sans-serif;
  }

  body {
    background: linear-gradient(to bottom right, #00796b, #004d40);
    color: #fff;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background: rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(6px);
    padding: 20px;
    text-align: center;
    position: relative;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
  }

  header img {
    width: 70px;
    vertical-align: middle;
    border-radius: 50%;
  }

  header h1 {
    font-size: 1.8rem;
    margin-top: 10px;
    font-weight: 700;
    letter-spacing: 1px;
    color: #fff;
  }

  .container {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
    max-width: 1100px;
    margin: 0 auto;
  }

  .card {
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    text-align: center;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    text-decoration: none;
  }

  .card:hover {
    transform: translateY(-8px);
    background: rgba(255, 255, 255, 0.2);
  }

  .card i {
    font-size: 60px;
    color: #fff;
    margin-bottom: 10px;
  }

  .card h2 {
    font-size: 1.1rem;
    color: #fff;
    margin-top: 5px;
  }

  footer {
    text-align: center;
    padding: 1rem;
    background: rgba(0, 0, 0, 0.25);
    color: #ddd;
    font-size: 0.9rem;
    border-top: 2px solid rgba(255, 255, 255, 0.1);
  }

  /* Back Button */
  .back-btn {
    display: inline-block;
    background: #004d40;
    color: #fff;
    padding: 10px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    position: absolute;
    top: 20px;
    left: 20px;
  }

  .back-btn:hover {
    background: #009688;
  }

  @media (max-width: 600px) {
    header h1 {
      font-size: 1.4rem;
    }
    .back-btn {
      padding: 8px 18px;
      font-size: 0.9rem;
    }
  }
</style>
</head>
<body>

  <header>
    <a href="pamanlinan.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
    <img src="pamanlinan.png" alt="Barangay Seal">
    <h1>Barangay Pamanlinan Clearance Services</h1>
  </header>

  <div class="container">
    <a href="barangay_clearance.php" class="card">
      <i class="fa-solid fa-file-lines"></i>
      <h2>Barangay Clearance</h2>
    </a>

    <a href="indigent_certificate.php" class="card">
      <i class="fa-solid fa-hand-holding-heart"></i>
      <h2>Certificate of Indigency</h2>
    </a>

    <a href="residency.php" class="card">
      <i class="fa-solid fa-house-user"></i>
      <h2>Certificate of Residency</h2>
    </a>

    <a href="good_moral.php" class="card">
      <i class="fa-solid fa-certificate"></i>
      <h2>Certificate of Good Moral</h2>
    </a>

    <a href="job_seeker.php" class="card">
      <i class="fa-solid fa-briefcase"></i>
      <h2>Certificate of Job Seeker</h2>
    </a>
  </div>

  <footer>
    &copy; 2025 Barangay Pamanlinan | All Rights Reserved
  </footer>

</body>
</html>
