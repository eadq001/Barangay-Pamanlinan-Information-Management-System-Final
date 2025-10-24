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


     .bulletins {
      padding: 20px 0;
    }

    .bulletin-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .bulletin-card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }

    .bulletin-card:hover {
      transform: translateY(-5px);
    }

    .bulletin-card h3 {
      color: #004d40;
      margin: 0 0 10px 0;
    }

    .date, .event-date {
      color: #666;
      font-size: 0.9em;
      margin: 5px 0;
    }

    .content {
      margin: 10px 0;
      line-height: 1.5;
    }

    .no-bulletins {
      text-align: center;
      color: #666;
      font-style: italic;
    }

    .bulletin-image {
      position: relative;
      aspect-ratio: 16/9;
      background: #f5f5f5;
      border-radius: 5px;
      overflow: hidden;
      margin: 10px 0;
    }

    .bulletin-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .bulletin-card {
      text-decoration: none;
      color: inherit;
      cursor: pointer;
      padding: 15px;
      border: 1px solid #e5e7eb;
      margin-bottom: 20px;
      border-radius: 8px;
      background: white;
    }

    .bulletin-card:hover {
      border-color: #d1d5db;
    }

    .bulletin-content {
      position: relative;
    }

    .event-date {
      color: #059669;
      font-weight: 600;
      margin: 5px 0;
    }

    .date {
      color: #6b7280;
      font-size: 0.9em;
      margin-bottom: 10px;
    }

    .read-more {
      color: #059669;
      font-weight: 600;
    }

    .bulletin-card:hover .read-more {
      text-decoration: underline;
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
    <?php
    include 'connection.php';

    // Fetch bulletins from database ordered by event_date
    $query = "SELECT * FROM bulletins ORDER BY CASE 
        WHEN event_date IS NULL THEN 1 
        ELSE 0 
    END, event_date DESC, created_at DESC";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
      echo '<section class="bulletins">';
      echo '<h2>Latest Bulletins & Events</h2>';
      echo '<div class="bulletin-grid">';
      
      while ($row = mysqli_fetch_assoc($result)) {
        echo '<a href="bulletin_view_user.php?id=' . $row['id'] . '" class="bulletin-card block hover:shadow-lg transition-shadow duration-300">';
        echo '<div class="bulletin-content">';
        echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
        if ($row['event_date']) {
            echo '<p class="event-date">Event Date: ' . date('F j, Y', strtotime($row['event_date'])) . 
                 (!empty($row['event_time']) ? ' at ' . date('g:i A', strtotime($row['event_time'])) : '') . '</p>';
        }
        echo '<p class="date">Posted: ' . date('F j, Y', strtotime($row['created_at'])) . '</p>';
        if (!empty($row['image_path'])) {
            echo '<div class="bulletin-image"><img src="' . htmlspecialchars($row['image_path']) . '" alt="Bulletin Image" style="max-width: 100%; height: auto; margin: 10px 0;"></div>';
        }
        echo '<p class="content">' . htmlspecialchars(substr($row['content'], 0, 300)) . 
             (strlen($row['content']) > 300 ? '... <span class="read-more">Read More →</span>' : '') . '</p>';
        if ($row['event_date']) {
          echo '<p class="event-date">Event Date: ' . date('F j, Y', strtotime($row['event_date'])) . '</p>';
        }
        echo '</div>';
      }
      
      echo '</div></section>';
    } else {
      echo '<p class="no-bulletins">No bulletins available at the moment.</p>';
    }

    mysqli_close($conn);
    ?>

    
    <div style="text-align:center; margin-top:20px;">
      <a href="index.php"><button type="button">Back to Home</button></a>
    </div>
  </main>

  <footer>
    &copy; 2025 Barangay Pamanlinan | All Rights Reserved
  </footer>

</body>
</html>
