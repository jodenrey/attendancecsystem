<?php
include "./Includes/dbcon.php";

// 1. Check the current state of the table
echo "<h2>Initial State of tblsessionterm</h2>";
$query = mysqli_query($conn, "SELECT * FROM tblsessionterm ORDER BY Id");
echo "<table border=1>";
echo "<tr><th>ID</th><th>School Year</th><th>Is Active</th><th>Date Created</th></tr>";
while($row = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    echo "<td>{$row["Id"]}</td>";
    echo "<td>{$row["sessionName"]}</td>";
    echo "<td>{$row["isActive"]}</td>";
    echo "<td>{$row["dateCreated"]}</td>";
    echo "</tr>";
}
echo "</table><hr>";

// 2. Fix any zero or null IDs
$query = mysqli_query($conn, "SELECT * FROM tblsessionterm WHERE Id = 0 OR Id IS NULL");
if(mysqli_num_rows($query) > 0) {
    echo "<h3>Found records with ID=0 or NULL. Fixing...</h3>";
    
    // Get the highest ID
    $getMaxId = mysqli_query($conn, "SELECT MAX(Id) as maxId FROM tblsessionterm WHERE Id > 0");
    $maxIdRow = mysqli_fetch_assoc($getMaxId);
    $nextId = $maxIdRow["maxId"] + 1;
    
    // Update the problematic records
    $updateQuery = mysqli_query($conn, "UPDATE tblsessionterm SET Id = $nextId WHERE Id = 0 OR Id IS NULL");
    if($updateQuery) {
        echo "Updated records with new ID: $nextId<br>";
        $nextId++;
    } else {
        echo "Error updating records: " . mysqli_error($conn) . "<br>";
    }
}

// 3. Reset auto_increment to avoid future issues
$getMaxId = mysqli_query($conn, "SELECT MAX(Id) as maxId FROM tblsessionterm");
$maxIdRow = mysqli_fetch_assoc($getMaxId);
$nextId = $maxIdRow["maxId"] + 1;

$alterQuery = mysqli_query($conn, "ALTER TABLE tblsessionterm AUTO_INCREMENT = $nextId");
if($alterQuery) {
    echo "<h3>Auto increment reset to $nextId</h3>";
} else {
    echo "<h3>Error resetting auto increment: " . mysqli_error($conn) . "</h3>";
}

// 4. Check final state
echo "<h2>Final State of tblsessionterm</h2>";
$query = mysqli_query($conn, "SELECT * FROM tblsessionterm ORDER BY Id");
echo "<table border=1>";
echo "<tr><th>ID</th><th>School Year</th><th>Is Active</th><th>Date Created</th></tr>";
while($row = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    echo "<td>{$row["Id"]}</td>";
    echo "<td>{$row["sessionName"]}</td>";
    echo "<td>{$row["isActive"]}</td>";
    echo "<td>{$row["dateCreated"]}</td>";
    echo "</tr>";
}
echo "</table>";

// Add a button to return to the admin page
echo "<p><a href=\"Admin/manageAttendance.php\" style=\"padding: 10px; background: #4e73df; color: white; text-decoration: none; border-radius: 5px;\">Go to Manage Attendance</a></p>";
?>
