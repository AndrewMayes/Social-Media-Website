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
                <li class="active"><a href="invite_groups.php">Groups Invites</a></li>
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
        
        <div class='posting_invite'>
        <?php
            echo " <div class='form_invite'>";
            echo "<form method='POST'>
            <input id='invites_input_1' type='text' name='inv_username' value='' placeholder='Enter Username To Invite'>
            <input id='invites_input_2' type='text' name='inv_groupname' value='' placeholder='Enter Group Name'>
            <input id='invites_submit' type='submit' name='inv_submit' value='Invite!'>
            </form>";
            echo "</div>";

            $queryOwnedGroups = "SELECT * FROM groups WHERE owner_id = $userID and type = 'private'";
            $resultOwnedGroups = $conn->query($queryOwnedGroups);
            if ($resultOwnedGroups->num_rows > 0) {
                while ($row_owned = $resultOwnedGroups->fetch_assoc()) {
                    $ownedGroupsArray[] = $row_owned['group_name'];
                }
            }

            echo "<div class='my_group_invite'>";
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

                $archivedQuery = "SELECT isArchived FROM groups WHERE group_id = $invgroupID";
                $archived = $conn->query($archivedQuery);
                if ($archived->num_rows > 0) {
                    while ($row = $archived->fetch_assoc()) {
                        $resultArchived = $row['isArchived'];
                    }
                }
                if ($resultArchived == 0) {
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
                                            header("Location: invite_groups.php?inv_success");
                                        } 
                                    }
                
                                    //header("Location: groups.php"); 

                                } else {
                                    echo "<center><span>User already has a pending invite</span></center>";
                                }
                            } else {
                                echo "<center><span>User is already in the group</span></center>";  
                            }
                        } else {
                            echo "<center><span>You can not invite people to this group</span></center>";
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
                                        header("Location: invite_groups.php?inv_success");
                                    } 
                                }
            
                                //header("Location: groups.php"); 

                            } else {
                                echo "<center><span>User already has a pending invite</span><center>";
                            }
                        } else {
                            echo "<center><span>User is already in the group</span><center>";  
                        }
                    } else {
                        echo "<center><span>Group not found. Try again</span><center>"; 
                    }
                }
            }

            /*
                Accepting invites
            */
            
            $queryInvites = "SELECT * FROM group_invites a WHERE a.user_id = $userID";
            $resultInvites = $conn->query($queryInvites);
            if (!$resultInvites->num_rows > 0) { 
                echo "<center><span>You have no group invites</span></center>";
            } else {
                while($row = $resultInvites->fetch_assoc()) {
                    $fixedGroupName = addslashes($row['group_name']);
                    $getGroupID = "SELECT group_id FROM groups WHERE group_name = '".$fixedGroupName."'";
                    $resultID = $conn->query($getGroupID);
                    if ($resultID->num_rows > 0) { 
                        // output data of each row
                        while($row2 = $resultID->fetch_assoc()) {
                            $groupID = $row2['group_id'];
                        } 
                    }
                    echo "<span>You were invited to <b>".$row['group_name']."</b></span>";
                    echo "<span><form method='POST'><input id='invites_submit' type='submit' name='$groupID' value='Join Group'></form></span>";
                    echo "<div class='underline'></div>";
                    if (isset($_POST["$groupID"])) {
                        $archivedQuery = "SELECT isArchived FROM groups WHERE group_id = $groupID";
                        $archived = $conn->query($archivedQuery);
                        if ($archived->num_rows > 0) {
                            while ($row = $archived->fetch_assoc()) {
                                $resultArchived = $row['isArchived'];
                            }
                        }
                        if ($resultArchived == 0) {
    
                            $queryJoin = "INSERT INTO `group_users` (`user_id`, `group_id`) VALUES ('$userID', '$groupID');";
                            $conn->query($queryJoin);
                            $queryRemoveInv =  "DELETE FROM `group_invites` WHERE `group_invites`.`group_id` = $groupID AND `group_invites`.`user_id` = $userID";
                            $conn->query($queryRemoveInv);
                            header("Location: invite_groups.php?group_joined");
                        }
                    }
                } 
            }

            
            echo"</div>"
        ?>
        </div>
	</body>
</html>

