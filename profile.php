<?php
/*
References: https://www.youtube.com/watch?v=JNtZl9SMmLQ
			https://www.youtube.com/watch?v=y4GxrIa7MiE
			https://www.youtube.com/watch?v=jvnRFsFwiT8
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
				<li><a href="home.php">Home</a></li>
			</ul>

			<ul>
				<li class="active"><a href="profile.php">Profile</a></li>
				<li><a href="search_users.php">Search Users</a></li>
			</ul>

			<ul id="submenu">
				<li>
					<span>Groups</span>
					<ul>
						<?php
							for ($x = 1; $x < $countIDs; $x++) {
								echo "<li><a href='./home.php?id=" . $groupIDs[$x] ."'>" . $groupNames[$x] . "</a></li>";
							}
							if ($countIDs == 1) {
								echo "<li><a href='./home.php'>User is only in the global group</a></li>";
							}
						?>
					</ul>
				</li>
			</ul>

			 <ul>
			 	<li><a href="invite_groups.php">Groups Invites</a></li>
                <li><a href="create_groups.php">Create Groups</a></li>
				<li><a href="search_groups.php">Search Groups</a></li>
            </ul>
		</div>
		<div class="profile_pos">
		<div class="profile">
			<center>
                <?php
					if(isset($_POST['upload'])){

						$file = $_FILES['file'];

						$fileName = $_FILES['file']['name'];
						$fileTmpName = $_FILES['file']['tmp_name'];
						$fileSize = $_FILES['file']['size'];
						$fileError = $_FILES['file']['error'];
						$fileType= $_FILES['file']['type'];

						$fileExt = explode('.', $fileName);
						$fileActualExt = strtolower(end($fileExt));

						$allowed = array('jpg', 'jpeg', 'gif', 'png');

						if(in_array($fileActualExt, $allowed)) {
							if ($fileError === 0) {
								if ($fileSize < 1000000) {
									$fileDestination = 'uploads/'.$fileName;
									move_uploaded_file($fileTmpName, $fileDestination);
									$result = mysqli_query($conn,"UPDATE users SET img = '".$fileName."' WHERE username = '".$_SESSION['username']."'");
									header("Location: profile.php?upload_success");
								} else {
									echo "Your file is too big!";
									echo "<br></br>";
								}
								
							} else {
								echo "There was an errpr uploading your file!";
								echo "<br></br>";
							}
						} else {
							echo "You cannot upload this type of file!";
							echo "<br></br>";
						}
					}
	
					$result_img = mysqli_query($conn,"SELECT * FROM users WHERE username ='" . $_SESSION['username'] . "'");
					while($row_img = mysqli_fetch_assoc($result_img)){
							
							if($row_img['img'] == ''){
									echo "<img id='avatar' width='300' height='300' src='uploads/profiledefault.png' alt='Default Profile Pic'>";
							} else {
									echo "<img id='avatar' width='300' height='300' src='uploads/".$row_img['img']."' alt='Profile Pic'>";
							}
							echo "<br>";
					}
                ?>
				<a href="#modal" class="modal-trigger">Change Picture</a>

				<div class="modal" id="modal">
					<div class="modal__dialog">
						<section class="modal__content">
							<header class="modal__header">
								Change Profile Picture
								<a href="#" class="modal__close">Close</a>
							</header>

							<div class="modal__body">
								<form action="" method="post" enctype="multipart/form-data">
										<input id="upload" type="file" name="file">
										<input id="up_submit" type="submit" name="upload" value="Upload">
								</form>
							</div>
						</section>
					</div>
				</div>

				<b>
				<?php 
					echo "<div id='pro_username'>";
						echo $_SESSION['fname'];
						echo " " . $_SESSION['lname'];
					echo "</div>";
				?>
				</b>
				</center>
			</div>
			
			<div class="pro_group">
				<div class="profile_underline"></div>
				
				<b><u><span align="center"> My Groups </span></u></b>
				<ul>
					<?php
						//Finds the public groups that a user is in
						$queryGroups = "SELECT groups.group_id,groups.group_name FROM users, groups, group_users WHERE users.id = group_users.user_id AND groups.group_id = group_users.group_id AND users.id = " . "'$userID' AND type = 'public';";
						$userGroups = $conn->query($queryGroups);

						if ($userGroups->num_rows > 0) { 
							// output data of each row
							while($row = $userGroups->fetch_assoc()) {
								$count++;
								if($row['group_id'] != 1) {
									echo "<span>"."<li>". $row['group_name'] . "</li>"."</span>";
								}
							} 
							if ($count == 1) {
								echo "User is only in the global group";
							}
						}
					?>
				</ul>
			</div>

			<div class="pro_info">
				<div class="profile_underline"></div>
				<b><u><span align="center"> My Info </span></u></b>

				<?php 
						echo "<span>". "Name: ". $_SESSION['fname'] . " " . $_SESSION['lname']."</span>";
						echo "<span>". "Username: ". $_SESSION['username'] . "</span>";
						echo "<span>". "Email Address: ". $_SESSION['email'] . "</span>";
				?>
			</div>
		</div>
	</body>
</html>

