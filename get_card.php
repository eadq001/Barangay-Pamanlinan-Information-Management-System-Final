<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM dashboard_cards WHERE id=$id");
echo json_encode($result->fetch_assoc());
?>
