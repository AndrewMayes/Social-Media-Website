<?php
/*
References: 
            https://github.com/jbrunelle/ODUCS418F18/blob/master/examples/userinputs/greetMe.php
            https://github.com/jbrunelle/ODUCS418F18/blob/master/examples/userinputs/forms.html
			https://www.w3schools.com/html/html_forms.asp
			https://www.w3schools.com/tags/att_input_placeholder.asp
			https://stackoverflow.com/questions/35108708/how-to-prevent-browser-from-going-back-to-login-form-page-once-user-is-logged-in
*/

	session_start();
	
	if(isset($_SESSION['email'])){
		header("Location: home.php?msg=" . urlencode('already_logged_in'));
	}  
?>

<!doctype HTML>
<html>
	<head>
		<title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css?">
		<link href="https://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet">
	</head>
	<body>
		<div class="header">
				<div id="logo">:Social Media:</div>
				<div class="menu">
					<ul>
						<li><a href="signup.php">Sign Up</a></li>
						<li><a href="index.php">Log In</a></li>
					</ul>
				</div>
		</div>	
		<table>
			<tr>
				<td>
					<h2 style="text-align: center;"> Log In </h2>
					<form action="index.php" method="POST">
						<input id="loggin_text" type="text" name="email" value="" placeholder="Email"><br /><br />
						<input id="loggin_text" type="password" name="password" value="" placeholder="Password"><br /><br />
						<input id="loggin_submit" type="submit" name="submit" value="Log In!">    
					</form> 
				</td>
			</tr>
		</table>
		
		<div class="footer">
			<p>":Social Media:": A CS418 Project</p><br/>
			<p>Created by: Andrew Mayes and James Lopez</p>
		</div>
	</body>
</html>

<?php

	include 'connection.php';

	if (isset($_POST['submit'])) {
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);

		$query1 = "SELECT * FROM users WHERE email ='" . $email . "' AND password ='" . $password . "';";
		$result = $conn->query($query1);

		if ($result->num_rows > 0) { 
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$dbfname = $row['fname'];
				$dblname = $row['lname'];
				$dbemail = $row['email'];
				$dbpassword = $row['password'];
				$dbusername = $row['username'];
			}
			if ($dbemail == $email) {
				$_SESSION['fname'] = $dbfname;
				$_SESSION['lname'] = $dblname;
				$_SESSION['email'] = $dbemail;
				$_SESSION['username'] = $dbusername;
				header("Location: home.php");
			} 
		} else {
			echo "<p class= " . "notfound" . ">User not found</p>";
		}
	}

	$conn->close();

?>