<?php
// === Database Connection ===
$conn = new mysqli("localhost", "root", "", "pamanlinan_db"); 
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// === Handle Form Submission ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    function getValues($name) {
        return isset($_POST[$name]) ? implode(", ", $_POST[$name]) : '';
    }

    // Collect all field values
    $fields = [
        'type_building','roof_material','wall_material','state_repair','floor_area','year_built',
        'monthly_rental','source_income','tenure_status','acquisition','electricity_source',
        'fuel_cooking','fuel_lighting','garbage_disposal','water_drinking','water_cooking',
        'water_laundry','toilet_facility','internet_access','household_devices'
    ];

    $values = [];
    foreach ($fields as $f) $values[$f] = getValues($f);

    $stmt = $conn->prepare("INSERT INTO household_housing 
    (type_building, roof_material, wall_material, state_repair, floor_area, year_built, monthly_rental, 
     source_income, tenure_status, acquisition, electricity_source, fuel_cooking, fuel_lighting, 
     garbage_disposal, water_drinking, water_cooking, water_laundry, toilet_facility, internet_access, 
     household_devices)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
     
    $stmt->bind_param(
        "ssssssssssssssssssss",
        $values['type_building'],$values['roof_material'],$values['wall_material'],$values['state_repair'],
        $values['floor_area'],$values['year_built'],$values['monthly_rental'],$values['source_income'],
        $values['tenure_status'],$values['acquisition'],$values['electricity_source'],$values['fuel_cooking'],
        $values['fuel_lighting'],$values['garbage_disposal'],$values['water_drinking'],$values['water_cooking'],
        $values['water_laundry'],$values['toilet_facility'],$values['internet_access'],$values['household_devices']
    );

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        header("Location: view_survey.php?id=" . $last_id);
        exit();
    } else {
        echo "<script>alert('Error saving data!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Household / Housing Survey Form</title>
<style>
body {
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(to right, #6ca0a3, #ffffff);
    margin: 0;
    padding: 20px;
}
.container {
    max-width: 1400px;
    background: #fff;
    padding: 20px;
    margin: auto;
    border-radius: 8px;
}
h1, h2 { text-align: center; color: #333; }
.header { text-align: center; margin-bottom: 20px; }
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}
.section {
    border: 1px solid #ccc;
    border-radius: 6px;
    padding: 12px;
    background: #fdfdfd;
}
.section h2 {
    font-size: 16px;
    margin-bottom: 8px;
    color: #444;
    text-align: center;
}
label { display: block; margin: 4px 0; font-size: 14px; }
input[type="text"] {
    width: 80%; padding: 5px; margin-top: 5px;
    border: 1px solid #ccc; border-radius: 4px;
}
button {
    display: block; width: 30%; padding: 14px;
    background: #28a745; color: #fff; font-size: 16px;
    border: none; border-radius: 6px; cursor: pointer;
    margin-top: 25px; margin-left: 50%; transform: translateX(-50%);
    transition: background 0.3s;
}
button:hover { background: #218838; }
.btn {
    display: inline-block; padding: 10px 20px; background: #007bff;
    color: #fff; border-radius: 6px; text-decoration: none; font-size: 15px;
}
.btn:hover { background: #0056b3; }
@media(max-width: 768px) {
    .form-grid { grid-template-columns: 1fr; }
    input[type="text"] { width: 100%; }
}
</style>
</head>
<body>
<div class="back-btn">
    <a href="pamanlinan.php" class="btn">⬅ Back to Dashboard</a>
</div>

<form method="POST" class="container">
  <div class="header">
    <h1>Household / Housing Survey Form</h1>
    <p>Republic of the Philippines • Province of Surigao del Sur<br>
    City of Bislig • Barangay Pamanlinan</p>
  </div>

  <div class="form-grid">
  <?php
  function checkboxGroup($title,$name,$items){
      echo "<div class='section'><h2>$title</h2>";
      foreach($items as $item){
          if(strpos($item,'Others')!==false){
              echo "<label><input type='checkbox' name='{$name}[]' value='Others'> Others, Specify 
              <input type='text' name='{$name}[]'></label>";
          } else {
              echo "<label><input type='checkbox' name='{$name}[]' value='$item'> $item</label>";
          }
      }
      echo "</div>";
  }

  // === Form Sections ===
  checkboxGroup("Type of Building/House","type_building",[
  "Single House","Duplex","Apartment/Accessoria/Rowhouse/Townhouse",
  "Commercial/Industrial/Agricultural Building used as dwelling unit",
  "Institutional living quarter (school dormitory, hospital, etc.)","Others"]);

  checkboxGroup("Construction Materials of the Roof","roof_material",[
  "Strong materials (Galvanized iron/Aluminum/Tile/Concrete/Clay tile/Asbestos)",
  "Light materials (Cogon/Nipa/Anahaw)","Salvaged/Makeshift/Improvised materials","Others"]);

  checkboxGroup("Outer Wall Materials","wall_material",[
  "Strong materials (Concrete/Brick/Stone/Wood)",
  "Light materials (Bamboo/Salvaged materials)","Makeshift/Improvised materials","Others"]);

  checkboxGroup("State of Repair","state_repair",[
  "Needs no repair","Needs minor repair","Needs major repair","Dilapidated/Condemned",
  "Under construction","Unfinished construction","Others"]);

  checkboxGroup("Floor Area","floor_area",[
  "Below 10 sq. m","10-19 sq. m","20-29 sq. m","30-39 sq. m","40-49 sq. m",
  "50-69 sq. m","70-99 sq. m","100 sq. m and over"]);

  checkboxGroup("Year Built","year_built",[
  "2000 & later","1991-2000","1981-1990","1971-1980","1961-1970","1960 & earlier"]);

  checkboxGroup("Monthly Rental","monthly_rental",[
  "None (Owned)","Below PHP 1000","PHP 1001 - 3000","PHP 3001 - 5000","PHP 5001 - 10000","PHP 10001 and over"]);

  checkboxGroup("Source of Income","source_income",[
  "Wages/Salary","Entrepreneurial activities","Domestic household activities","Others"]);

  checkboxGroup("Tenure Status","tenure_status",[
  "Owned/being amortized by owner","Rented","Rent-free with consent of owner","Rent-free without consent of owner","Others"]);

  checkboxGroup("Acquisition of Unit","acquisition",[
  "Purchased","Constructed by owner","Government housing program","Inherited","Others"]);

  checkboxGroup("Electricity Source","electricity_source",["Solar","Generator","Others"]);
  checkboxGroup("Fuel for Cooking","fuel_cooking",["LPG","Kerosene","Wood","Others"]);
  checkboxGroup("Fuel for Lighting","fuel_lighting",["Electricity","Kerosene","LPG","Oil","Others","None"]);
  checkboxGroup("Usual Manner of Garbage Disposal","garbage_disposal",["Picked up by garbage truck","Burning","Composting","Burying","Feeding to animals","Others"]);
  checkboxGroup("Source of Water Supply (Drinking)","water_drinking",["Water District","Deep Well","Shared Well","Spring","Rain","Bottled","Others"]);
  checkboxGroup("Source of Water Supply (Cooking)","water_cooking",["Water District","Deep Well","Shared Well","Spring","Rain","Bottled","Others"]);
  checkboxGroup("Source of Water Supply (Laundry/Bathing)","water_laundry",["Water District","Deep Well","Shared Well","Spring","Rain","Bottled","Others"]);
  checkboxGroup("Kind of Toilet Facility","toilet_facility",["Water-sealed (exclusive)","Water-sealed (shared)","Closed pit","Open pit","Others","None"]);
  checkboxGroup("Internet Access","internet_access",["Yes","No"]);
  checkboxGroup("Household Devices","household_devices",["Television","Cellphone","Computer","Refrigerator","Washing Machine","Vehicle","Others"]);
  ?>
  </div>

  <button type="submit">Submit</button>
</form>
</body>
</html>
