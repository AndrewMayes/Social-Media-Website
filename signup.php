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
					<form action="#" method="POST">
						<input id="loggin_text" type="text" name="fname" size="25" placeholder="First Name" /> <br /><br />
						<input id="loggin_text" type="text" name="lname" size="25" placeholder="Last Name" /> <br /><br />
						<input id="loggin_text" type="text" name="username" size="25" placeholder="Username" /> <br /><br />
						<input id="loggin_text" type="text" name="email" size="25" placeholder="Email Address" /> <br /><br />
						<input id="loggin_text" type="text" name="password" size="25" placeholder="Password" /> <br /><br />
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