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
					<div class="help_image"><img width=75% height=75% src="img/home.png" border="2"></div>
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
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Profile"?</button>
				<div class="panel">
					<span><h3>Profile</h3>
					<div class='help_paragraph'>This is where the users can view their own information. The profile page contains the user's avatar, which the user can change it anytime, the user's groups, personal information such as the user's name, username, and email address, and the user's achievements.</div>
					<div class="help_image"><img width=75% height=75% src="img/profile.png" border="2"></div>
					<div class='help_paragraph'><b>The Change Picture button</b>: Click to change your profile picture. </div>
					<div class="help_image"><img width=30% height=30% src="img/profile_change_button.png" border="2"></div>
					<div class='help_paragraph'>After you click the button, a popup will appear to upload your picture</div>
					<div class="help_image"><img width=30% height=30% src="img/profile_change_avatar.png" border="2"></div>
					<div class='help_paragraph'>Choose your avatar picture in your local files. After you choose your picture, click upload to upload it.</div>
					<div class='help_paragraph'><b>Profile Info</b>: You will see your own user information in this page: Name, Username, Email Address, Groups, and Achievements</div>
					<div class="help_image"><img width=30% height=30% src="img/profile_info.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Search Users"?</button>
				<div class="panel">
				<span><h3>Search Users</h3>
					<div class='help_paragraph'>This is where users can search other user and view their profile page.</div>
					<div class="help_image"><img width=75% height=75% src="img/search_users.png" border="2"></div>
					<div class='help_paragraph'><b>The Input Search Box</b>: The box with "Search Users" in it is where you type other user's names or usernames to search them </div>
					<div class="help_image"><img width=70% height=70% src="img/search_users_box.png" border="2"></div>
					<div class='help_paragraph'>After you type the username or name you want to search, your search result will display. Click that user to display the profile page</div>
					<div class="help_image"><img width=50% height=50% src="img/search_users_search.png" border="2"></div>
					<div class='help_paragraph'>After you click it, it will redirect to that searched user's profile page</div>
					<div class="help_image"><img width=50% height=50% src="img/search_users_profile.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Group"?</button>
				<div class="panel">
				<span><h3>Group</h3>
					<div class='help_paragraph'>This is where you can view all your groups.</div>
					<div class="help_image"><img width=75% height=75% src="img/groups.png" border="2"></div>
					<div class='help_paragraph'><b>The Input Search Box</b>: The box with "Search Users" in it is where you type other user's names or usernames to search them </div>
					<div class="help_image"><img width=70% height=70% src="img/search_users_box.png" border="2"></div>
					<div class='help_paragraph'>After you type the username or name you want to search, your search result will display. Click that user to display the profile page</div>
					<div class="help_image"><img width=50% height=50% src="img/search_users_search.png" border="2"></div>
					<div class='help_paragraph'>After you click it, it will redirect to that searched user's profile page</div>
					<div class="help_image"><img width=50% height=50% src="img/search_users_profile.png" border="2"></div>
					</span>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Group Invites"?</button>
				<div class="panel">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Create Group"?</button>
				<div class="panel">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
				</div>
			</div>

			<div class="help_division">
				<button class="accordion">What is "Create Group"?</button>
				<div class="panel">
				<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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

