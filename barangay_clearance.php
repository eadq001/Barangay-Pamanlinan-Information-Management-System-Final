<?php
// barangay_clearance.php
session_start();

// Optional: Redirect to login if not logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Connect to your database (mysqli)
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX POST update from modal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_officials') {
    // Expect arrays: id[], name[], position[]
    $ids = $_POST['id'] ?? [];
    $names = $_POST['name'] ?? [];
    $positions = $_POST['position'] ?? [];

    // Basic validation: arrays must match length
    if (!is_array($ids) || !is_array($names) || !is_array($positions) || count($ids) !== count($names) || count($ids) !== count($positions)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    // Prepared statement to update each official
    $stmt = $conn->prepare("UPDATE barangay_officials SET name = ?, position = ? WHERE id = ?");
    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // iterate and update
    for ($i = 0; $i < count($ids); $i++) {
        $id = (int)$ids[$i];
        $name = trim($names[$i]);
        $position = trim($positions[$i]);

        // Avoid blank name - you can adjust this rule
        if ($name === '') $name = '[Not set]';

        $stmt->bind_param('ssi', $name, $position, $id);
        $stmt->execute();
    }
    $stmt->close();

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Officials updated.']);
    exit;
}

// Fetch officials to display
$officials_result = $conn->query("SELECT id, name, position FROM barangay_officials ORDER BY id ASC");
$officials = [];
if ($officials_result) {
    while ($r = $officials_result->fetch_assoc()) {
        $officials[] = $r;
    }
    $officials_result->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Barangay Clearance | Barangay Pamanlinan</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">
<style>
  /* --- Keep your existing styling but add modal styles and a small edit button --- */
  @page { size: A4; margin: 0.7in; }
  body { font-family: "Times New Roman", serif; background: #f4f7f8; color: #000; margin:0; padding:0; }
  .top-btn { text-align:left; margin:20px; }
  .top-btn button { background:#607d8b;color:#fff;border:none;padding:8px 18px;border-radius:6px;cursor:pointer;font-size:15px;box-shadow:0 2px 5px rgba(0,0,0,0.2); }
  .container { width:8.27in; min-height:11.69in; background:#fff; margin:0 auto 20px auto; position:relative; border:1px solid #ccc; box-shadow:0 0 10px rgba(0,0,0,0.2); padding:0.7in; box-sizing:border-box; overflow:hidden; }
  .watermark { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:70%; opacity:0.08; z-index:0; pointer-events:none; }
  .content { position:relative; z-index:2; }
  .header { text-align:center; line-height:1.2; position:relative; }
  .header img { position:absolute; }
  .seal { top:0.8in; left:0.1in; width:100px; }
  .logo-right { top:0.8in; right:0.1in; width:120px; }
  h3,h4,h2 { margin:0; }
  h2 { margin-top:10px; text-transform:uppercase; text-decoration:underline; }

  .row { display:flex; justify-content:space-between; margin-top:20px; }
  .left-column { width:40%; font-size:14px; line-height:1.5; }
  .left-column strong { display:block; font-size:15px; }
  .right-column { width:58%; font-size:15px; line-height:1.6; text-align:justify; text-indent:50px; }

  input[type="text"], input[type="date"] { border:none; border-bottom:1px solid #000; font-family:inherit; font-size:15px; background:transparent; text-align:center; outline:none; }

  .signature { margin-top:40px; text-align:right; font-weight:bold; }
  .attested { text-align:right; margin-top:15px; }
  .footer { margin-top:25px; font-size:14px; line-height:1.4; }
  .thumbmark-box { display:flex; justify-content:space-between; width:40%; margin-top:10px; }
  .thumbmark-box div { border:1px solid #000; width:48%; height:60px; }
  .print-btn { text-align:center; margin-top:20px; }
  button.primary { background:#004d40; color:#fff; border:none; padding:10px 22px; border-radius:6px; cursor:pointer; font-size:15px; }
  button.primary:hover { background:#00796b; }

  /* Edit button */
  .edit-officials-btn { background:#0b6b29; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; font-weight:600; box-shadow:0 2px 6px rgba(0,0,0,0.12); margin-bottom:12px; }
  .edit-officials-btn:hover { background:#15803d; }

  /* Modal */
  .modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); align-items:center; justify-content:center; z-index:9999; }
  .modal.show { display:flex; }
  .modal-content { width:90%; max-width:760px; background:#fff; border-radius:10px; padding:18px; box-shadow:0 8px 30px rgba(0,0,0,0.2); max-height:90vh; overflow:auto; }
  .modal header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
  .modal header h3{ margin:0; color:#0b6b29; }
  .modal .row-grid { display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:8px; align-items:center; }
  .modal label { font-weight:600; color:#0b6b29; font-size:13px; display:block; margin-bottom:6px; }
  .modal input[type="text"] { width:100%; padding:8px; border:1px solid #ccc; border-radius:6px; }
  .modal .actions { display:flex; justify-content:flex-end; gap:8px; margin-top:12px; }
  .btn-cancel { background:#888; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
  .btn-save { background:#0b6b29; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }

  @media print { .top-btn, .print-btn, .edit-officials-btn { display:none; } .container { box-shadow:none; border:none; margin:0; } }
</style>
</head>
<body>

<!-- BACK BTN -->
<div class="top-btn">
  <button onclick="window.location.href='pamanlinan.php'">⬅ Back to Dashboard</button>
</div>

<div class="container">
  <!-- watermark -->
  <img src="pamanlinan.png" class="watermark" alt="Watermark">

  <div class="content">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <!-- Edit button placed above left-column; prints hidden -->
        <button class="edit-officials-btn" id="openEditModal">✎ Edit Officials</button>
      </div>
      <div style="text-align:right;">
        <!-- (keeps header area clean) -->
      </div>
    </div>

    <div class="header">
      <img src="pamanlinan.png" class="seal" alt="Seal">
      <img src="Bagong-Pilipinas-Logo-1966x2048.png" class="logo-right" alt="Logo">
      <h4>Republic of the Philippines</h4>
      <h4>Office of the Punong Barangay</h4>
      <h3><strong>BARANGAY PAMANLINAN</strong></h3>
      <p>Bislig City, Surigao del Sur, District II, Caraga Region XIII</p>
      <br>
      <p><strong>TO WHOM THESE PRESENTS MAY COME</strong></p>
      <h2>BARANGAY CLEARANCE</h2>
    </div>

    <div class="row">
      <!-- LEFT: OFFICIALS (dynamic) -->
      <div class="left-column" id="officialsList">
        <?php if (count($officials) > 0): ?>
          <?php foreach ($officials as $off): ?>
            <strong><?= htmlspecialchars($off['name']) ?></strong> <?= htmlspecialchars($off['position']) ?><br>
          <?php endforeach; ?>
        <?php else: ?>
          <em>No officials found. Use Edit to add.</em>
        <?php endif; ?>
      </div>

      <!-- RIGHT: FORM CONTENT -->
      <div class="right-column">
        <p>THIS IS TO CERTIFY that 
        <input type="text" placeholder="Full Name" style="width:220px;">, of legal age, 
        <input type="text" placeholder="Civil Status" style="width:100px;">, residing at Purok 
        <input type="text" placeholder="____" style="width:60px;">, Barangay Pamanlinan, Bislig City, is a person of <strong>good moral character and has no derogatory record or pending civil/criminal case</strong> in this office as of this date. He/She is a law-abiding citizen and not a member of any subversive organization.</p>

        <p>This clearance is issued upon request as a requirement for 
        <input type="text" placeholder="Purpose" style="width:250px;">.</p>

        <p>WITNESS MY SIGNATURE this 
        <input type="date" style="width:160px;"> at Barangay Pamanlinan, Bislig City, Surigao del Sur.</p>

        <div class="signature">
          <p>JENNIFER M. MAGNO<br><em>Punong Barangay</em></p>
        </div>

        <div class="attested">
          <p><strong>Attested by:</strong><br>
          SHERLITA T. RAMOS<br><em>Barangay Secretary</em></p>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="thumbmark-box">
        <div></div><div></div>
      </div>
      <small>Left Thumb &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Right Thumb</small>

      <p>Name: <input type="text" style="width:200px;"> &nbsp; CTC No.: <input type="text" style="width:120px;"></p>

      <p>Issued on: <input type="date" style="width:140px;"> &nbsp; Issued at: <input type="text" style="width:140px;"></p>

      <p>O.R. No.: <input type="text" style="width:120px;"> &nbsp; Issued on: <input type="date" style="width:140px;"> &nbsp; Issued at: <input type="text" style="width:140px;"></p>

      <p style="font-style: italic;">Not valid without the official seal</p>
    </div>
  </div>
</div>

<div class="print-btn">
  <button class="primary" onclick="window.print()">🖨 Print Barangay Clearance</button>
</div>

<!-- Modal: Edit Officials -->
<div class="modal" id="editModal" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="editOfficialsTitle">
    <header>
      <h3 id="editOfficialsTitle">Edit Officials</h3>
      <button class="btn-cancel" id="closeModalTop" title="Close">✕</button>
    </header>

    <!-- Form - we generate inputs for each official -->
    <form id="editOfficialsForm">
      <input type="hidden" name="action" value="update_officials">

      <div id="officialsFieldsContainer">
        <?php if (count($officials) > 0): ?>
          <?php foreach ($officials as $off): ?>
            <div class="row-grid" data-official-id="<?= (int)$off['id'] ?>">
              <div>
                <label>Name</label>
                <input type="text" name="name[]" value="<?= htmlspecialchars($off['name']) ?>" required>
                <input type="hidden" name="id[]" value="<?= (int)$off['id'] ?>">
              </div>
              <div>
                <label>Position</label>
                <input type="text" name="position[]" value="<?= htmlspecialchars($off['position']) ?>">
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <!-- If none exist, provide 5 empty rows so user can add (you can change count) -->
          <?php for ($i=0; $i<5; $i++): ?>
            <div class="row-grid">
              <div>
                <label>Name</label>
                <input type="text" name="name[]" value="">
                <input type="hidden" name="id[]" value="0">
              </div>
              <div>
                <label>Position</label>
                <input type="text" name="position[]" value="">
              </div>
            </div>
          <?php endfor; ?>
        <?php endif; ?>
      </div>

      <div style="display:flex; gap:8px; margin-top:10px;">
        <button type="button" class="btn-cancel" id="closeModal">Cancel</button>
        <button type="submit" class="btn-save">Save Changes</button>
      </div>

      <p id="modalMsg" style="color:green; margin-top:10px; display:none;"></p>
    </form>
  </div>
</div>

<script>
  // Modal open/close
  const editBtn = document.getElementById('openEditModal');
  const editModal = document.getElementById('editModal');
  const closeModal = document.getElementById('closeModal');
  const closeModalTop = document.getElementById('closeModalTop');

  function showModal(){ editModal.classList.add('show'); editModal.setAttribute('aria-hidden','false'); }
  function hideModal(){ editModal.classList.remove('show'); editModal.setAttribute('aria-hidden','true'); }

  editBtn.addEventListener('click', showModal);
  closeModal && closeModal.addEventListener('click', hideModal);
  closeModalTop && closeModalTop.addEventListener('click', hideModal);
  window.addEventListener('click', (e)=>{ if (e.target === editModal) hideModal(); });

  // Submit via fetch (AJAX)
  document.getElementById('editOfficialsForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    // Basic client-side validation (at least one name)
    let anyName = false;
    for (let pair of formData.getAll('name[]')) {
      if (pair.trim() !== '') { anyName = true; break; }
    }
    if (!anyName) {
      alert('Please provide at least one official name.');
      return;
    }

    // show saving text
    const modalMsg = document.getElementById('modalMsg');
    modalMsg.style.display = 'block';
    modalMsg.style.color = '#0b6b29';
    modalMsg.textContent = 'Saving changes...';

    try {
      const res = await fetch(window.location.href, {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
      });
      const data = await res.json();
      if (data.success) {
        modalMsg.style.color = 'green';
        modalMsg.textContent = 'Saved successfully — reloading...';
        setTimeout(()=> location.reload(), 900);
      } else {
        modalMsg.style.color = 'crimson';
        modalMsg.textContent = 'Error: ' + (data.message || 'Unable to save.');
      }
    } catch (err) {
      modalMsg.style.color = 'crimson';
      modalMsg.textContent = 'Network or server error.';
      console.error(err);
    }
  });
</script>

</body>
</html>
