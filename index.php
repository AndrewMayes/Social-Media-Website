<?php
/*
References: 
            https://github.com/jbrunelle/ODUCS418F18/blob/master/examples/userinputs/greetMe.php
            https://github.com/jbrunelle/ODUCS418F18/blob/master/examples/userinputs/forms.html
            https://www.w3schools.com/html/html_forms.asp
            https://www.w3schools.com/tags/att_input_placeholder.asp
*/
?>

<!doctype HTML>
<html>
	<head>
		<title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow" rel="stylesheet">
	</head>
	<body>
		<div class="header">
			<div id="wrapper">
				<div id="logo">
					<t> :Social Media:</t>
				</div>
				<div id="menu">
					<a href="testdesign.php" />Sign Up</a>
					<a href="index.php" />Log In</a>
				</div>
			</div>
		</div>	

		<table>
			<tr>
				<td>
					<h2 align="center"> Log In </h2>
					<form action="index.php" method="POST">
					<input type="text" name="email" value="" placeholder="Email"><br /><br />
					<input type="password" name="password" value="" placeholder="Password"><br /><br />
					<input type="submit" name="submit" value="Log In!">    
					</form> 
				</td>
			</tr>
		</table>
	</body>
</html>

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

	if (isset($_POST['submit'])) {
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);

		$query1 = "SELECT * FROM users WHERE email ='" . $email . "' AND password ='" . $password . "';";
		$result = $conn->query($query1);

		if ($result->num_rows > 0) { 
			echo $email . "<br>";
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$dbemail = $row['email'];
				$dbpassword = $row['password'];
			}
			if ($dbemail == $email) {
				header("Location: loggedin.php");
			} 
		} else {
			echo "<p class= " . "notfound" . ">User not found</p>";
		}
	}

	$conn->close();

?>