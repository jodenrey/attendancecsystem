<?php
// Connect to database
$host = "localhost";
$user = "root";
$pass = "password";
$db = "attendancemsystem01";

$conn = new mysqli($host, $user, $pass, $db);
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully\n";

// Create a backup of the tblsessionterm table
$backupQuery = "CREATE TABLE IF NOT EXISTS tblsessionterm_backup AS SELECT * FROM tblsessionterm";
if ($conn->query($backupQuery) === TRUE) {
    echo "Backup table created successfully\n";
} else {
    echo "Error creating backup table: " . $conn->error . "\n";
}

// Check if termId column exists
$checkColumn = $conn->query("SHOW COLUMNS FROM tblsessionterm LIKE \"termId\"");
if($checkColumn->num_rows > 0) {
    // Remove the termId column
    $alterQuery = "ALTER TABLE tblsessionterm DROP COLUMN termId";
    if ($conn->query($alterQuery) === TRUE) {
        echo "Column termId removed successfully\n";
    } else {
        echo "Error removing column: " . $conn->error . "\n";
    }
} else {
    echo "Column termId does not exist in tblsessionterm table\n";
}

echo "Database update complete!\n";
$conn->close();
?>
