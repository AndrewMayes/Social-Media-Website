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
		</div>
		<div class="position">
			<div class = "feed">
                <h1>My Groups</h1>
				<?php
                    $query = "SELECT group_id, group_name FROM groups";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            foreach ($myGroupIDsArray as $value) {
                                if ($row['group_id'] == $value) {
                                    //echo "<a href='./home.php?id=".$row['group_id']."</a>";
                                    //echo "<a href='./home.php?id='>".$row['group_name']."</a>";
                                    echo "<a href='./home.php?id=" . $row['group_id'] ."'>" . $row['group_name'] . "</a>";
                                    echo "<br>";
                                }
                            }
                        }
                    }
                    
				?>
			</div>
		</div>
		<div class="posting">
		<?php
			echo "<form method='POST'>
                <input id='messeging' type='text' name='group_name' value='' placeholder='Enter Name Of Group'>
                <input id='' type='radio' name='group_type' value='public'>Public
                <input id='' type='radio' name='group_type' value='private'>Private
				<input id='msg_submit' type='submit' name='group_submit' value='Create!'>
                </form>";

            
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
                        header("Location: groups.php"); 
                    }

                    //header("Location: groups.php");
                
                    $conn->close();
                } else {
                    echo "<h1>You left out part of the form. Please enter all information</h1>";
                }
            } 



		?>
		</div>
        <div class = bottom>
            <?php

                echo "<form method='POST'>
                <input id='messeging' type='text' name='inv_username' value='' placeholder='Enter Username To Invite'>
                <input id='messeging' type='text' name='inv_groupname' value='' placeholder='Enter Group Name'>
                <input id='msg_submit' type='submit' name='inv_submit' value='Invite!'>
                </form>";

            

                $queryOwnedGroups = "SELECT * FROM groups WHERE owner_id = $userID and type = 'private'";
                $resultOwnedGroups = $conn->query($queryOwnedGroups);
                if ($resultOwnedGroups->num_rows > 0) {
                    while ($row_owned = $resultOwnedGroups->fetch_assoc()) {
                        $ownedGroupsArray[] = $row_owned['group_name'];
                    }
                }

                if (isset($_POST['inv_submit'])) {

                    $invusername = mysqli_real_escape_string($conn, $_POST['inv_username']);
                    $invgroupname = mysqli_real_escape_string($conn, $_POST['inv_groupname']);

                    $printGroupName = stripslashes($invgroupname);

                    $getGroupID = "SELECT group_id FROM groups WHERE group_name = '" .$invgroupname."'";
                    $resultID = $conn->query($getGroupID);
                    if ($resultID->num_rows > 0) { 
                        // output data of each row
                        while($row_id = $resultID->fetch_assoc()) {
                            $invgroupID = $row_id['group_id'];
                        } 
                    }
 
                    $queryGroupMembers = "SELECT distinct username, id from users a inner join group_users b on a.id = b.user_id where b.group_id = $invgroupID";
                    $resultGroupMembers = $conn->query($queryGroupMembers);
                    if ($resultGroupMembers->num_rows > 0) {
                        while ($row_members = $resultGroupMembers->fetch_assoc()) {
                            $groupMembersArray[] = $row_members['username'];
                        }
                    }

                    $queryPendingGroupMembers = "SELECT distinct username, id from users a inner join group_invites b on a.id = b.user_id where b.group_id = $invgroupID";
                    $resultPendingGroupMembers = $conn->query($queryPendingGroupMembers);
                    if ($resultPendingGroupMembers->num_rows > 0) {
                        while ($row_pendingmembers = $resultPendingGroupMembers->fetch_assoc()) {
                            $pendingGroupMembersArray[] = $row_pendingmembers['username'];
                        }
                    }
                   
                    //function retrieved from http://php.net/manual/en/function.in-array.php#89256
                    function in_arrayi($needle, $haystack) {
                        return in_array(strtolower($needle), array_map('strtolower', $haystack));
                    }

                    $queryGetType = "SELECT type FROM groups WHERE group_id = $invgroupID";
                    $resultGetType = $conn->query($queryGetType);
                    if ($resultGetType->num_rows > 0) {
                        while ($row_type = $resultGetType->fetch_assoc()) {
                            $type = $row_type['type'];
                        }
                    } else {

                    }

                    if ($type == 'private') {
                        //can only invite people to groups that you are the owner of.
                        if (in_arrayi($printGroupName,$ownedGroupsArray)) {
                            if (!in_arrayi($invusername,$groupMembersArray)) {
                                if (!in_arrayi($invusername,$pendingGroupMembersArray)) {

                                    $getUserID = "SELECT id FROM users WHERE username = '" .$invusername."'";
                                    $resultUserID = $conn->query($getUserID);
                                    if ($resultUserID->num_rows > 0) { 
                                        // output data of each row
                                        while($row = $resultUserID->fetch_assoc()) {
                                            $invuserID = $row['id'];
                                        } 
                                    }
                
                                    $query = "SELECT username FROM users WHERE username = '" . $invusername . "'";
                                    $resultUsers = $conn->query($query);
                                    if (!$resultUsers->num_rows > 0) { 
                                        echo "<p>Username not found</p>";
                                    } else {
                                        while($row = $resultUsers->fetch_assoc()) {
                                            $invquery = "INSERT INTO `group_invites` (`group_id`, `group_name`, `user_id`) VALUES ('$invgroupID','$invgroupname','$invuserID')";
                                            $conn->query($invquery);
                                            header("Location: groups.php?inv_success");
                                        } 
                                    }
                
                                    //header("Location: groups.php"); 

                                } else {
                                    echo "<p>User already has a pending invite</p>";
                                }
                            } else {
                                echo "<p>User is already in the group</p>";  
                            }
                        } else {
                            echo "<p>You can not invite people to this group</p>";
                        }                                               
                    } else if ($type == 'public') {
                        if (!in_arrayi($invusername,$groupMembersArray)) {
                            if (!in_arrayi($invusername,$pendingGroupMembersArray)) {

                                $getUserID = "SELECT id FROM users WHERE username = '" .$invusername."'";
                                $resultUserID = $conn->query($getUserID);
                                if ($resultUserID->num_rows > 0) { 
                                    // output data of each row
                                    while($row = $resultUserID->fetch_assoc()) {
                                        $invuserID = $row['id'];
                                    } 
                                }
            
                                $query = "SELECT username FROM users WHERE username = '" . $invusername . "'";
                                $resultUsers = $conn->query($query);
                                if (!$resultUsers->num_rows > 0) { 
                                    echo "<p>Username not found</p>";
                                } else {
                                    while($row = $resultUsers->fetch_assoc()) {
                                        $invquery = "INSERT INTO `group_invites` (`group_id`, `group_name`, `user_id`) VALUES ('$invgroupID','$invgroupname','$invuserID')";
                                        $conn->query($invquery);
                                        header("Location: groups.php?inv_success");
                                    } 
                                }
            
                                //header("Location: groups.php"); 

                            } else {
                                echo "<p>User already has a pending invite</p>";
                            }
                        } else {
                            echo "<p>User is already in the group</p>";  
                        }
                    } else {
                        echo "<p>Group not found. Try again</p>"; 
                    }

            
                }

                /*
                    Accepting invites
                */
                $queryInvites = "SELECT * FROM group_invites a WHERE a.user_id = $userID";
                $resultInvites = $conn->query($queryInvites);
                if (!$resultInvites->num_rows > 0) { 
                    echo "<p>You have no group invites</p>";
                } else {
                    while($row = $resultInvites->fetch_assoc()) {
                        echo "<p>You were invited to ".$row['group_name']."</p>";
                        echo "<form method='POST'><input id='msg_submit' type='submit' name='join_group' value='Join Group'></form>";
                        if (isset($_POST['join_group'])) {
                            $fixedGroupName = addslashes($row['group_name']);
                            $getGroupID = "SELECT group_id FROM groups WHERE group_name = '".$fixedGroupName."'";
                            $resultID = $conn->query($getGroupID);
                            if ($resultID->num_rows > 0) { 
                                // output data of each row
                                while($row = $resultID->fetch_assoc()) {
                                    $groupID = $row['group_id'];
                                } 
                            }
        
                            $queryJoin = "INSERT INTO `group_users` (`user_id`, `group_id`) VALUES ('$userID', '$groupID');";
                            $conn->query($queryJoin);
                            $queryRemoveInv =  "DELETE FROM `group_invites` WHERE `group_invites`.`group_id` = $groupID AND `group_invites`.`user_id` = $userID";
                            $conn->query($queryRemoveInv);
                            header("Location: groups.php");
                        }
                    } 
                }



            ?>
        </div>
	</body>
</html>

