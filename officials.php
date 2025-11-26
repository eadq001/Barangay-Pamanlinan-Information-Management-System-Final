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

// ✅ Handle bulk update request
$updateMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_update_officials'])) {
    $updates = $_POST['officials'] ?? [];
    $errorCount = 0;
    $successCount = 0;
    
    foreach ($updates as $id => $data) {
        $name = trim($data['name'] ?? '');
        $position = trim($data['position'] ?? '');
        
        if ($id && $name && $position) {
            try {
                $stmt = $pdo->prepare("UPDATE barangay_officials SET name = ?, position = ? WHERE id = ?");
                $stmt->execute([$name, $position, $id]);
                $successCount++;
            } catch (PDOException $e) {
                $errorCount++;
            }
        }
    }
    
    if ($successCount > 0) {
        $updateMessage = '<div style="background:#4caf50;color:white;padding:10px;border-radius:4px;margin-bottom:15px;">✓ ' . $successCount . ' official(s) updated successfully!</div>';
    }
    if ($errorCount > 0) {
        $updateMessage .= '<div style="background:#f44336;color:white;padding:10px;border-radius:4px;margin-bottom:15px;">✗ ' . $errorCount . ' update(s) failed.</div>';
    }
}

// ✅ Fetch all officials from database
$officials = [];
try {
    $stmt = $pdo->query("SELECT id, name, position FROM barangay_officials ORDER BY id ASC");
    $officials = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $officials = [];
}

// ✅ Group officials by position
$groupedOfficials = [];
foreach ($officials as $official) {
    $position = $official['position'] ?? 'Other';
    if (!isset($groupedOfficials[$position])) {
        $groupedOfficials[$position] = [];
    }
    $groupedOfficials[$position][] = $official;
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
      padding: 8px;
      border-radius: 4px;
      transition: 0.2s;
    }

    .officials p:hover {
      background-color: #f5f5f5;
    }

    .edit-all-btn {
      background: #0b6b29;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
      transition: 0.3s;
      margin-bottom: 20px;
    }

    .edit-all-btn:hover {
      background: #037a2b;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.4);
      overflow-y: auto;
    }

    .modal-content {
      background-color: #fefefe;
      margin: 2% auto;
      padding: 20px;
      border: 1px solid #888;
      border-radius: 8px;
      width: 90%;
      max-width: 900px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      border-bottom: 2px solid #0b6b29;
      padding-bottom: 10px;
    }

    .modal-header h2 {
      margin: 0;
      color: #0b6b29;
      font-size: 1.5rem;
    }

    .close-btn {
      color: #aaa;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      background: none;
      border: none;
      padding: 0;
    }

    .close-btn:hover {
      color: #000;
    }

    /* Edit Table Styles */
    .edit-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .edit-table th {
      background-color: #0b6b29;
      color: white;
      padding: 10px;
      text-align: left;
      font-weight: 600;
    }

    .edit-table td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
    }

    .edit-table tr:hover {
      background-color: #f5f5f5;
    }

    .edit-table input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 0.95rem;
      box-sizing: border-box;
    }

    .edit-table input:focus {
      outline: none;
      border-color: #0b6b29;
      box-shadow: 0 0 5px rgba(11, 107, 41, 0.3);
    }

    .modal-buttons {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 20px;
    }

    .btn-save {
      background: #4caf50;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
      transition: 0.3s;
    }

    .btn-save:hover {
      background: #45a049;
    }

    .btn-cancel {
      background: #999;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1rem;
      transition: 0.3s;
    }

    .btn-cancel:hover {
      background: #777;
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
        <?= $updateMessage ?>
        <?php if (!empty($officials)): ?>
          <button class="edit-all-btn" onclick="openEditAllModal()">Edit All Officials</button>
        <?php endif; ?>
        
        <?php if (!empty($groupedOfficials)): ?>
          <?php foreach ($groupedOfficials as $position => $positionOfficials): ?>
            <h3><?= htmlspecialchars($position) ?></h3>
            <?php foreach ($positionOfficials as $official): ?>
              <p>
                <strong><?= htmlspecialchars($official['name']) ?></strong><br>
                <?= htmlspecialchars($official['position']) ?>
              </p>
            <?php endforeach; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No officials data available.</p>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <!-- Edit All Officials Modal -->
  <div id="editAllModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit All Officials</h2>
        <button class="close-btn" onclick="closeEditAllModal()">&times;</button>
      </div>
      <form method="POST" action="">
        <input type="hidden" name="bulk_update_officials" value="1">
        
        <table class="edit-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Position</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($officials as $official): ?>
              <tr>
                <td><input type="text" name="officials[<?= $official['id'] ?>][name]" value="<?= htmlspecialchars($official['name']) ?>" required></td>
                <td><input type="text" name="officials[<?= $official['id'] ?>][position]" value="<?= htmlspecialchars($official['position']) ?>" required></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        
        <div class="modal-buttons">
          <button type="button" class="btn-cancel" onclick="closeEditAllModal()">Cancel</button>
          <button type="submit" class="btn-save">Save All Changes</button>
        </div>
      </form>
    </div>
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

    // Modal Functions
    function openEditAllModal() {
      document.getElementById('editAllModal').style.display = 'block';
    }

    function closeEditAllModal() {
      document.getElementById('editAllModal').style.display = 'none';
    }

    // Close modal when clicking outside the content
    window.onclick = function(event) {
      const editAllModal = document.getElementById('editAllModal');
      if (event.target === editAllModal) {
        editAllModal.style.display = 'none';
      }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeEditAllModal();
      }
    });
  </script>

</body>
</html>
