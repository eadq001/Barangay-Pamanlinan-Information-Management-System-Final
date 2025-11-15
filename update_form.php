<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$title = $_POST['title'];
$desc = $_POST['desc'];
$link = $_POST['link'];
$icon = $_POST['icon'];

$stmt = $conn->prepare("UPDATE forms SET title=?, description=?, link=?, icon=? WHERE id=?");
$stmt->bind_param("ssssi", $title, $desc, $link, $icon, $id);
$stmt->execute();
$stmt->close();
$conn->close();

echo "success";
?>
