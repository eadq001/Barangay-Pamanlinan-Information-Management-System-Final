<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM forms WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

echo "success";
?>
