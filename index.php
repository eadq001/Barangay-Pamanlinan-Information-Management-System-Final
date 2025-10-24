<?php
session_start();
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Brgy. Pamanlinan Demographic Profiling System</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Poetsen+One&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">

  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Inter', sans-serif;
      color: #fff;
      background: #000;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      overflow-x: hidden;
      position: relative;
    }

    /* ✅ Background Video */
    .bg-video {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      z-index: -2;
    }

    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: -1;
    }

    /* ✅ Navbar */
    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background: rgba(0, 0, 0, 0.6);
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      z-index: 100;
      backdrop-filter: blur(10px);
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
      color: rgb(0, 255, 191);
      font-size: 1.4rem;
      font-weight: bold;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
      text-transform: uppercase;
    }

    .logo img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
    }

    .nav-links {
      display: flex;
      gap: 25px;
    }

    .nav-links a {
      color: #e0f7fa;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
    }

    .nav-links a:hover {
      color: #00ff7f;
    }

    .hamburger {
      display: none;
      flex-direction: column;
      cursor: pointer;
      width: 26px;
      height: 20px;
      justify-content: space-between;
    }

    .hamburger span {
      background: #fff;
      height: 3px;
      width: 100%;
      border-radius: 2px;
    }

    /* ✅ Main Section */
    .profile-section {
      text-align: center;
      padding: 2rem 1rem;
      margin-top: 120px;
      animation: fadeIn 1s ease;
      max-width: 90%;
    }

    .profile-pic {
      border-radius: 50%;
      width: 180px;
      height: 180px;
      object-fit: cover;
      margin-bottom: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.7);
    }

    h1 {
      color: rgb(0, 255, 255);
      font-size: 3rem;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
      -webkit-text-stroke: 1px black;
      font-family: "Lilita One", sans-serif;
    }

    h2 {
      color: rgb(7, 255, 255);
      font-size: 1.8rem;
      margin-bottom: 25px;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
      -webkit-text-stroke: 0.5px black;
      font-family: "Lilita One", sans-serif;
    }

    .fancy-button {
      display: inline-block;
      background-color: rgb(8, 98, 12);
      color: white;
      padding: 0.6rem 2rem;
      font-size: 1rem;
      font-weight: 600;
      border: 2px solid transparent;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease-in-out;
      margin: 0.5rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
      text-decoration: none;
    }

    .fancy-button:hover {
      background-color: rgb(0, 180, 60);
      border-color: rgb(0, 255, 80);
      transform: scale(1.05);
    }

    /* ✅ Footer */
    footer {
      margin-top: auto;
      text-align: center;
      padding: 1rem;
      background: rgba(0, 0, 0, 0.6);
      color: #ccc;
      width: 100%;
      font-size: 0.9rem;
    }

    /* ✅ Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* ✅ Mobile Responsive */
    @media (max-width: 768px) {
      header {
        padding: 15px 20px;
      }

      .nav-links {
        position: absolute;
        top: 70px;
        right: 0;
        width: 100%;
        flex-direction: column;
        background: rgba(0, 0, 0, 0.85);
        display: none;
        text-align: center;
        padding: 1rem 0;
      }

      .nav-links.active {
        display: flex;
      }

      .hamburger {
        display: flex;
      }

      .nav-links a {
        padding: 0.8rem 0;
        font-size: 1.1rem;
      }

      h1 {
        font-size: 2.2rem;
      }

      h2 {
        font-size: 1.4rem;
      }

      .profile-pic {
        width: 140px;
        height: 140px;
      }

      .fancy-button {
        width: 80%;
        padding: 0.8rem 0;
      }
    }

    @media (max-width: 480px) {
      h1 {
        font-size: 1.8rem;
      }

      h2 {
        font-size: 1.2rem;
      }
    }
  </style>
</head>

<body>

  <!-- ✅ Background Video -->
  <video autoplay muted loop playsinline class="bg-video">
    <source src="pamanlinan.mp4" type="video/mp4">
  </video>
  <div class="overlay"></div>

  <!-- ✅ Navbar -->
  <header>
    <div class="logo">
      <img src="pamanlinan.png" alt="Logo">
      BARANGAY PAMANLINAN
    </div>
    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="about.html">About</a>
      <a href="login.php">Admin</a>
      <a href="pamanlinan.php">User</a>
    </nav>
    <div class="hamburger">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </header>

  <!-- ✅ Main Section -->
  <section class="profile-section">
    <img src="pamanlinan.png" alt="Profile Pic" class="profile-pic" />
    <h1>BARANGAY PAMANLINAN </h1>
    <h2>INFORMATION MANAGEMENT SYSTEM</h2>
   
    <div class="btn">
  <a href="login.php" class="fancy-button">ADMIN</a>
  <a href="user_page.php" class="fancy-button">USER</a>
</div>

  </section>

  <!-- ✅ Footer -->
  <footer>
    &copy; 2025 Barangay Pamanlinan | All Rights Reserved
  </footer>

  <!-- ✅ Script for hamburger menu -->
  <script>
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('active');
      hamburger.classList.toggle('open');
    });
  </script>

</body>
</html>
