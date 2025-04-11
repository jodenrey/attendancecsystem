<?php
include "./Includes/dbcon.php";

// Display header
echo "<h1>School Year Fix</h1>";

// 1. Find any school years with ID=0
$zeroQuery = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE Id = 0");
$hasZero = mysqli_num_rows($zeroQuery) > 0;

if($hasZero) {
    // Get next available ID (max ID + 1)
    $maxQuery = mysqli_query($conn, "SELECT MAX(Id) as maxId FROM tblsessionterm WHERE Id > 0");
    $maxRow = mysqli_fetch_assoc($maxQuery);
    $nextId = $maxRow['maxId'] ? ($maxRow['maxId'] + 1) : 1;
    
    // Update records with ID=0
    $updateQuery = mysqli_query($conn, "UPDATE tblsessionterm SET Id = $nextId WHERE Id = 0");
    
    if($updateQuery) {
        echo "<p style='color:green; font-weight:bold;'>✅ Success! Fixed school year(s) with ID=0 by assigning ID=$nextId</p>";
    } else {
        echo "<p style='color:red; font-weight:bold;'>❌ Error updating records: " . mysqli_error($conn) . "</p>";
    }

    // Reset auto_increment
    $resetQuery = mysqli_query($conn, "ALTER TABLE tblsessionterm AUTO_INCREMENT = " . ($nextId + 1));
    if($resetQuery) {
        echo "<p style='color:green;'>✅ Auto increment reset successfully</p>";
    } else {
        echo "<p style='color:red;'>❌ Error resetting auto increment: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>No school years with ID=0 found.</p>";
    
    // Check for other problematic records
    $query = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE sessionName = '2024/2025'");
    if(mysqli_num_rows($query) > 0) {
        $row = mysqli_fetch_assoc($query);
        echo "<p>School year 2024/2025 exists with ID: " . $row['Id'] . "</p>";
    } else {
        // Create the school year with a proper ID
        $insertQuery = mysqli_query($conn, "INSERT INTO tblsessionterm (sessionName, isActive, dateCreated) 
                                          VALUES ('2024/2025', '0', '" . date('Y-m-d') . "')");
        if($insertQuery) {
            $newId = mysqli_insert_id($conn);
            echo "<p style='color:green;'>✅ Created 2024/2025 school year with ID: " . $newId . "</p>";
        } else {
            echo "<p style='color:red;'>❌ Error creating school year: " . mysqli_error($conn) . "</p>";
        }
    }
}

// Display current school years
echo "<h2>Current School Years</h2>";
$query = mysqli_query($conn, "SELECT * FROM tblsessionterm ORDER BY Id");
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>School Year</th><th>Is Active</th><th>Date Created</th></tr>";

while($row = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    echo "<td>" . $row['Id'] . "</td>";
    echo "<td>" . $row['sessionName'] . "</td>";
    echo "<td>" . ($row['isActive'] == '1' ? 'Active' : 'Inactive') . "</td>";
    echo "<td>" . $row['dateCreated'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Add links for navigation
echo "<div style='margin-top: 20px;'>";
echo "<a href='check_school_years.php' style='padding: 10px; background: #4e73df; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>View School Years</a>";
echo "<a href='Admin/manageAttendance.php' style='padding: 10px; background: #1cc88a; color: white; text-decoration: none; border-radius: 5px;'>Go to Attendance Management</a>";
echo "</div>";
?>