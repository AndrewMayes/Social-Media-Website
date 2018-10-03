<?php
	$servername = "localhost";
	$username = "admin";
	$password = "monarchs";
	$dbname = "cs418";

	// Create connection
	$conn = new mysqli($servername,$username,$password,$dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
?>