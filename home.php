<?php
/*
	References: https://www.youtube.com/watch?v=pfFdbpPgg4M&list=PLBOh8f9FoHHhRk0Fyus5MMeBsQ_qwlAzG&index=14
				https://www.youtube.com/watch?v=tVLHGHshNdU&index=15&list=PLBOh8f9FoHHhRk0Fyus5MMeBsQ_qwlAzG
	I referenced these 2 videos when writing the 'likes' code 
*/
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

	if (isset($_GET['liked'])) {
		$hasUserLikedQuery = "SELECT `user_id` FROM `messages_likes` WHERE `msg_id` = " . $_GET['liked'] . " AND `user_id` = " . $userID . "";
		$userLiked = $conn->query($hasUserLikedQuery);

		if (!$userLiked->num_rows > 0) { 
			// output data of each row
			$likedQuery = "UPDATE `messages` SET `likes` = `likes`+1 WHERE `messages`.`msg_id` = " . $_GET['liked'] . "";
			$postLikesQuery = "INSERT INTO `messages_likes` (`msg_id`, `user_id`) VALUES ('" . $_GET['liked'] . "', '" . $userID . "')";
			$conn->query($likedQuery);
			$conn->query($postLikesQuery);
			header("Location: home.php?id=" . $groupID . "");
		} else {
			$unlikedQuery = "UPDATE `messages` SET `likes` = `likes`-1 WHERE `messages`.`msg_id` = " . $_GET['liked'] . "";
			$postunLikesQuery = "DELETE FROM `messages_likes` WHERE `messages_likes`.`msg_id` = " . $_GET['liked'] . " AND `messages_likes`.`user_id` = " . $userID . "";
			$conn->query($unlikedQuery);
			$conn->query($postunLikesQuery);
			header("Location: home.php?id=" . $groupID . "");
		}
	}

	if (isset($_POST['submit'])) {
		$message = mysqli_real_escape_string($conn, $_POST['message']);

		$query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`, `likes`) VALUES (NULL, '" . $userID . "', '" . $message . "', CURRENT_TIMESTAMP, '" . $groupID . "',0);";

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
					echo $_SESSION['username'];
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
				<li><a href="profile.php">Profile</a></li>
			</ul>

			<ul id="submenu">
				<li>
					<span>Groups</span>
					<ul>
						<?php
							//Finds the groups that a user is in
							$queryGroups = "SELECT groups.group_id,groups.group_name FROM users, groups, group_users WHERE users.id = group_users.user_id AND groups.group_id = group_users.group_id AND users.id = " . "'$userID';";
							$userGroups = $conn->query($queryGroups);

							if ($userGroups->num_rows > 0) { 
								// output data of each row
								while($row = $userGroups->fetch_assoc()) {
									if($row['group_id'] != 1) {
										echo "<li><a href='./home.php?id=" . $row['group_id'] ."'>" . $row['group_name'] . "</a></li>";
									} else {
										echo "<li><a href='./home.php'>User is only in the global group</a></li>";
									}
								} 
							}
						?>
					</ul>
				</li>
			</ul>
		</div>
		<div class="position">
			<div class = "feed">
				<?php
					$postFeed = "SELECT username, msg, post_time, msg_id, likes from users inner join messages on users.id = messages.user_id WHERE group_id = " . $groupID . " ORDER BY msg_id DESC";
					$result = $conn->query($postFeed);

					if ($result->num_rows > 0) { 
						// output data of each row
						while($row = $result->fetch_assoc()) {
							echo "<h2 id ='userName'>" . $row['username'] . ": " . htmlspecialchars($row['msg']) . "</h2>";
							//echo "<a id ='userName'>" . $row['fname'] . " " . $row['lname'] . ":<span id='msg'> " . htmlspecialchars($row['msg']) . "</span></a>";
							echo "<div class='time'>" . $row['post_time'] . "</div>"."\n";
							echo "<form action='home.php?id=" . $groupID . "&liked=" . $row['msg_id'] . "' method='POST'>
							<input id='msg_submit' type='submit' name='like' value='Like'>
							<span>".$row['likes']." likes</span>
							</form>";
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
	</body>
</html>

