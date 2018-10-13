<?php
	include ('connection.php');

	session_start();
	
	if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
	}

	//getUserID();
	//turn these into functions soon.
	//retrieve UserID from database
	$userEmail = $_SESSION['email'];
	$queryID = "SELECT id FROM users WHERE email = " . "'$userEmail';";
	$userEmail = $conn->query($queryID);

	if ($userEmail->num_rows > 0) { 
		// output data of each row
		while($row = $userEmail->fetch_assoc()) {
			$userID = $row['id'];  
		} 
	}

	//groups need to be worked on
	if (isset($_POST['submit'])) {
		$message = mysqli_real_escape_string($conn, $_POST['message']);

		//$userid = getUserID();
		//$userid = 6;
		

		$query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`) VALUES (NULL, '" . $userID . "', '" . $message . "', CURRENT_TIMESTAMP, '1');";

		$conn->query($query);

		header("Location: loggedin.php"); //temporary so that a user's message does not get posted twice when they refresh the page

		$conn->close();
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

