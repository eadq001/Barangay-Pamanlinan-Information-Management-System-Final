<?php
require_once __DIR__ . '/../connection.php';

$sql = "CREATE TABLE IF NOT EXISTS `cases` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `purok` VARCHAR(50) NOT NULL,
    `barangay` VARCHAR(100) NOT NULL,
    `location` TEXT NOT NULL,
    `incident_date` DATE NOT NULL,
    `incident_time` TIME NOT NULL,
    `police_notified` ENUM('Yes', 'No') NOT NULL,
    `incident_type` VARCHAR(100) NOT NULL,
    `injuries_sustained` TEXT,
    `doc_path` VARCHAR(255),
    `pdf_path` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (mysqli_query($conn, $sql)) {
    echo "Cases table created successfully\n";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "\n";
}