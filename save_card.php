<?php
$conn = new mysqli("localhost", "root", "", "pamanlinan_db");

$id = $_POST['id'] ?? '';
$title = $_POST['title'];
$desc = $_POST['desc'];
$link = $_POST['link'];
$icon = $_POST['icon'];

if ($id) {
    $stmt = $conn->prepare("UPDATE dashboard_cards SET title=?, description=?, link=?, icon=? WHERE id=?");
    $stmt->bind_param("ssssi", $title, $desc, $link, $icon, $id);
} else {
    $stmt = $conn->prepare("INSERT INTO dashboard_cards (title, description, link, icon) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $title, $desc, $link, $icon);
}
$stmt->execute();
?>
