<?php
	$host = "localhost";
	$user = "root";
	$pass = "password";
	$db = "attendancemsystem01";
	
	$conn = new mysqli($host, $user, $pass, $db);
	if($conn->connect_error){
		echo "Seems like you have not configured the database. Failed To Connect to database:" . $conn->connect_error;
	}
?>