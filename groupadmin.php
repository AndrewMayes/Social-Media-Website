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

    if($_SESSION['adminID'] != $userID){
		header("Location: home.php?msg=" . urlencode('admins_only'));
	}
    
    
    if(isset($_GET['groups'], $_GET['users'], $_GET['choice'])) {
        if ($_GET['choice'] == 'add') {
            $query = "INSERT INTO `group_users` (`user_id`, `group_id`) VALUES ('".$_GET['users']."', '".$_GET['groups']."')";
            $conn->query($query);
        } else if ($_GET['choice'] == 'remove') {
            $query = "DELETE FROM `group_users` WHERE `group_users`.`user_id` = ".$_GET['users']." AND `group_users`.`group_id` = ".$_GET['groups']."";
            $conn->query($query);
        } else {
            $queryName = "SELECT group_name FROM groups WHERE group_id = ".$_GET['groups']."";
            $gname = $conn->query($queryName);
            if ($gname->num_rows > 0) { 
                // output data of each row
                while($row = $gname->fetch_assoc()) {
                    $groupname = $row['group_name'];  
                } 
            }
            $query = "INSERT INTO `group_invites` (`group_id`, `group_name`, `user_id`) VALUES ('".$_GET['groups']."', '".$groupname."', '".$_GET['users']."')";
            $conn->query($query);
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
				<li><a href="search_groups.php">Search Groups</a></li>
                <?php
                    if ($_SESSION['adminID'] == $userID) {
                        echo "<li class='active'><a href='groupadmin.php'>Group Administration</a></li>";
                    }
                ?>
            </ul>
		</div>
        <div class = "my_group">

            <?php

                $query = "SELECT group_id, group_name FROM groups";
                $queryResult = $conn->query($query);
                if ($queryResult->num_rows > 0) {
                    while ($row = $queryResult->fetch_assoc()) {
                        $groupIDs[] = $row['group_id'];
                        $groupNames[] = $row['group_name'];
                    }
                }

                $query2 = "SELECT id, username FROM users";
                $query2Result = $conn->query($query2);
                if ($query2Result->num_rows > 0) {
                    while ($row = $query2Result->fetch_assoc()) {
                        $groupUsernames[] = $row['username'];
                        $ids[] = $row['id'];
                    }
                }
   
            ?>
            <form action="./groupadmin.php">
            <label for="groups">Groups</label>
                <select name="groups">
                    <?php
                        for ($x=0;$x<count($groupIDs);$x++) {
                            echo "<option value='$groupIDs[$x]'>$groupIDs[$x] : $groupNames[$x]</option>";
                        }
                    ?>
                </select>
                <label for="groups">Users</label>
                <select name="users">
                    <?php
                        for ($x=0;$x<count($groupUsernames);$x++) {
                            echo "<option value='$ids[$x]'>$groupUsernames[$x]</option>";
                        }
                    ?>
                </select>
                <input type="radio" id="choice1" name="choice" value="add" required>
                <label for="choice1">Add user to group</label>
                <input type="radio" id="choice2" name="choice" value="remove" required>
                <label for="choice1">Remove user from group</label>
                <input type="radio" id="choice3" name="choice" value="invite" required>
                <label for="choice1">Invite user to group</label>
                <input type="submit" value="Submit">
            </form>
        </div>

		<div class="group_invite">
        <?php

         
            
		?>
        </div>
	</body>
</html>

