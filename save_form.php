<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$title = $_POST['title'];
$desc = $_POST['desc'];
$link = $_POST['link'];
$icon = $_POST['icon'];

$stmt = $conn->prepare("INSERT INTO forms (title, description, link, icon) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $title, $desc, $link, $icon);
$stmt->execute();
$stmt->close();
$conn->close();

echo "success";
?>
