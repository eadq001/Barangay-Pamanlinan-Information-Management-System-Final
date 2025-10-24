<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "pamanlinan_db";

// Establish a mysqli connection using the commonly-used variable name `$conn`.
// Some files in the project use `$conn`, others may use `$con` — set both to
// maintain backward compatibility.
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Keep legacy variable name available if any files reference `$con`.
$con = $conn;

