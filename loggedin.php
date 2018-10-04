<?php
	include ('connection.php');

	session_start();
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
			<div id="wrapper">
				<div id="logo">
					<t> :Social Media:</t>
				</div>
				<div id="menu">
					<a href="loggedout.php" />Log Out</a>
				</div>
			</div>
		</div>	
		
		<ul>
			<li><a href="#">Account</a></li>
			<li><a href="#">Group</a>
				<ul class='dropdown1'>
					<li> <a href="#">Global</a></li>
					<li> <a href="#">League of Legends</a></li>
					<li> <a href="#">Dota 2</a></li>
					<li> <a href="#">CS:GO</a></li>
				</ul>
			</li>
		</ul>
		
		<form action="loggedin.php" method="POST">
			<div class="posting">
				<input id="messeging" type="text" name="message" value="" placeholder="Post Your Status...">
				<input id="msg_submit" type="submit" name="submit" value="Post!">
			</div>
		</form> 
		
		<div class="footer">
				<p>":Social Media:": A CS418 Project</p><br/>
				<p>Created by: Andrew Mayes and James Lopez</p>
		</div>
	</body>
</html>

