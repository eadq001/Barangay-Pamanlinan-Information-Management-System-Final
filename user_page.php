<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Portal | Barangay Pamanlinan</title>
  <link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(135deg, #00bfa5, #80deea);
      margin: 0;
      color: #000;
    }
    header {
      background: #004d40;
      color: #fff;
      padding: 1rem;
      text-align: center;
      font-size: 1.4rem;
      font-weight: bold;
    }
    nav {
      background: #00695c;
      text-align: center;
      padding: 10px 0;
    }
    nav a {
      color: #e0f2f1;
      margin: 0 15px;
      text-decoration: none;
      font-weight: 600;
    }
    nav a:hover {
      color: #00e676;
    }
    main {
      max-width: 900px;
      margin: 2rem auto;
      background: rgba(255,255,255,0.9);
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    h2 {
      color: #004d40;
      margin-bottom: 10px;
    }
    section {
      margin-bottom: 2rem;
    }
    .announcement {
      background: #e0f2f1;
      padding: 1rem;
      border-radius: 8px;
      margin-top: 10px;
    }
    footer {
      text-align: center;
      background: #004d40;
      color: #fff;
      padding: 1rem;
      margin-top: 2rem;
    }
    button {
      background: #004d40;
      color: #fff;
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      cursor: pointer;
    }
    button:hover {
      background: #00796b;
    }
  </style>
</head>
<body>

  <header>
    Barangay Pamanlinan — Citizen Information Page
  </header>

  <nav>
    <a href="index.php">Home</a>
    <a href="#announcements">Announcements</a>
    <a href="#schedule">Schedule</a>
    <a href="#notice">Notices</a>
  </nav>

  <main>
    <section id="announcements">
      <h2>📢 Announcements</h2>
      <div class="announcement">
        <p><strong>Barangay Assembly:</strong> Join us this Sunday, 9:00 AM, at the Barangay Hall.</p>
      </div>
      <div class="announcement">
        <p><strong>Health Mission:</strong> Free medical check-up and dental services this Friday.</p>
      </div>
    </section>

    <section id="schedule">
      <h2>🗓 Schedule</h2>
      <p>• Garbage Collection — Every Tuesday & Friday (6:00 AM)<br>
         • Barangay Clean-up Drive — Every last Saturday of the month<br>
         • Youth Sports League — Every weekend at Barangay Court</p>
    </section>

    <section id="notice">
      <h2>📄 Community Notices</h2>
      <p>Residents are reminded to update their household information records for 2025.</p>
    </section>

    <div style="text-align:center; margin-top:20px;">
      <button onclick="window.location.href='index.php'">Back to Home</button>
    </div>
  </main>

  <footer>
    &copy; 2025 Barangay Pamanlinan | All Rights Reserved
  </footer>

</body>
</html>
