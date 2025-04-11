<?php
include 'Includes/dbcon.php';

echo "<h2>Database Table Structure</h2>";

// Check tblclassarms structure
$query = "DESCRIBE tblclassarms";
$result = mysqli_query($conn, $query);

echo "<h3>tblclassarms Table Structure:</h3>";
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";

// Check tblclassteacher structure
$query = "DESCRIBE tblclassteacher";
$result = mysqli_query($conn, $query);

echo "<h3>tblclassteacher Table Structure:</h3>";
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Field']}</td>";
    echo "<td>{$row['Type']}</td>";
    echo "<td>{$row['Null']}</td>";
    echo "<td>{$row['Key']}</td>";
    echo "<td>{$row['Default']}</td>";
    echo "<td>{$row['Extra']}</td>";
    echo "</tr>";
}
echo "</table>";

// Check current assignments 
$query = "SELECT ca.Id, ca.classId, ca.classArmName, ca.isAssigned, ca.teacherId, 
          ct.Id as teacherId, ct.firstName, ct.lastName, ct.classId as ctClassId, ct.classArmId
          FROM tblclassarms ca
          LEFT JOIN tblclassteacher ct ON ca.teacherId = ct.Id
          LIMIT 5";
$result = mysqli_query($conn, $query);

echo "<h3>Sample Assignments:</h3>";
echo "<table border='1'><tr><th>Class Arm ID</th><th>Class ID</th><th>Class Arm Name</th><th>Is Assigned</th><th>Teacher ID (tblclassarms)</th><th>Teacher ID (tblclassteacher)</th><th>Teacher Name</th><th>Teacher's Class ID</th><th>Teacher's ClassArm ID</th></tr>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['Id']}</td>";
    echo "<td>{$row['classId']}</td>";
    echo "<td>{$row['classArmName']}</td>";
    echo "<td>{$row['isAssigned']}</td>";
    echo "<td>{$row['teacherId']}</td>";
    echo "<td>{$row['teacherId']}</td>";
    echo "<td>{$row['firstName']} {$row['lastName']}</td>";
    echo "<td>{$row['ctClassId']}</td>";
    echo "<td>{$row['classArmId']}</td>";
    echo "</tr>";
}
echo "</table>";
?>