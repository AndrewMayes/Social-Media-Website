<?php
	include ('connection.php');

	session_start();
	
	if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
	}
?>

<!doctype HTML>
<html>
	<head>
		<title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet">
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
		<script src="script/dropdown.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="header">
			<?php 
				echo "<div id='logo'>";
					echo $_SESSION['fname'];
					echo " " . $_SESSION['lname'];
				echo "</div>";
			?>

			<div class="menu">
				<ul>
					<li><a href="loggedout.php">Log Out</a></li>
				</ul>
			</div>
		</div>

		<div class="sidemenu">
			<ul>
				<li class="active"><a href="loggedin.php">Home</a></li>
			</ul>

			<ul>
				<li><a href="#">Account</a></li>
			</ul>

			<ul id="submenu">
				<li>
					<span>Groups</span>
					<ul>
						<li><a href="global_group.php">Global</a></li>
						<li><a href="#">Games</a></li>            
						<li><a href="#">Sports</a></li>
					</ul>
				</li>
			</ul>
		</div>

		<div class="posting">
			<form action="loggedin.php" method="POST">
				<input id="messeging" type="text" name="message" value="" placeholder="Post Your Status...">
				<input id="msg_submit" type="submit" name="submit" value="Post!">
			</form> 
		</div>
		
		<div class="footer">
			<p>":Social Media:": A CS418 Project</p><br/>
			<p>Created by: Andrew Mayes and James Lopez</p>
		</div>
	</body>
</html>

