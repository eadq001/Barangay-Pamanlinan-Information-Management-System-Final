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

    function formatEventDate($date, $time = null) {
        $formattedDate = date('F j, Y', strtotime($date));
        return $time ? $formattedDate . ' at ' . date('g:i A', strtotime($time)) : $formattedDate;
    }

    function renderBulletinCard($bulletin) {
        $title = trim($bulletin['title']);
        $content = trim($bulletin['content']);
        
        if (empty($title) || empty($content)) {
            return '';
        }

        $html = '<a href="bulletin_view_user.php?id=' . $bulletin['id'] . '" class="bulletin-card block hover:shadow-lg transition-shadow duration-300">';
        $html .= '<div class="bulletin-content">';
        
        // Title
        $html .= '<h3>' . htmlspecialchars($title) . '</h3>';
        
        // Event date if exists
        if (!empty($bulletin['event_date'])) {
            $html .= '<p class="event-date">Event Date: ' . formatEventDate($bulletin['event_date'], $bulletin['event_time']) . '</p>';
        }
        
        // Posted date
        $html .= '<p class="date">Posted: ' . formatEventDate($bulletin['created_at']) . '</p>';
        
        // Image if exists
        if (!empty($bulletin['image_path'])) {
            $html .= '<div class="bulletin-image">';
            $html .= '<img src="' . htmlspecialchars($bulletin['image_path']) . '" alt="Bulletin Image" style="max-width: 100%; height: auto; margin: 10px 0;">';
            $html .= '</div>';
        }
        
        // Content preview
        $contentPreview = substr($content, 0, 300);
        $html .= '<p class="content">' . htmlspecialchars($contentPreview);
        if (strlen($content) > 300) {
            $html .= '... <span class="read-more">Read More →</span>';
        }
        $html .= '</p>';
        
        $html .= '</div></a>';
        return $html;
    }

    // Get current date for comparison
    $today = date('Y-m-d');
    
    // Fetch valid bulletins from database
    $query = "SELECT * FROM bulletins 
              WHERE title IS NOT NULL 
              AND TRIM(title) != '' 
              AND content IS NOT NULL 
              AND TRIM(content) != ''
              AND (
                  -- Upcoming events first
                  (event_date >= '$today') OR
                  -- Then past events
                  (event_date < '$today') OR
                  -- Then non-event bulletins
                  (event_date IS NULL)
              )
              ORDER BY 
                CASE 
                    WHEN event_date >= '$today' THEN 1
                    WHEN event_date < '$today' THEN 2
                    WHEN event_date IS NULL THEN 3
                END,
                event_date ASC,
                created_at DESC";
    
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo '<section class="bulletins">';
        echo '<h2>Latest Bulletins & Events</h2>';
        
        // Separate upcoming events section
        $hasUpcomingEvents = false;
        $upcomingEvents = '';
        
        // Past events and regular bulletins section
        $otherBulletins = '';
        
        while ($row = mysqli_fetch_assoc($result)) {
            $bulletin = renderBulletinCard($row);
            
            if (!empty($row['event_date']) && strtotime($row['event_date']) >= strtotime($today)) {
                $upcomingEvents .= $bulletin;
                $hasUpcomingEvents = true;
            } else {
                $otherBulletins .= $bulletin;
            }
        }
        
        // Display upcoming events if any
        if ($hasUpcomingEvents) {
            echo '<div class="mb-8">';
            echo '<h3 class="text-xl font-semibold text-blue-600 mb-4">Upcoming Events</h3>';
            echo '<div class="bulletin-grid">' . $upcomingEvents . '</div>';
            echo '</div>';
        }
        
        // Display other bulletins
        if (!empty($otherBulletins)) {
            if ($hasUpcomingEvents) {
                echo '<h3 class="text-xl font-semibold text-gray-600 mb-4">Past Events & Announcements</h3>';
            }
            echo '<div class="bulletin-grid">' . $otherBulletins . '</div>';
        }
        
        echo '</section>';
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
