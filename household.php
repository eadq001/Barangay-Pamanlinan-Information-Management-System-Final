<?php
// ✅ Database Connection
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Form Processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Helper function for collecting checkbox and "others" field
    function getValues($name) {
        $values = [];
        if (!empty($_POST[$name]) && is_array($_POST[$name])) {
            foreach ($_POST[$name] as $val) {
                if (trim($val) !== "") {
                    $values[] = htmlspecialchars($val);
                }
            }
        }
        $otherField = $name . "_others";
        if (!empty($_POST[$otherField])) {
            $values[] = "Others: " . htmlspecialchars($_POST[$otherField]);
        }
        return implode(", ", $values);
    }

    // ✅ All form fields
    $fields = [
        'type_building','roof_material','wall_material','state_repair','floor_area','year_built',
        'monthly_rental','source_income','tenure_status','acquisition','electricity_source',
        'fuel_cooking','fuel_lighting','garbage_disposal','water_drinking','water_cooking',
        'water_laundry','toilet_facility','internet_access','household_devices'
    ];

    // Collect all data
    $values = [];
    foreach ($fields as $f) {
        $values[$f] = getValues($f);
    }

    // ✅ Insert into database
    $stmt = $conn->prepare("
        INSERT INTO household_housing 
        (type_building, roof_material, wall_material, state_repair, floor_area, year_built, monthly_rental, 
         source_income, tenure_status, acquisition, electricity_source, fuel_cooking, fuel_lighting, 
         garbage_disposal, water_drinking, water_cooking, water_laundry, toilet_facility, internet_access, household_devices)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
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
        echo "<script>
            window.onload = function() {
                const popup = document.getElementById('popup');
                popup.classList.add('show');
                setTimeout(() => {
                    window.location.href = 'view_survey.php?id=$last_id';
                }, 1500);
            }
        </script>";
    } else {
        echo "<script>alert('Error saving data: " . addslashes($stmt->error) . "');</script>";
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
:root {
  --primary: #0b6b2d;
  --primary-light: #15803d;
  --bg: #f4f7f5;
  --white: #ffffff;
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
header h2 { font-size: 1.3rem; font-weight: 600; }
header a {
  color: white; text-decoration: none; background: rgba(255,255,255,0.15);
  padding: 0.4rem 1rem; border-radius: 6px; transition: 0.3s;
}
header a:hover { background: rgba(255,255,255,0.3); }
.container {
  background: var(--white);
  margin: 2rem auto;
  max-width: 1200px;
  padding: 2rem 2.5rem;
  border-radius: 12px;
  box-shadow: var(--shadow);
}
.container h1 {
  text-align: center; color: var(--primary);
  font-size: 1.7rem; margin-bottom: 0.3rem;
}
.container p {
  text-align: center; color: #666;
  margin-bottom: 2rem; font-size: 0.95rem;
}
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
  gap: 1.2rem;
}
.section {
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  padding: 1rem 1.3rem;
  background: #fcfcfc;
  transition: 0.3s;
}
.section:hover { box-shadow: var(--shadow); transform: translateY(-2px); }
.section h2 {
  font-size: 1.05rem; color: var(--primary); text-align: center;
  border-bottom: 2px solid var(--primary); padding-bottom: 6px; margin-bottom: 0.6rem;
}
label { display: block; font-size: 0.9rem; margin: 6px 0; }
input[type="checkbox"] {
  transform: scale(1.1); margin-right: 8px; accent-color: var(--primary);
}
input[type="text"] {
  width: 60%; padding: 5px; border: 1px solid #ccc;
  border-radius: 6px; margin-left: 6px;
}
button {
  display: block; width: 260px; margin: 2rem auto 1rem;
  padding: 0.8rem; background: var(--primary);
  color: white; font-size: 1rem; border: none; border-radius: 8px;
  cursor: pointer; transition: 0.3s; box-shadow: var(--shadow);
}
button:hover { background: var(--primary-light); transform: translateY(-2px); }
.popup {
  display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45);
  justify-content: center; align-items: center; z-index: 1000;
}
.popup.show { display: flex; }
.popup-content {
  background: white; padding: 2rem 3rem; border-radius: 12px;
  box-shadow: var(--shadow); text-align: center; animation: fadeIn 0.4s ease;
}
@keyframes fadeIn { from {opacity: 0; transform: scale(0.9);} to {opacity: 1; transform: scale(1);} }
</style>
</head>
<body>

<header>
  <h2>Barangay Pamanlinan Information System</h2>
  <a href="pamanlinan.php">← Back to Dashboard</a>
</header>

<form method="POST" class="container">
  <h1>🏠 Household / Housing Survey Form</h1>
  <p>Republic of the Philippines • Province of Camarines Sur<br>Municipality of Gainza • Barangay Pamanlinan</p>

  <div class="form-grid">
<?php
function checkboxGroup($title, $name, $items) {
    echo "<div class='section'><h2>$title</h2>";
    foreach ($items as $item) {
        if ($item === 'Others') {
            echo "<label><input type='checkbox' name='{$name}[]' value='Others'> Others: 
                  <input type='text' name='{$name}_others' placeholder='Specify here'></label>";
        } else {
            echo "<label><input type='checkbox' name='{$name}[]' value='$item'> $item</label>";
        }
    }
    echo "</div>";
}

checkboxGroup("Type of Building/House","type_building",["Single House","Duplex","Apartment/Rowhouse","Commercial/Industrial","Institutional","Others"]);
checkboxGroup("Roof Material","roof_material",["Strong materials","Light materials","Makeshift","Others"]);
checkboxGroup("Wall Material","wall_material",["Concrete/Brick/Stone","Wood","Light materials","Makeshift","Others"]);
checkboxGroup("State of Repair","state_repair",["Needs no repair","Minor repair","Major repair","Dilapidated","Under construction","Others"]);
checkboxGroup("Floor Area","floor_area",["Below 10 sq.m","10-19 sq.m","20-29 sq.m","30-39 sq.m","40-49 sq.m","50 sq.m and above"]);
checkboxGroup("Year Built","year_built",["2000 & later","1991-2000","1981-1990","1971-1980","1960 & earlier"]);
checkboxGroup("Monthly Rental","monthly_rental",["Owned","Below ₱1000","₱1001 - 3000","₱3001 - 5000","₱5001 and over"]);
checkboxGroup("Source of Income","source_income",["Wages/Salary","Business","Remittances","Others"]);
checkboxGroup("Tenure Status","tenure_status",["Owned","Rented","Rent-free (with consent)","Rent-free (without consent)","Others"]);
checkboxGroup("Acquisition of Unit","acquisition",["Purchased","Constructed","Government housing","Inherited","Others"]);
checkboxGroup("Electricity Source","electricity_source",["Solar","Generator","Others"]);
checkboxGroup("Fuel for Cooking","fuel_cooking",["LPG","Wood","Charcoal","Others"]);
checkboxGroup("Fuel for Lighting","fuel_lighting",["Electricity","Kerosene","Solar","None","Others"]);
checkboxGroup("Garbage Disposal","garbage_disposal",["Collected","Burning","Burying","Composting","Feeding to animals","Others"]);
checkboxGroup("Water (Drinking)","water_drinking",["Water District","Deep well","Spring","Bottled","Rain","Others"]);
checkboxGroup("Water (Cooking)","water_cooking",["Water District","Deep well","Spring","Rain","Others"]);
checkboxGroup("Water (Laundry/Bathing)","water_laundry",["Water District","Deep well","Spring","Rain","Others"]);
checkboxGroup("Toilet Facility","toilet_facility",["Water-sealed","Closed pit","Open pit","None","Others"]);
checkboxGroup("Internet Access","internet_access",["Yes","No"]);
checkboxGroup("Household Devices","household_devices",["TV","Cellphone","Computer","Refrigerator","Vehicle","Others"]);
?>
  </div>

  <button type="submit">Submit Survey</button>
</form>

<div class="popup" id="popup">
  <div class="popup-content">
    <h3>✅ Survey Submitted Successfully!</h3>
    <p>Redirecting to view page...</p>
  </div>
</div>

</body>
</html>
