<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");
$id = $_GET['id'];
$conn->query("DELETE FROM dashboard_cards WHERE id=$id");
?>
