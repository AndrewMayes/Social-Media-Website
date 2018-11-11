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
    
    //returns groups which a user is in.
	$getMyGroupIDs = "select b.group_id from users a, groups b, group_users c where a.id = c.user_id and b.group_id = c.group_id and a.id = ".$userID."";
	$resultMyGroupIDs = $conn->query($getMyGroupIDs);

	if ($resultMyGroupIDs->num_rows > 0) {
		while ($row = $resultMyGroupIDs->fetch_assoc()) {
			$myGroupIDsArray[] = $row['group_id'];
		}
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
                <li><a href="profile.php">Profile</a></li>
				<li><a href="search_users.php">Search Users</a></li>
            </ul>

			<ul id="submenu">
				<li>
					<span>Groups</span>
					<ul>
						<?php
							//Finds the groups that a user is in
							$queryGroups = "SELECT groups.group_id,groups.group_name,groups.type FROM users, groups, group_users WHERE users.id = group_users.user_id AND groups.group_id = group_users.group_id AND users.id = " .$userID."";
							$userGroups = $conn->query($queryGroups);

							if ($userGroups->num_rows > 0) { 
								// output data of each row
								while($row = $userGroups->fetch_assoc()) {
									$count++;
									if($row['group_id'] != 1) {
										echo "<li><a href='./home.php?id=" . $row['group_id'] ."&type=".$row['type']."'>" . $row['group_name'] . "</a></li>";
									}
								} 
								if ($count == 1) {
									echo "<li><a href='./home.php'>User is only in the global group</a></li>";
								}
							}
                        ?>
					</ul>
				</li>
            </ul>
            
            <ul>
                <li><a href="invite_groups.php">Groups Invites</a></li>
				<li><a href="create_groups.php">Create Groups</a></li>
				<li class="active"><a href="search_groups.php">Search Groups</a></li>
            </ul>
		</div>

		<div class="group_search">
        <?php
            echo "<form method='POST'>
			<input id='messeging' type='text' name='search_group' value='' placeholder='Enter Group Name'>
			<input id='msg_submit' type='submit' name='search_submit' value='Search!'>
			</form>";

			if (isset($_POST['search_submit'])) {


				$searchgroupname = mysqli_real_escape_string($conn, $_POST['search_group']);

				$getGroupID = "SELECT group_id FROM groups WHERE group_name = '" .$searchgroupname."'";
				$resultID = $conn->query($getGroupID);
				if ($resultID->num_rows > 0) { 
					// output data of each row
					while($row_id = $resultID->fetch_assoc()) {
						$searchgroupid = $row_id['group_id'];
					} 
				}

				$_SESSION['searchgroupid'] = $searchgroupid;

				$query = "SELECT * FROM groups WHERE group_id = $searchgroupid";
				$queryResults = $conn->query($query);

				if ($queryResults->num_rows > 0) {
					while ($row = $queryResults->fetch_assoc()) {
					   $gType = $row['type']; 
					}
				}
				
				$query2 = "SELECT * from group_users a WHERE a.user_id = $userID and group_id = $searchgroupid";
				$query2Results = $conn->query($query2);

				if ($query2Results->num_rows > 0) {
					$isIn = 1;
				} else {
					$isIn = 0;
				}

				if ($isIn == 1) {
					echo "You are already a member of this group";
					echo "<br>";
					echo "Here is the link: " . "<a href='./home.php?id=" . $searchgroupid ."'>$searchgroupname</a>";
				} else {
					if ($gType == 'public') {
						$formhelper = 1;
						$_SESSION['formhelper'] = $formhelper;
						echo "You can join this group!";
					} else if ($gType == 'private') {
						echo "This is a private group. Invitation by owner required.";
					} else {
						echo "Group not found";
					}
				}

			}


			$query4 = "SELECT * FROM groups WHERE group_id = ".$_SESSION['searchgroupid']."";
			$queryResultsOutside = $conn->query($query4);

			if ($queryResultsOutside->num_rows > 0) {
				while ($row = $queryResultsOutside->fetch_assoc()) {
				   $grType = $row['type']; 
				}
			}


			echo "<form method='POST'>
			<input id='msg_submit' type='submit' name='join_submit' value='Join!'>
			</form>";

			if (isset($_POST['join_submit'])) {
				if (empty($_SESSION['searchgroupid'])) {
					echo "Please enter group name";
				} else if ($grType == 'private') {
				} else {
					$query3 = "INSERT INTO `group_users` (`user_id`, `group_id`) VALUES ($userID, " . $_SESSION['searchgroupid'] . ")";
					$conn->query($query3);
					header("Location: search_groups.php"); 
				}
			}
		?>
        </div>
	</body>
</html>

