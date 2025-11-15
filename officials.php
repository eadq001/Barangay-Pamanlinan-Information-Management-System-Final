<?php
session_start();
try {
    $pdo = new PDO('mysql:host=localhost;dbname=pamanlinan_db', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Database failed: " . $e->getMessage());
}

// Fetch barangay info
$stmt = $pdo->query("SELECT * FROM barangay_info LIMIT 1");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle update request via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'punong_barangay', 'kagawad1', 'kagawad2', 'kagawad3', 'kagawad4', 'kagawad5', 'kagawad6', 'kagawad7',
        'ipmr', 'sk_chair', 'secretary', 'treasurer', 'address', 'phone', 'email', 'facebook'
    ];

    $values = [];
    foreach ($fields as $f) $values[$f] = $_POST[$f] ?? '';

    $sql = "UPDATE barangay_info SET " . implode('=?, ', $fields) . "=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array_merge(array_values($values), [$data['id']]));
    echo json_encode(["success" => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Pamanlinan | Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
body {
  margin: 0;
  font-family: "Poppins", sans-serif;
  background-color: #e6f5eb;
  color: #222;
}
header {
  background-color: #0b6b29;
  color: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 5%;
}
header img { height: 60px; }
h1 { font-size: 1.4rem; margin: 0; }
.container {
  display: flex;
  justify-content: center;
  padding: 40px 5%;
  flex-wrap: wrap;
  gap: 2rem;
}
.sidebar {
  background: #fff;
  border-radius: 12px;
  padding: 15px;
  flex: 0.3 1 220px;
  height: fit-content;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.sidebar ul { list-style: none; padding: 0; margin: 0; }
.sidebar ul li a {
  text-decoration: none;
  color: #0b6b29;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 0;
  font-weight: 600;
  transition: 0.3s;
}
.sidebar ul li a:hover { color: #024d20; }

.content {
  flex: 1 1 400px;
  max-width: 700px;
  background: #fff;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  position: relative;
}
.content h2 {
  color: #0b6b29;
  border-bottom: 3px solid #0b6b29;
  display: inline-block;
  margin-bottom: 15px;
}
button.edit-btn {
  background: #0b6b29;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 6px;
  cursor: pointer;
  position: absolute;
  top: 25px;
  right: 25px;
}
button.edit-btn:hover { background: #15803d; }

/* Officials layout */
.officials h3 { color: #0b6b29; margin-top: 20px; }
.officials p { margin: 4px 0; }

/* Modal */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.4);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: white;
  border-radius: 10px;
  padding: 20px;
  width: 90%;
  max-width: 600px;
  overflow-y: auto;
  max-height: 90vh;
}
.modal h3 { color: #0b6b29; margin-bottom: 10px; }
.modal label { font-weight: 600; color: #0b6b29; }
.modal input {
  width: 100%; margin-bottom: 10px;
  padding: 8px; border: 1px solid #ccc; border-radius: 6px;
}
.modal .btns {
  display: flex; justify-content: flex-end; gap: 10px;
}
.modal .btns button {
  border: none; border-radius: 6px; padding: 8px 14px; cursor: pointer;
}
.save { background: #0b6b29; color: white; }
.close { background: #999; color: white; }
footer {
  text-align: center;
  font-size: 0.9rem;
  color: #555;
  margin: 25px 0;
}
</style>
</head>
<body>
<header>
  <div style="display:flex;align-items:center;gap:10px;">
    <img src="pamanlinan.png" alt="">
    <div><h1>Barangay Pamanlinan</h1><div>City of Bislig, Surigao del Sur</div></div>
  </div>
</header>

<div class="container">
  <aside class="sidebar">
    <ul>
      <li><a href="pamanlinan.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
      <li><a href="all-forms-page.php"><i class="ri-file-list-line"></i> Forms</a></li>
      <li><a href="list.php"><i class="ri-database-2-line"></i> Main Records</a></li>
      <li><a href="ageGroup.php"><i class="ri-user-heart-line"></i> Age Group</a></li>
      <li><a href="disabilitiesGroup.php"><i class="ri-wheelchair-line"></i> Disabilities</a></li>
      <li><a href="deceased.php"><i class="ri-emotion-sad-line"></i> Deceased</a></li>
    </ul>
  </aside>

  <main class="content">
    <button class="edit-btn" id="editBtn"><i class="ri-edit-2-line"></i> Edit Info</button>
    <h2>Barangay Officials</h2>

    <div class="officials" id="officialsSection">
      <p><strong>Punong Barangay:</strong> <?= htmlspecialchars($data['punong_barangay']) ?></p>
      <h3>Sangguniang Barangay:</h3>
      <?php for($i=1;$i<=7;$i++): ?>
        <p><?= htmlspecialchars($data["kagawad$i"]) ?></p>
      <?php endfor; ?>

      <h3>Appointed Officials:</h3>
      <p><strong>IPMR:</strong> <?= htmlspecialchars($data['ipmr']) ?></p>
      <p><strong>SK Chairperson:</strong> <?= htmlspecialchars($data['sk_chair']) ?></p>
      <p><strong>Secretary:</strong> <?= htmlspecialchars($data['secretary']) ?></p>
      <p><strong>Treasurer:</strong> <?= htmlspecialchars($data['treasurer']) ?></p>

      <h3>Contact Information</h3>
      <p><i class="ri-map-pin-2-line"></i> <?= htmlspecialchars($data['address']) ?></p>
      <p><i class="ri-phone-line"></i> <?= htmlspecialchars($data['phone']) ?></p>
      <p><i class="ri-mail-line"></i> <?= htmlspecialchars($data['email']) ?></p>
      <p><i class="ri-facebook-circle-line"></i> <?= htmlspecialchars($data['facebook']) ?></p>
    </div>
  </main>
</div>

<!-- Modal -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <h3>Edit Officials and Contact Info</h3>
    <form id="editForm">
      <label>Punong Barangay</label><input name="punong_barangay" value="<?= htmlspecialchars($data['punong_barangay']) ?>">
      <?php for($i=1;$i<=7;$i++): ?>
        <label>Kagawad <?= $i ?></label><input name="kagawad<?= $i ?>" value="<?= htmlspecialchars($data["kagawad$i"]) ?>">
      <?php endfor; ?>
      <label>IPMR</label><input name="ipmr" value="<?= htmlspecialchars($data['ipmr']) ?>">
      <label>SK Chairperson</label><input name="sk_chair" value="<?= htmlspecialchars($data['sk_chair']) ?>">
      <label>Secretary</label><input name="secretary" value="<?= htmlspecialchars($data['secretary']) ?>">
      <label>Treasurer</label><input name="treasurer" value="<?= htmlspecialchars($data['treasurer']) ?>">
      <h3>Contact Info</h3>
      <label>Address</label><input name="address" value="<?= htmlspecialchars($data['address']) ?>">
      <label>Phone</label><input name="phone" value="<?= htmlspecialchars($data['phone']) ?>">
      <label>Email</label><input name="email" value="<?= htmlspecialchars($data['email']) ?>">
      <label>Facebook</label><input name="facebook" value="<?= htmlspecialchars($data['facebook']) ?>">
      <div class="btns">
        <button type="button" class="close" id="closeModal">Cancel</button>
        <button type="submit" class="save">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<footer>
  © 2025 Barangay Pamanlinan, City of Bislig | All Rights Reserved
</footer>

<script>
const modal = document.getElementById("editModal");
document.getElementById("editBtn").onclick = ()=> modal.style.display="flex";
document.getElementById("closeModal").onclick = ()=> modal.style.display="none";
window.onclick = (e)=>{ if(e.target===modal) modal.style.display="none"; };

document.getElementById("editForm").onsubmit = async (e)=>{
  e.preventDefault();
  const formData = new FormData(e.target);
  const res = await fetch("", {method:"POST", body: formData});
  const data = await res.json();
  if(data.success){
    alert("✅ Information updated successfully!");
    location.reload();
  }
};
</script>
</body>
</html>
