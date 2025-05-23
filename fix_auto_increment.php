<?php
include "./Includes/dbcon.php";

// Get the max ID from the table
$query = mysqli_query($conn, "SELECT MAX(Id) as max_id FROM tblsessionterm");
$result = mysqli_fetch_assoc($query);
$max_id = $result["max_id"] ? $result["max_id"] : 0;

// Set the auto-increment to max_id + 1
$max_id++;
$alter_query = mysqli_query($conn, "ALTER TABLE tblsessionterm AUTO_INCREMENT = $max_id");

if ($alter_query) {
    echo "Auto-increment updated successfully to: $max_id";
} else {
    echo "Error updating auto-increment: " . mysqli_error($conn);
}
?>
