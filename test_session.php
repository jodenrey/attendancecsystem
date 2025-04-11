<?php
// Show all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "Includes/dbcon.php";

echo "<h1>School Year Table Test</h1>";

// Check the table structure
$query = mysqli_query($conn, "SHOW CREATE TABLE tblsessionterm");
if(!$query) {
    echo "<p style='color:red'>Error checking table: " . mysqli_error($conn) . "</p>";
} else {
    $row = mysqli_fetch_array($query);
    echo "<h2>Table Structure:</h2>";
    echo "<pre>" . htmlspecialchars($row['Create Table']) . "</pre>";
}

// Create test record
echo "<h2>Test Record Creation:</h2>";

// Remove termId from the insert if it exists
$checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM tblsessionterm LIKE 'termId'");
$termColumnExists = mysqli_num_rows($checkColumn) > 0;

$testYear = "2025/2026";
$dateCreated = date("Y-m-d");

try {
    if ($termColumnExists) {
        $query = mysqli_query($conn, "INSERT INTO tblsessionterm (sessionName, termId, isActive, dateCreated) VALUES ('$testYear', '1', '0', '$dateCreated')");
    } else {
        $query = mysqli_query($conn, "INSERT INTO tblsessionterm (sessionName, isActive, dateCreated) VALUES ('$testYear', '0', '$dateCreated')");
    }
    
    if ($query) {
        $newId = mysqli_insert_id($conn);
        echo "<p style='color:green'>Test record created successfully with ID: $newId</p>";
    } else {
        echo "<p style='color:red'>Error creating test record: " . mysqli_error($conn) . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
}

echo "<h2>Existing Records:</h2>";
$query = mysqli_query($conn, "SELECT * FROM tblsessionterm");
echo "<table border='1' cellpadding='5'>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>School Year</th>";
if ($termColumnExists) {
    echo "<th>Term ID</th>";
}
echo "<th>Is Active</th>";
echo "<th>Date Created</th>";
echo "</tr>";

while ($row = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    echo "<td>" . $row['Id'] . "</td>";
    echo "<td>" . $row['sessionName'] . "</td>";
    if ($termColumnExists) {
        echo "<td>" . $row['termId'] . "</td>";
    }
    echo "<td>" . $row['isActive'] . "</td>";
    echo "<td>" . $row['dateCreated'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check the auto-increment value
$query = mysqli_query($conn, "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'attendancemsystem01' AND TABLE_NAME = 'tblsessionterm'");
if ($query) {
    $row = mysqli_fetch_assoc($query);
    echo "<p>Current AUTO_INCREMENT value: " . ($row['AUTO_INCREMENT'] ?? 'Not set') . "</p>";
} else {
    echo "<p style='color:red'>Error checking AUTO_INCREMENT: " . mysqli_error($conn) . "</p>";
}
?>