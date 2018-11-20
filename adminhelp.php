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
				<li><a href="profile.php">Profile</a></li>
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
                        echo "<li class='active'><a href='adminhelp.php'>Help</a></li>";
                    }
                    else{
                        echo "<li class='active'><a href='help.php'>Help</a></li>";
                    }
                ?>
            </ul>
		</div>
        <div class="help">
			<center><span><h2>FAQ</h2></span></center>
			
			<div class="help_division">
				<button class="accordion">What is "Home"?</button>
				<div class="panel">
					<span><h3>Home</h3>
					<div class='help_paragraph'>This is where the users can interact with other users. Users can send messages, like or dislike messages, and reply messages in the Home page.</div>
					<div class="help_image"><img width=75% height=75% src="img/home_admin.png" border="2"></div>
					<div class='help_paragraph'><b>The Message Input Box</b>: The box with "Post Your Status" in it is where you type your posts or messages.</div>
					<div class="help_image"><img width=50% height=50% src="img/home_input_box.png" border="2"></div>
					<div class='help_paragraph'><b>The "Post" Button</b>: Click it to post your messages after you type your post or messages in the Message Input Box.</div>
					<div class="help_image"><img width=15% height=15% src="img/home_post.png" border="2"></div>
					<div class='help_paragraph'><b>The "Like" or "Dislike" Button</b>: Click it when you like or dislike the post.</div>
					<div class="help_image"><img width=25% height=25% src="img/home_like.png" border="2"></div>
					<div class='help_paragraph'><b>The Reply Input Box</b>: The box with "Post Your Reply" in it is where you type your reply to a certain post or message.</div>
					<div class="help_image"><img width=30% height=30% src="img/home_reply_input.png" border="2"></div>
					<div class='help_paragraph'><b>The "Reply" Button</b>: Click it to reply a certain post or message after you type your reply in the Reply Input Box.</div>
					<div class="help_image"><img width=15% height=15% src="img/home_reply_post.png" border="2"></div>
					<div class='help_paragraph'><b>The "Show Replies" Button</b>: Click it to view all the replies of a post or message.</div>
					<div class="help_image"><img width=15% height=15% src="img/home_show_replies.png" border="2"></div>
					<div class='help_paragraph'> After you click the button, it will display all the replies.</div>
					<div class="help_image"><img width=55% height=55% src="img/home_replies.png" border="2"></div>
					<div class='help_paragraph'><b>The "Archive" Button</b>: ADMIN EXCLUSIVE FEATURE! Click it to stop activities in Home. Users can't send or reply any message. They can't even like or dislike messages. Click it again to revert Home back to normal</div>
					<div class="help_image"><img width=20% height=20% src="img/home_archive.png" border="2"></div>
					<div class='help_paragraph'><b>The "Delete" Button</b>: ADMIN EXCLUSIVE FEATURE! Click it to delete messages or replies.</div>
					<div class="help_image"><img width=20% height=20% src="img/home_delete.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Profile"?</button>
				<div class="panel">
					<span><h3>Profile</h3>
					<div class='help_paragraph'>This is where the users can view their own information. The profile page contains the user's avatar, which the user can change it anytime, the user's groups, personal information such as the user's name, username, and email address, and the user's achievements.</div>
					<div class="help_image"><img width=75% height=75% src="img/profile_admin.png" border="2"></div>
					<div class='help_paragraph'><b>The Change Picture button</b>: Click to change your profile picture. </div>
					<div class="help_image"><img width=30% height=30% src="img/profile_change_button_admin.png" border="2"></div>
					<div class='help_paragraph'>After you click the button, a popup will appear to upload your picture</div>
					<div class="help_image"><img width=30% height=30% src="img/profile_change_avatar.png" border="2"></div>
					<div class='help_paragraph'>Choose your avatar picture in your local files. After you choose your picture, click upload to upload it.</div>
					<div class='help_paragraph'><b>Profile Info</b>: You will see your own user information in this page: Name, Username, Email Address, Groups, and Achievements</div>
					<div class="help_image"><img width=30% height=30% src="img/profile_info_admin.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What are the "Achievements"?</button>
				<div class="panel">
					<span><h3>Achievements</h3>
					<div class='help_paragraph'>You can have achievements in this website. These are the achievements:</div>
					<div class='help_paragraph'><b>":Active Poster:"</b>: You will have this achievement when you have 3 or more messages posted. </div>
					<div class='help_paragraph'><b>":Most Liked Post:"</b>: You will have this achievement when you have 3 or more likes in your Post that you have posted. ONLY ONE USER CAN HAVE THIS ACHIEVEMENT! </div>
					<div class='help_paragraph'><b>":Most Disliked Post:"</b>: You will have this achievement when you have 3 or more dislikes in your Post that you have posted. ONLY ONE USER CAN HAVE THIS ACHIEVEMENT! </div>
					<div class='help_paragraph'><b>":Group Collector:"</b>: You will have this achievement when you have 3 or more Groups joined. </div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Search Users"?</button>
				<div class="panel">
				<span><h3>Search Users</h3>
					<div class='help_paragraph'>This is where users can search other user and view their profile page.</div>
					<div class="help_image"><img width=75% height=75% src="img/search_users_admin.png" border="2"></div>
					<div class='help_paragraph'><b>The Input Search Box</b>: The box with "Search Users" in it is where you type other user's names or usernames to search them </div>
					<div class="help_image"><img width=70% height=70% src="img/search_users_box.png" border="2"></div>
					<div class='help_paragraph'>After you type the username or name you want to search, your search result will display. Click that user to display the profile page</div>
					<div class="help_image"><img width=50% height=50% src="img/search_users_search.png" border="2"></div>
					<div class='help_paragraph'>After you click it, it will redirect to that searched user's profile page</div>
					<div class="help_image"><img width=50% height=50% src="img/search_users_profile_admin.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Groups"?</button>
				<div class="panel">
				<span><h3>Groups</h3>
					<div class='help_paragraph'>This is where you can view all your groups.</div>
					<div class="help_image"><img width=75% height=75% src="img/groups_admin.png" border="2"></div>
					<div class='help_paragraph'><b>Groups</b>: It is located on the side menu. All of your groups will display.</div>
					<div class="help_image"><img width=30% height=30% src="img/groups_groups.png" border="2"></div>
					<div class='help_paragraph'>Click one of your groups and it will redirect you to your home group.</div>
					<div class="help_image"><img width=50% height=50% src="img/groups_admin.png" border="2"></div>
					<h3>Group Access</h3>
						<div class='help_paragraph'>Groups can be <b>public</b> or <b>private.</b></div>
						<div class='help_paragraph'>You can view (read posts and replies) public groups; however, you must first join the group before you are able to post, reply, like, or dislike.</div>
						<div class='help_paragraph'>You can not view or interact with private groups unless you are invited to join the group. Once invited, accept your invite to gain access to the group.</div>
						<div class='help_paragraph'>*This applies to admins as well. To circumvent this, simply add yourself to any group in the "Group Administration" tab*.</div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Group Invites"?</button>
				<div class="panel">
				<span><h3>Group Invites</h3>
					<div class='help_paragraph'>This is where you can invite other users to your public or private groups.</div>
					<div class="help_image"><img width=75% height=75% src="img/group_invite_admin.png" border="2"></div>
					<div class='help_paragraph'><b>The Username and Group Invite Input Boxes</b>: It is the boxes with "Enter Username to Invite" and "Enter Group Name" in it is where you put a username you want to invite and the group that you want the user to join.</div>
					<div class="help_image"><img width=70% height=70% src="img/group_invite_boxes.png" border="2"></div>
					<div class='help_paragraph'><b>The Invite Button</b>:Click it to invite a user to a group after you input a username and a group in The Username and Group Invite Input Boxes.</div>
					<div class="help_image"><img width=30% height=30% src="img/group_invite_button.png" border="2"></div>
					<div class='help_paragraph'><b>The Group Invitation Box</b>:The box with "You have no group invites" in it is where you can view you group invitation from other users.</div>
					<div class="help_image"><img width=50% height=50% src="img/group_invite_screen.png" border="2"></div>
					<div class='help_paragraph'>If you have an invitation, it will display the group name and a "Join" button to join.</div>
					<div class="help_image"><img width=70% height=70% src="img/group_invite_join.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Create Group"?</button>
				<div class="panel">
				<span><h3>Create Group</h3>
					<div class='help_paragraph'>This is where you can create public or private groups.</div>
					<div class="help_image"><img width=75% height=75% src="img/create_group_admin.png" border="2"></div>
					<div class='help_paragraph'><b>The Group Name Input Box</b>: It is the boxes with "Enter Name of Group" in it is where you input the group name you want to create.</div>
					<div class="help_image"><img width=70% height=70% src="img/create_group_box.png" border="2"></div>
					<div class='help_paragraph'><b>The Create Button</b>:Click it to create your group after you input your group name in The Group Name Input Box and choose your group type in The Type Options.</div>
					<div class="help_image"><img width=20% height=20% src="img/create_group_button.png" border="2"></div>
					<div class='help_paragraph'><b>The Group List Box</b>:The box with the list groups you belong.</div>
					<div class="help_image"><img width=70% height=70% src="img/create_group_list.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Search Group"?</button>
				<div class="panel">
				<span><h3>Search Group</h3>
					<div class='help_paragraph'>This is where you can only search public groups.</div>
					<div class="help_image"><img width=75% height=75% src="img/search_group_admin.png" border="2"></div>
					<div class='help_paragraph'><b>The Group Name Input Box</b>: It is the boxes with "Enter Name of Group" in it is where you input the public group name you want to join.</div>
					<div class="help_image"><img width=70% height=70% src="img/search_group_box.png" border="2"></div>
					<div class='help_paragraph'><b>The Search Button</b>:Click it to search your group after you input your group name in The Group Name Input Box.</div>
					<div class="help_image"><img width=20% height=20% src="img/search_group_button.png" border="2"></div>
					<div class='help_paragraph'><b>The Join Button</b>:Click it to join the group you have searched.</div>
					<div class="help_image"><img width=20% height=20% src="img/search_group_join.png" border="2"></div>
					</span>
				</div>
			</div>	

			<div class="help_division">
				<button class="accordion">What is "Group Administration"?</button>
				<div class="panel">
				<span><h3>Group Administration</h3>
					<div class='help_paragraph'>ADMIN EXCLUSIVE FEATURE! This is where you can add, invite, or delete user to a group.</div>
					<div class="help_image"><img width=75% height=75% src="img/group_admin.png" border="2"></div>
					<div class='help_paragraph'><b>The Group and User Selectors</b>: This is where you select a User you want to add, invite or delete to a group.</div>
					<div class="help_image"><img width=30% height=30% src="img/group_admin_select.png" border="2"></div>
					<div class='help_paragraph'><b>The Add, Remove and Invite Options</b>:Choose whether you to add, remove or invite a user to a group after you select a user and group.</div>
					<div class="help_image"><img width=50% height=50% src="img/group_admin_options.png" border="2"></div>
					<div class='help_paragraph'><b>The Submit Button</b>:Click it after you choose the option to add, remove or invite a user.</div>
					<div class="help_image"><img width=20% height=20% src="img/group_admin_button.png" border="2"></div>
					</span>
				</div>
			</div>	
		</div>

	</body>
</html>

<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
    acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
}
</script>

