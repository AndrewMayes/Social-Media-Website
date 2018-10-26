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
									echo "<img width='300' height='300' src='uploads/profiledefault.png' alt='Default Profile Pic'>";
							} else {
									echo "<img width='300' height='300' src='uploads/".$row_img['img']."' alt='Profile Pic'>";
							}
							echo "<br>";
					}
                ?>
				
				<form action="" method="post" enctype="multipart/form-data">
                        <input id="upload" type="file" name="file">
                        <input id="up_submit" type="submit" name="upload" value="Upload">
                </form>
               
				<?php 
					echo "<div id='pro_username'>";
						echo $_SESSION['fname'];
						echo " " . $_SESSION['lname'];
						echo "</div>";
				?>
				</center>
			</div>
			
			<div class="pro_group">
				<ul>
					<?php
						//Finds the groups that a user is in
						$queryGroups = "SELECT groups.group_id,groups.group_name FROM users, groups, group_users WHERE users.id = group_users.user_id AND groups.group_id = group_users.group_id AND users.id = " . "'$userID';";
						$userGroups = $conn->query($queryGroups);

						if ($userGroups->num_rows > 0) { 
							// output data of each row
							while($row = $userGroups->fetch_assoc()) {
								$count++;
								if($row['group_id'] != 1) {
									echo "<li>". $row['group_name'] . "</li>";
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
			
			</div>
		</div>
	</body>
</html>

