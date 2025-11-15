<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cards = $conn->query("SELECT * FROM dashboard_cards ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Pamanlinan Dashboard</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
:root {
  --primary: #0b6b2d;
  --primary-dark: #06481a;
  --background: #f4f9f6;
  --white: #fff;
  --text: #333;
  --shadow: 0 4px 10px rgba(0,0,0,0.1);
}

* { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }

body {
  background: var(--background);
  color: var(--text);
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

/* HEADER */
header {
  background: var(--primary);
  color: var(--white);
  padding: 1.2rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow);
}
header h1 {
  font-size: 1.4rem;
  display: flex;
  align-items: center;
  gap: 10px;
}
header h1 img { width: 45px; height: 45px; }
nav a {
  color: var(--white);
  text-decoration: none;
  margin-left: 1.5rem;
  font-weight: 600;
}
nav a:hover { color: gold;}

/* MAIN */
main {
  flex: 1;
  padding: 2rem 3%;
}
.dashboard {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1.5rem;
}

/* CARD */
.card {
  background: var(--white);
  border-radius: 15px;
  box-shadow: var(--shadow);
  text-align: center;
  padding: 2rem 1.5rem;
  border-top: 6px solid var(--primary);
  position: relative;
  transition: 0.3s;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 14px rgba(0,0,0,0.15);
  cursor: pointer;
}
.card i { font-size: 3rem; color: var(--primary); margin-bottom: 1rem; }
.card h3 { color: var(--primary); margin-bottom: 0.5rem; }

.card-actions {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  gap: 8px;
}
.card-actions .edit-btn, .card-actions .delete-btn {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2rem;
}
.edit-btn i { color: #008CBA; font-size: 1.3rem; }
.delete-btn i { color: #d9534f; font-size: 1.3rem; }

/* FLOATING ADD BUTTON */
.add-btn {
  position: fixed;
  bottom: 25px;
  left: 25px;
  background: var(--primary);
  color: white;
  border: none;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  font-size: 2rem;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  transition: 0.3s;
}
.add-btn:hover {
  background: var(--primary-dark);
  transform: scale(1.05);
}

/* MODAL */
.modal {
  display: none;
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal-content {
  background: var(--white);
  padding: 25px;
  border-radius: 12px;
  width: 90%;
  max-width: 450px;
  box-shadow: var(--shadow);
}
.modal-content h2 {
  color: var(--primary);
  margin-bottom: 15px;
}
.modal-content label {
  display: block;
  margin-top: 10px;
  font-weight: 600;
  color: var(--primary);
}
.modal-content input, .modal-content textarea {
  width: 100%;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
  margin-top: 5px;
}
.modal-buttons {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}
.modal-buttons button {
  border: none;
  border-radius: 6px;
  padding: 10px 16px;
  cursor: pointer;
  font-weight: 600;
}
.btn-save { background: var(--primary); color: white; }
.btn-cancel { background: #999; color: white; }

/* FOOTER */
footer {
  background: var(--primary);
  color: var(--white);
  text-align: center;
  padding: 1rem;
}
</style>
</head>
<body>

<header>
  <h1><img src="pamanlinan.png" alt="Logo"> Barangay Pamanlinan Dashboard</h1>
  <nav>
    <a href="pamanlinan.php">Dashboard</a>
    <a href="all-forms.php">All Forms</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>

<main>
  <section class="dashboard" id="dashboard">
    <?php while ($row = $cards->fetch_assoc()): ?>
      <div class="card" data-id="<?= $row['id'] ?>" onclick="window.location.href='<?= htmlspecialchars($row['link']) ?>'">
        <div class="card-actions">
          <button class="edit-btn" onclick="editCard(event, <?= $row['id'] ?>)"><i class="fa-solid fa-pen-to-square"></i></button>
          <button class="delete-btn" onclick="deleteCard(event, <?= $row['id'] ?>)"><i class="fa-solid fa-trash"></i></button>
        </div>
        <i class="<?= htmlspecialchars($row['icon']) ?>"></i>
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p><?= htmlspecialchars($row['description']) ?></p>
      </div>
    <?php endwhile; ?>
  </section>
  <button class="add-btn" id="addBtn">+</button>
</main>

<!-- MODAL -->
<div class="modal" id="addModal">
  <div class="modal-content">
    <h2 id="modalTitle">Add New Form</h2>
    <form id="addForm">
      <input type="hidden" name="id" id="formId">
      <label>Form Title</label>
      <input type="text" name="title" id="title" required>

      <label>Description (Auto)</label>
      <textarea name="desc" id="desc" required readonly></textarea>

      <label>Page Link (e.g., newform.php)</label>
      <input type="text" name="link" id="link" required>

      <div class="modal-buttons">
        <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
        <button type="submit" class="btn-save">Save</button>
      </div>
    </form>
  </div>
</div>

<footer>
  &copy; 2025 Barangay Pamanlinan | All Rights Reserved
</footer>

<script>
const modal = document.getElementById("addModal");
const addBtn = document.getElementById("addBtn");
const cancelBtn = document.getElementById("cancelBtn");
const form = document.getElementById("addForm");
const descInput = document.getElementById("desc");

addBtn.onclick = () => { modal.style.display = "flex"; resetForm(); };
cancelBtn.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };

// 🧠 Dynamic Auto Description Generator
document.getElementById("title").addEventListener("input", (e) => {
  const title = e.target.value.trim().toLowerCase();
  let desc = "";

  if (title.includes("clearance")) desc = "Manage the processing and issuance of Barangay Clearance certificates.";
  else if (title.includes("registration")) desc = "Register new residents and maintain accurate demographic records.";
  else if (title.includes("household")) desc = "Monitor and manage household profiles and living conditions.";
  else if (title.includes("incident") || title.includes("case")) desc = "Log and track barangay-level incidents, disputes, and case reports.";
  else if (title.includes("service")) desc = "Oversee barangay programs, community services, and assistance distribution.";
  else if (title.includes("population") || title.includes("resident")) desc = "Maintain accurate records of the barangay population and residents.";
  else if (title.includes("bulletin") || title.includes("announcement")) desc = "Post news, announcements, and updates for residents.";
  else desc = `Manage and process ${title || 'this section'} efficiently within the barangay system.`;

  descInput.value = desc;
});

function getAutoIcon(title) {
  title = title.toLowerCase();
  if (title.includes("house")) return "fa-solid fa-house-user";
  if (title.includes("resident") || title.includes("registration")) return "fa-solid fa-id-card";
  if (title.includes("clearance") || title.includes("certificate")) return "fa-solid fa-file-signature";
  if (title.includes("incident") || title.includes("case")) return "fa-solid fa-exclamation-triangle";
  if (title.includes("service") || title.includes("aid")) return "fa-solid fa-hand-holding-heart";
  if (title.includes("population") || title.includes("people")) return "fa-solid fa-users";
  if (title.includes("bulletin") || title.includes("announcement")) return "fa-solid fa-bullhorn";
  return "fa-solid fa-folder";
}

// SAVE
form.onsubmit = async (e) => {
  e.preventDefault();
  const formData = new FormData(form);
  formData.append("icon", getAutoIcon(formData.get("title")));

  const res = await fetch("save_card.php", { method: "POST", body: formData });
  if (res.ok) location.reload();
};

// EDIT
function editCard(e, id) {
  e.stopPropagation();
  fetch(`get_card.php?id=${id}`)
    .then(r => r.json())
    .then(data => {
      document.getElementById("modalTitle").innerText = "Edit Form";
      document.getElementById("formId").value = data.id;
      document.getElementById("title").value = data.title;
      document.getElementById("desc").value = data.description;
      document.getElementById("link").value = data.link;
      modal.style.display = "flex";
    });
}

// DELETE
function deleteCard(e, id) {
  e.stopPropagation();
  if (confirm("Are you sure you want to delete this form?")) {
    fetch(`delete_card.php?id=${id}`).then(() => location.reload());
  }
}

function resetForm() {
  document.getElementById("modalTitle").innerText = "Add New Form";
  form.reset();
  document.getElementById("formId").value = "";
}
</script>
</body>
</html>
