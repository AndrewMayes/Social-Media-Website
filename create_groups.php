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
				<li class="active"><a href="create_groups.php">Create Groups</a></li>
				<li><a href="search_groups.php">Search Groups</a></li>
            </ul>
		</div>
        <div class = "my_group">
            <u><span><h2>My Groups</h2></span></u>
            <?php
                $query = "SELECT group_id, group_name FROM groups";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        foreach ($myGroupIDsArray as $value) {
                            if ($row['group_id'] == $value) {
                                //echo "<a href='./home.php?id=".$row['group_id']."</a>";
                                //echo "<a href='./home.php?id='>".$row['group_name']."</a>";
                                echo "<span><a href='./home.php?id=" . $row['group_id'] ."'>" . $row['group_name'] . "</a></span>";
                            }
                        }
                    }
                }
                
            ?>
        </div>

		<div class="group_invite">
        <?php
            echo "<div class='form_groups_pos'>";
			echo "<form method='POST'>
                <input id='group_input' type='text' name='group_name' value='' placeholder='Enter Name Of Group'>
                <input id='' type='radio' name='group_type' value='public'>Public
                <input id='' type='radio' name='group_type' value='private'>Private
				<input id='group_submit' type='submit' name='group_submit' value='Create!'>
                </form>";
            echo "</div>";

            
            if (isset($_POST['group_submit'])) {
                if (!empty($_POST['group_name']) && !empty($_POST['group_type'])) {
                    $groupname = mysqli_real_escape_string($conn, $_POST['group_name']);
                    $grouptype = mysqli_real_escape_string($conn, $_POST['group_type']);
                
                    $queryExists = "SELECT group_name FROM groups WHERE group_name = '" .$groupname."'";
                    $resultExists = $conn->query($queryExists);
                    if ($resultExists->num_rows > 0) {
                        echo "<h1>Can't Create Group. Group Name already exists</h1>";
                        
                    } else {
                        $insquery = "INSERT INTO `groups` (`group_id`, `group_name`, `type`, `owner_id`) VALUES (NULL, '" . $groupname . "', '" . $grouptype . "', $userID);";
                        $conn->query($insquery);

                        $getGroupID = "SELECT group_id FROM groups WHERE group_name = '" .$groupname."'";
                        $resultID = $conn->query($getGroupID);
                        if ($resultID->num_rows > 0) { 
                            // output data of each row
                            while($row = $resultID->fetch_assoc()) {
                                $groupID = $row['group_id'];
                            } 
                        }
        
                        $query2 = "INSERT INTO `group_users` (`user_id`, `group_id`) VALUES ($userID, $groupID);";
                        $conn->query($query2);
                        header("Location: create_groups.php"); 
                    }

                    //header("Location: groups.php");
                
                    $conn->close();
                } else {
                    echo "<h1>You left out part of the form. Please enter all information</h1>";
                }
            }
		?>
        </div>
	</body>
</html>

