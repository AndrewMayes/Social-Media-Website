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

	if(isset($_GET['id'])) {
		$uID = $_GET['id'];	
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

	if(!isset($_GET['id'])) {
		header("Location: profile.php?id=". $userID . "");
	}

	$queryUserProfile = "SELECT * FROM users WHERE id ='" . $_GET['id']. "'";
	$result_profile = $conn->query($queryUserProfile);
	if($result_profile->num_rows > 0){	
		while($row_profile = $result_profile->fetch_assoc()) {
			$profileFname = $row_profile['fname'];
			$profileLname = $row_profile['lname'];
			$profileEmail = $row_profile['email'];
			$profilePassword = $row_profile['password'];
			$profileUsername = $row_profile['username'];
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
				<?php
                    if ($_SESSION['adminID'] == $userID) {
                        echo "<li><a href='groupadmin.php'>Group Administration</a></li>";
                    }
				?>
                <?php
                    if ($_SESSION['adminID'] == $userID) {
                        echo "<li><a href='adminhelp.php'>Help</a></li>";
                    }
                    else{
                        echo "<li><a href='help.php'>Help</a></li>";
                    }
                ?>				
            </ul>
		</div>
		
		<div class="profile_pos">
			<div class="profile">
			<center>
				<?php
				if(isset($_SESSION['email'])) {

				?>

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
									$fileNewName = "profile".$profileUsername.".". $fileActualExt;
									$fileDestination = 'uploads/'.$fileNewName;
									move_uploaded_file($fileTmpName, $fileDestination);
									$result = mysqli_query($conn,"UPDATE users SET img = '".$fileNewName."' WHERE username = '".$profileUsername."'");
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
	
					$result_img = mysqli_query($conn,"SELECT * FROM users WHERE username ='" . $profileUsername . "'");
					while($row_img = mysqli_fetch_assoc($result_img)){
							
							if($row_img['img'] == ''){
								$gravatar = "https://www.gravatar.com/avatar/".md5($row_img['email'])."?d=retro";
								
								if(isset($gravatar)){
									echo "<img id='avatar' width='300' height='300' src= $gravatar alt='Default Profile Pic'>";
								} else {
									echo "<img id='avatar' width='300' height='300' src='uploads/profiledefault.png' alt='Default Profile Pic'>";
								}
							} else {
									echo "<img id='avatar' width='300' height='300' src='uploads/".$row_img['img']."' alt='Profile Pic'>";
							}
							echo "<br>";
					
                ?>

				<?php if($userID == $uID){ ?>
					<a href="#modal" class="modal-trigger">Change Picture</a>
					<form action="deleteimg.php" method="post">
						<button class="delete_img" type="submit" name="delete">Default Picture</button>
					</form>
				<?php }?>
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
						echo $profileFname;
						echo " " . $profileLname;
					echo "</div>";
					echo '<button class="dm" type=\'button\' onclick=\'location.href = "dm.php?id='.$uID.'"\'>Direct Message!</button>';
				?>
				</b>
				</center>
			</div>
			
			<div class="pro_group">
				<div class="profile_underline"></div>
				
				<b><u><span align="center"> My Groups </span></u></b>
				<ul>
					<?php
						//Finds the groups that a user is in
						$queryGroups = "SELECT groups.group_id,groups.group_name FROM users, groups, group_users WHERE users.id = group_users.user_id AND groups.group_id = group_users.group_id AND users.id = '" . $_GET['id']. "' AND type='public';";
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
								echo "<span>"."User is only in the global group"."</span>";
							}
						}
					}
					?>
				</ul>
			</div>

			<div class="pro_info">
				<div class="profile_underline"></div>
				<b><u><span align="center"> My Info </span></u></b>

				<?php 
						echo "<span>". "Name: ". $profileFname . " " . $profileLname."</span>";
						echo "<span>". "Username: ". $profileUsername . "</span>";
						echo "<span>". "Email Address: ". $profileEmail . "</span>";
						
						if ($_SESSION['adminID'] == $userID) {
							echo "<center><b><u><span><a class='achievement_link'href='adminhelp.php'>Achievements</a></span></u></b></center>";
						}
						else{
							echo "<center><b><u><span><a class='achievement_link'href='help.php'>Achievements</a></span></u></b></center>";
						}

						$queryMessageCount = "SELECT users.id, COUNT(messages.msg_id) AS msg_count FROM messages INNER JOIN users ON '" . $_GET['id']. "' = messages.user_id GROUP BY users.id";
						$result_msg_count = $conn->query($queryMessageCount);
						
						if ($result_msg_count->num_rows > 0) { 
							// output data of each row
							$row_msg_count = $result_msg_count->fetch_assoc();
							$msgCount = $row_msg_count['msg_count'];
							
							if($msgCount > 3) {
								if ($_SESSION['adminID'] == $userID) {
									echo "<span><a class='achievement_link'href='adminhelp.php'>:Active Poster:</a></span>";
								}
								else{
									echo "<span><a class='achievement_link'href='help.php'>:Active Poster:</a></span>";
								}
							}
						}

						$queryMostLiked = "SELECT DISTINCT messages.msg_id, messages.user_id, MAX(messages.likes) AS most_liked FROM messages INNER JOIN users ON users.id = messages.user_id GROUP BY messages.msg_id ORDER BY most_liked DESC LIMIT 1";
						$result_most_liked = $conn->query($queryMostLiked);
						
						if ($result_most_liked->num_rows > 0) { 
							$row_most_liked = $result_most_liked->fetch_assoc();

							$mostLikedUser = $row_most_liked['user_id'];
							$getid = mysqli_real_escape_string($conn, $_GET['id']);
							
							if($mostLikedUser == $getid) {
								if ($_SESSION['adminID'] == $userID) {
									echo "<span><a class='achievement_link'href='adminhelp.php'>:Most Liked Post:</a></span>";
								}
								else{
									echo "<span><a class='achievement_link'href='help.php'>:Most Liked Post:</a></span>";
								}
							}
						}

						$queryMostDisliked = "SELECT DISTINCT messages.msg_id, messages.user_id, MAX(messages.dislikes) AS most_disliked FROM messages INNER JOIN users ON users.id = messages.user_id GROUP BY messages.msg_id ORDER BY most_disliked DESC LIMIT 1";
						$result_most_disliked = $conn->query($queryMostDisliked);
						
						if ($result_most_disliked->num_rows > 0) { 
							$row_most_disliked = $result_most_disliked->fetch_assoc();

							$mostDislikedUser = $row_most_disliked['user_id'];
							$getid = mysqli_real_escape_string($conn, $_GET['id']);
							
							if($mostDislikedUser == $getid) {
								if ($_SESSION['adminID'] == $userID) {
									echo "<span><a class='achievement_link'href='adminhelp.php'>:Most Disliked Post:</a></span>";
								}
								else{
									echo "<span><a class='achievement_link'href='help.php'>:Most Disliked Post:</a></span>";
								}
							}
						}

						$queryGroupCount = "SELECT group_users.user_id, COUNT(group_users.group_id ) AS group_count FROM group_users INNER JOIN users ON group_users.user_id = '" . $_GET['id']. "' GROUP BY users.id";
						$result_group_count = $conn->query($queryGroupCount);
						
						if ($result_group_count->num_rows > 0) { 
							// output data of each row
							$row_group_count = $result_group_count->fetch_assoc();
							$groupCount = $row_group_count['group_count'];
							if($groupCount > 3) {
								if ($_SESSION['adminID'] == $userID) {
									echo "<span><a class='achievement_link'href='adminhelp.php'>:Group Collector:</a></span>";
								}
								else{
									echo "<span><a class='achievement_link'href='help.php'>:Group Collector:</a></span>";
								}
							}
						}
					}
				?>
			</div>
		</div>
	</body>
</html>

