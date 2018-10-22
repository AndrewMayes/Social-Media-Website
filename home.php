<?php
	include ('connection.php');

	session_start();
	
	if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
	}

	if(isset($_GET['id'])) {
		$groupID = $_GET['id'];	
	} else {
		$groupID = "1";
	}

	$test = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`) VALUES (NULL, '1', 'testingthisstuff', CURRENT_TIMESTAMP, '" . $groupID . "');";
	

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

	//Finds the groups that a user is in
	$queryGroups = "SELECT groups.group_id,groups.group_name FROM users, groups, group_users WHERE users.id = group_users.user_id AND groups.group_id = group_users.group_id AND users.id = " . "'$userID';";
	$userGroups = $conn->query($queryGroups);

	if ($userGroups->num_rows > 0) { 
		// output data of each row
		while($row = $userGroups->fetch_assoc()) {
			$groupNames[] = $row['group_name'];
			$groupIDs[] = $row['group_id'];
		} 
	}

	$countNames = count($groupNames);
	$countIDs = count($groupIDs);


	if (isset($_POST['submit'])) {
		$message = mysqli_real_escape_string($conn, $_POST['message']);

		$query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`) VALUES (NULL, '" . $userID . "', '" . $message . "', CURRENT_TIMESTAMP, '" . $groupID . "');";

		$conn->query($query);

		header("Location: home.php?id=" . $groupID . ""); 

		$conn->close();
	}
?>

<!doctype HTML>
<html>
	<head>
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
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
				<li class="active"><a href="home.php">Home</a></li>
			</ul>

			<ul>
				<li><a href="#">Account</a></li>
			</ul>

			<ul id="submenu">
				<li>
					<span>Groups</span>
					<ul>
						<?php
							for ($x = 1; $x < $countNames; $x++) {
								echo "<li><a href='./home.php?name=" . $groupNames[$x] ."'>" . $groupNames[$x] . "</a></li>";
							}
							for ($x = 1; $x < $countIDs; $x++) {
								echo "<li><a href='./home.php?id=" . $groupIDs[$x] ."'>" . $groupIDs[$x] . "</a></li>";
							}
						?>
					</ul>
				</li>
			</ul>
		</div>
		<div class="position">
			<div class = "feed">
				<?php
					$postFeed = "SELECT fname,lname, msg, post_time, msg_id from users inner join messages on users.id = messages.user_id WHERE group_id = " . $groupID . " ORDER BY msg_id DESC";
					$result = $conn->query($postFeed);

					if ($result->num_rows > 0) { 
						// output data of each row
						while($row = $result->fetch_assoc()) {
							echo "<h2 id ='userName'>" . $row['fname'] . " " . $row['lname'] . ": " . htmlspecialchars($row['msg']) . "</h2>";
							//echo "<a id ='userName'>" . $row['fname'] . " " . $row['lname'] . ":<span id='msg'> " . htmlspecialchars($row['msg']) . "</span></a>";
							echo "<div class='time'>" . $row['post_time'] . "</div>"."\n";
						} 
					} else {
						echo "<h2 id ='userName'>No messages in this channel yet. Come back soon!</h2>";
					}
				?>
			</div>
		</div>
		<div class="posting">
		<?php
			echo "<form action='home.php?id=" . $groupID . "' method='POST'>
				<input id='messeging' type='text' name='message' value='' placeholder='Post Your Status...'>
				<input id='msg_submit' type='submit' name='submit' value='Post!'>
				</form>";
		?>
		</div>
		<?php
		echo "<p>$groupID</p>";
		echo "<p>$test</p>";
		?>
	</body>
</html>

