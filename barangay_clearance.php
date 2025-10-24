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
<title>Barangay Clearance | Barangay Pamanlinan</title>
<link rel="shortcut icon" href="pamanlinan.png" type="image/x-icon">

<style>
  @page {
    size: A4;
    margin: 0.7in;
  }

  body {
    font-family: "Times New Roman", serif;
    background: #f4f7f8;
    color: #000;
    margin: 0;
    padding: 0;
  }

  /* ===== Top Back Button ===== */
  .top-btn {
    text-align: left;
    margin: 20px;
  }

  .top-btn button {
    background: #607d8b;
    color: #fff;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  }

  .top-btn button:hover {
    background: #455a64;
  }

  .container {
    width: 8.27in;
    min-height: 11.69in;
    background: #fff;
    margin: 0 auto 20px auto;
    position: relative;
    border: 1px solid #ccc;
    box-shadow: 0 0 10px rgba(0,0,0,0.2);
    padding: 0.7in;
    box-sizing: border-box;
    overflow: hidden;
  }

  /* Watermark */
  .watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 70%;
    opacity: 0.08;
    z-index: 0;
    pointer-events: none;
  }

  .content {
    position: relative;
    z-index: 2;
  }

  /* Header */
  .header {
    text-align: center;
    line-height: 1.2;
    position: relative;
  }

  .header img {
    position: absolute;
  }

  .seal {
    top: 0.8in;
    left: 0.1in;
    width: 100px;
  }

  .logo-right {
    top: 0.8in;
    right: 0.1in;
    width: 120px;
  }

  h3, h4, h2 {
    margin: 0;
  }

  h2 {
    margin-top: 10px;
    text-transform: uppercase;
    text-decoration: underline;
  }

  /* Layout for officials + clearance */
  .row {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
  }

  .left-column {
    width: 40%;
    font-size: 14px;
    line-height: 1.5;
  }

  .left-column strong {
    display: block;
    font-size: 15px;
  }

  .right-column {
    width: 58%;
    font-size: 15px;
    line-height: 1.6;
    text-align: justify;
    text-indent: 50px;
  }

  input[type="text"], input[type="date"] {
    border: none;
    border-bottom: 1px solid #000;
    font-family: inherit;
    font-size: 15px;
    text-align: center;
    background: transparent;
    outline: none;
  }

  .signature {
    margin-top: 40px;
    text-align: right;
    font-weight: bold;
  }

  .attested {
    text-align: right;
    margin-top: 15px;
  }

  /* Footer */
  .footer {
    margin-top: 25px;
    font-size: 14px;
    line-height: 1.4;
  }

  .thumbmark-box {
    display: flex;
    justify-content: space-between;
    width: 40%;
    margin-top: 10px;
  }

  .thumbmark-box div {
    border: 1px solid #000;
    width: 48%;
    height: 60px;
  }

  .footer small {
    display: block;
    margin-top: 3px;
    font-size: 13px;
  }

  .print-btn {
    text-align: center;
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 15px;
  }

  button {
    background: #004d40;
    color: #fff;
    border: none;
    padding: 10px 22px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 15px;
  }

  button:hover {
    background: #00796b;
  }

  @media print {
    .top-btn, .print-btn { display: none; }
    .container { box-shadow: none; border: none; margin: 0; }
    body { background: #fff; }
  }
</style>
</head>
<body>

<!-- ✅ Top Back Button -->
<div class="top-btn">
  <button onclick="window.location.href='pamanlinan.php'">⬅ Back to Dashboard</button>
</div>

<div class="container">
  <!-- ✅ Watermark -->
  <img src="pamanlinan.png" class="watermark" alt="Barangay Seal Watermark">

  <div class="content">
    <!-- Header -->
    <div class="header">
      <img src="pamanlinan.png" class="seal" alt="Barangay Seal">
      <img src="Bagong-Pilipinas-Logo-1966x2048.png" class="logo-right" alt="Bagong Pilipinas Logo">
      <h4>Republic of the Philippines</h4>
      <h4>Office of the Punong Barangay</h4>
      <h3><strong>BARANGAY PAMANLINAN</strong></h3>
      <p>Bislig City, Surigao del Sur, District II, Caraga Region XIII</p>
      <br>
      <p><strong>TO WHOM THESE PRESENTS MAY COME</strong></p>
      <h2>BARANGAY CLEARANCE</h2>
    </div>

    <div class="row">
      <!-- Left Column -->
      <div class="left-column">
        <strong>HON. JENNIFER M. MAGNO</strong> Punong Barangay<br>
        <strong>HON. BERNADETH P. DEMONTEVERDE</strong> Barangay Kagawad<br>
        <strong>HON. JERRY T. SAMPAYAN</strong> Barangay Kagawad<br>
        <strong>HON. LIMUEL D. OTUGAY</strong> Barangay Kagawad<br>
        <strong>HON. MICHELLE R. PENANDE</strong> Barangay Kagawad<br>
        <strong>HON. ARVIN D. MAGNO</strong> Barangay Kagawad<br>
        <strong>HON. ROQUE C. DELOS SANTOS</strong> Barangay Kagawad<br>
        <strong>HON. FLORITO S. JOSAFAT</strong> Barangay Kagawad<br>
        <strong>HON. EMMANUEL J. LAYUPAN</strong> IPMR<br>
        <strong>HON. DARWIN M. REBUTA</strong> SK Chairman<br>
        <strong>MRS. SHERLITA T. RAMOS</strong> Barangay Secretary<br>
        <strong>MRS. AVELINA F. PENANDE</strong> Barangay Treasurer
      </div>

      <!-- Right Column -->
      <div class="right-column">
        <p>THIS IS TO CERTIFY that <input type="text" placeholder="Full Name" style="width:220px;">, of legal age, 
        <input type="text" placeholder="Civil Status" style="width:100px;">, residing at Purok <input type="text" placeholder="____" style="width:60px;">, 
        Barangay Pamanlinan, Bislig City, is a person of <strong>good moral character and has no derogatory record or pending civil/criminal case</strong> in this office as of this date. 
        He/She is a law-abiding citizen and not a member of any subversive organization.</p>

        <p>This clearance is issued upon request as a requirement for <input type="text" placeholder="Purpose" style="width:250px;">.</p>

        <p>WITNESS MY SIGNATURE this <input type="date" style="width:160px;"> at Barangay Pamanlinan, Bislig City, Surigao del Sur.</p>

        <div class="signature">
          <p>JENNIFER M. MAGNO<br><em>Punong Barangay</em></p>
        </div>

        <div class="attested">
          <p><strong>Attested by:</strong><br>
          SHERLITA T. RAMOS<br><em>Barangay Secretary</em></p>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <div class="thumbmark-box">
        <div></div>
        <div></div>
      </div>
      <small>Left Thumb &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Right Thumb</small>

      <p>Name: <input type="text" style="width:200px;"> &nbsp;&nbsp;&nbsp;&nbsp;
      CTC No.: <input type="text" style="width:120px;"></p>
      <p>Issued on: <input type="date" style="width:140px;"> &nbsp;&nbsp;&nbsp;&nbsp;
      Issued at: <input type="text" style="width:140px;"></p>

      <p>O.R. No.: <input type="text" style="width:120px;"> &nbsp;&nbsp;&nbsp;&nbsp;
      Issued on: <input type="date" style="width:140px;"> &nbsp;&nbsp;&nbsp;&nbsp;
      Issued at: <input type="text" style="width:140px;"></p>

      <p style="font-style: italic;">Not valid without the official seal</p>
    </div>
  </div>
</div>

<div class="print-btn">
  <button onclick="window.print()">🖨 Print Barangay Clearance</button>
</div>

</body>
</html>
