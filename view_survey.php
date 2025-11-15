<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;
$result = $conn->query("SELECT * FROM household_housing WHERE id = $id");

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    die("<h2>No record found for this survey.</h2>");
}

function formatValues($value) {
    if (empty($value)) return "<i style='color:#888;'>No data</i>";
    $items = array_map('trim', explode(',', $value));
    $output = "";
    foreach ($items as $item) {
        $output .= "<span class='badge'>" . htmlspecialchars($item) . "</span> ";
    }
    return $output;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>View Household Survey</title>
<style>
:root {
  --primary: #0b6b2d;
  --primary-light: #15803d;
  --bg: #f4f7f5;
  --white: #fff;
  --shadow: 0 4px 10px rgba(0,0,0,0.08);
}
body {
  font-family: "Poppins", sans-serif;
  background: var(--bg);
  margin: 0;
  color: #222;
}
header {
  background: var(--primary);
  color: white;
  padding: 1.2rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: var(--shadow);
}
header a {
  color: white;
  text-decoration: none;
  background: rgba(255,255,255,0.15);
  padding: 0.4rem 1rem;
  border-radius: 6px;
  transition: 0.3s;
}
header a:hover { background: rgba(255,255,255,0.3); }

.container {
  background: var(--white);
  margin: 2rem auto;
  max-width: 900px;
  padding: 1.5rem 2rem;
  border-radius: 12px;
  box-shadow: var(--shadow);
}
h1 {
  text-align: center;
  color: var(--primary);
  font-size: 1.6rem;
  margin-bottom: 1rem;
}
.table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}
.table tr td {
  padding: 5px 8px;
  vertical-align: top;
  border-bottom: 1px solid #e0e0e0;
}
.table tr td:first-child {
  font-weight: 600;
  color: var(--primary);
  width: 35%;
}
.badge {
  display: inline-block;
  background: var(--primary);
  color: white;
  font-size: 0.8rem;
  padding: 2px 6px;
  margin: 1px;
  border-radius: 6px;
}
button {
  display: block;
  margin: 2rem auto;
  padding: 0.6rem 1.5rem;
  background: var(--primary);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  box-shadow: var(--shadow);
  transition: 0.3s;
}
button:hover { background: var(--primary-light); transform: translateY(-2px); }

/* ✅ PRINT STYLES - LONG BOND PAPER */
@media print {
  @page {
    size: 8.5in 13in; /* Long bond paper */
    margin: 10mm;
  }
  body {
    background: white;
    color: black;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
    transform: scale(0.95); /* Slight scale to fit everything neatly */
    transform-origin: top center;
  }
  header, button {
    display: none;
  }
  .container {
    box-shadow: none;
    border-radius: 0;
    padding: 5mm 10mm;
    margin: 0;
    width: 100%;
  }
  .table tr td {
    font-size: 10pt;
    border-bottom: 1px solid #ccc;
  }
  .badge {
    background: #0b6b2d !important;
    color: white !important;
    font-size: 9pt;
  }
  h1 {
    font-size: 14pt;
    color: #0b6b2d;
  }
}
</style>
</head>
<body>

<header>
  <h2>Barangay Pamanlinan Information System</h2>
  <a href="household.php">← Back to Form</a>
</header>

<div class="container">
  <h1>🏠 Household / Housing Survey Result</h1>
  <table class="table">
    <tr><td>Type of Building</td><td><?= formatValues($data['type_building']) ?></td></tr>
    <tr><td>Roof Material</td><td><?= formatValues($data['roof_material']) ?></td></tr>
    <tr><td>Wall Material</td><td><?= formatValues($data['wall_material']) ?></td></tr>
    <tr><td>State of Repair</td><td><?= formatValues($data['state_repair']) ?></td></tr>
    <tr><td>Floor Area</td><td><?= htmlspecialchars($data['floor_area']) ?></td></tr>
    <tr><td>Year Built</td><td><?= htmlspecialchars($data['year_built']) ?></td></tr>
    <tr><td>Monthly Rental</td><td><?= htmlspecialchars($data['monthly_rental']) ?></td></tr>
    <tr><td>Source of Income</td><td><?= formatValues($data['source_income']) ?></td></tr>
    <tr><td>Tenure Status</td><td><?= formatValues($data['tenure_status']) ?></td></tr>
    <tr><td>Acquisition</td><td><?= formatValues($data['acquisition']) ?></td></tr>
    <tr><td>Electricity Source</td><td><?= formatValues($data['electricity_source']) ?></td></tr>
    <tr><td>Fuel for Cooking</td><td><?= formatValues($data['fuel_cooking']) ?></td></tr>
    <tr><td>Fuel for Lighting</td><td><?= formatValues($data['fuel_lighting']) ?></td></tr>
    <tr><td>Garbage Disposal</td><td><?= formatValues($data['garbage_disposal']) ?></td></tr>
    <tr><td>Water (Drinking)</td><td><?= formatValues($data['water_drinking']) ?></td></tr>
    <tr><td>Water (Cooking)</td><td><?= formatValues($data['water_cooking']) ?></td></tr>
    <tr><td>Water (Laundry/Bathing)</td><td><?= formatValues($data['water_laundry']) ?></td></tr>
    <tr><td>Toilet Facility</td><td><?= formatValues($data['toilet_facility']) ?></td></tr>
    <tr><td>Internet Access</td><td><?= formatValues($data['internet_access']) ?></td></tr>
    <tr><td>Household Devices</td><td><?= formatValues($data['household_devices']) ?></td></tr>
  </table>

  <button onclick="window.print()">🖨️ Print Survey</button>
</div>

</body>
</html>
