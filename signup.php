<?php
	session_start();
	if(isset($_SESSION['email'])){
		header("Location: home.php?msg=" . urlencode('already_has_account'));
	} 
?>

<!doctype HTML>
<html>
	<head>
		<title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
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
					<h2 align="center" style="font-family: 'Exo 2', sans-serif;"> Join Now! </h2>
					<form action="signup.php" method="POST">
						<input id="loggin_text" type="text" name="signupfirstname" size="25" placeholder="First Name" /> <br /><br />
						<input id="loggin_text" type="text" name="signuplastname" size="25" placeholder="Last Name" /> <br /><br />
						<input id="loggin_text" type="text" name="signupusername" size="25" placeholder="Username" /> <br /><br />
						<input id="loggin_text" type="text" name="signupemail" size="25" placeholder="Email Address" /> <br /><br />
						<input id="loggin_text" type="text" name="signuppassword" size="25" placeholder="Password" /> <br /><br />
						<input id="loggin_submit" type="submit" name="submit" value="Sign Up"/>
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
	$signupfirstname = mysqli_real_escape_string($conn, $_POST['signupfirstname']);
	$signuplastname = mysqli_real_escape_string($conn, $_POST['signuplastname']);
	$signupusername = mysqli_real_escape_string($conn, $_POST['signupusername']);
	$signupemail = mysqli_real_escape_string($conn, $_POST['signupemail']);
	$signuppassword = mysqli_real_escape_string($conn, $_POST['signuppassword']);
	
	$querySearchUsername = "SELECT username FROM users WHERE username = '".$signupusername."'";
	$resultSearch1 = $conn->query($querySearchUsername);
	$querySearchEmail = "SELECT email FROM users WHERE email = '".$signupemail."'";
	$resultSearch2 = $conn->query($querySearchEmail);
	
	if ($resultSearch1->num_rows > 0) {
		while($row = $resultSearch1->fetch_assoc()) {
			$searchedusername = $row['username'];
		}
	}

	if ($resultSearch2->num_rows > 0) {
		while($row = $resultSearch2->fetch_assoc()) {
			$searchedemail = $row['email'];
		}
	}

	if(empty($signuppassword) || empty($signupemail) || empty($signupusername) || empty($signupfirstname) || empty($signuplastname)) {
		echo "<p class= " . "notfound" . ">You left out part of the form. Please enter all information</p>";
	} else if(!$signupusername == $searchedusername) {
		if(!$signupemail == $searchedemail) {
			$query1 = "INSERT INTO `users` (`id`, `fname`, `lname`, `password`, `email`, `username`, `img`) VALUES (NULL, '".$signupfirstname."', '".$signuplastname."', '".$signuppassword."', '".$signupemail."', '".$signupusername."','')";
			$conn->query($query1);
			$queryID = "SELECT id FROM users WHERE email ='".$signupemail."'";
			$resultID = $conn->query($queryID);
			if ($resultID->num_rows > 0) {
				while($row = $resultID->fetch_assoc()) {
					$ID = $row['id'];
					$query2 = "INSERT INTO `group_users` (`user_id`, `group_id`) VALUES ('".$ID."', '1')";
					$conn->query($query2);
					$_SESSION['fname'] = $signupfirstname;
					$_SESSION['lname'] = $signuplastname;
					$_SESSION['email'] = $signupemail;
					$_SESSION['username'] = $signupusername;
					header("Location: home.php");
				}
			} 
		} else {
			echo "<p class= " . "notfound" . ">Email already exists</p>";
		}
	} else {
		echo "<p class= " . "notfound" . ">Username already exists</p>";
	}



	
}

$conn->close();

?>