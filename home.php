<?php
/*
	References: https://www.youtube.com/watch?v=pfFdbpPgg4M&list=PLBOh8f9FoHHhRk0Fyus5MMeBsQ_qwlAzG&index=14
				https://www.youtube.com/watch?v=tVLHGHshNdU&index=15&list=PLBOh8f9FoHHhRk0Fyus5MMeBsQ_qwlAzG
				I referenced these 2 videos when writing the 'likes' code 
				https://www.youtube.com/watch?v=82hnvUYY6QA   <- this one for ajax 
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
	/*
		Section of code which restricts user's access to groups which they are not members of or which are private
		/
		/
		/
		/
		/
	*/
	$getAccessGroupIDs = "select b.group_id from users a, groups b, group_users c where a.id = c.user_id and b.group_id = c.group_id and a.id = ".$userID." union select group_id from groups where type = 'public'";
	$resultAccessGroupIDs = $conn->query($getAccessGroupIDs);
	if ($resultAccessGroupIDs->num_rows > 0) {
		while ($row = $resultAccessGroupIDs->fetch_assoc()) {
			$accessGroupIDsArray[] = $row['group_id'];
		}
	}
	$getAllGroupIDs = "select group_id from groups";
	$resultAllGroupIDs = $conn->query($getAllGroupIDs);
	if ($resultAllGroupIDs->num_rows > 0) {
		while ($row = $resultAllGroupIDs->fetch_assoc()) {
			$allGroupIDsArray[] = $row['group_id'];
		}
	}
	$restrictedGroupIDs = array_diff($allGroupIDsArray,$accessGroupIDsArray);
	$_SESSION['restricted'] = $restrictedGroupIDs;
	foreach ($_SESSION['restricted'] as $key=>$value) {
		$restrictedID = $value;
		if ($groupID == $restrictedID) {
			header("Location: home.php?msg=" . urlencode('access_denied'));
		}
	}
	/*
		Section of code which deals with 'likes'. Reference for this section of code is found at the top of this file
		/
		/
		/
		/
		/
	*/
	if (isset($_GET['liked'])) {
		$hasUserLikedQuery = "SELECT `user_id` FROM `messages_likes` WHERE `msg_id` = " . $_GET['liked'] . " AND `user_id` = " . $userID . "";
		$userLiked = $conn->query($hasUserLikedQuery);
		if (!$userLiked->num_rows > 0) { 
			// output data of each row
			$likedQuery = "UPDATE `messages` SET `likes` = `likes`+1 WHERE `messages`.`msg_id` = " . $_GET['liked'] . "";
			$postLikesQuery = "INSERT INTO `messages_likes` (`msg_id`, `user_id`) VALUES ('" . $_GET['liked'] . "', '" . $userID . "')";
			$conn->query($likedQuery);
			$conn->query($postLikesQuery);
			
			//make sure user can't like and dislike a post
			$hasUserDisLikedQuery = "SELECT `user_id` FROM `messages_dislikes` WHERE `msg_id` = " . $_GET['liked'] . " AND `user_id` = " . $userID . "";
			$userDisLiked = $conn->query($hasUserDisLikedQuery);
			if (!$userDisLiked->num_rows > 0) {
			} else {
				$unDislikedQuery = "UPDATE `messages` SET `dislikes` = `dislikes`-1 WHERE `messages`.`msg_id` = " . $_GET['liked'] . "";
				$postunDisLikesQuery = "DELETE FROM `messages_dislikes` WHERE `messages_dislikes`.`msg_id` = " . $_GET['liked'] . " AND `messages_dislikes`.`user_id` = " . $userID . "";
				$conn->query($unDislikedQuery);
				$conn->query($postunDisLikesQuery);
			}

			header("Location: home.php?id=" . $groupID . "");
		} else {
			$unlikedQuery = "UPDATE `messages` SET `likes` = `likes`-1 WHERE `messages`.`msg_id` = " . $_GET['liked'] . "";
			$postunLikesQuery = "DELETE FROM `messages_likes` WHERE `messages_likes`.`msg_id` = " . $_GET['liked'] . " AND `messages_likes`.`user_id` = " . $userID . "";
			$conn->query($unlikedQuery);
			$conn->query($postunLikesQuery);
			header("Location: home.php?id=" . $groupID . "");
		}
	}

	if (isset($_GET['disliked'])) {
		$hasUserDisLikedQuery = "SELECT `user_id` FROM `messages_dislikes` WHERE `msg_id` = " . $_GET['disliked'] . " AND `user_id` = " . $userID . "";
		$userDisLiked = $conn->query($hasUserDisLikedQuery);
		if (!$userDisLiked->num_rows > 0) { 
			// output data of each row
			$dislikedQuery = "UPDATE `messages` SET `dislikes` = `dislikes`+1 WHERE `messages`.`msg_id` = " . $_GET['disliked'] . "";
			$postDisLikesQuery = "INSERT INTO `messages_dislikes` (`msg_id`, `user_id`) VALUES ('" . $_GET['disliked'] . "', '" . $userID . "')";
			$conn->query($dislikedQuery);
			$conn->query($postDisLikesQuery);

			//make sure user can't like and dislike a post
			$hasUserLikedQuery = "SELECT `user_id` FROM `messages_likes` WHERE `msg_id` = " . $_GET['disliked'] . " AND `user_id` = " . $userID . "";
			$userLiked = $conn->query($hasUserLikedQuery);
			if (!$userLiked->num_rows > 0) {
			} else {
				$unlikedQuery = "UPDATE `messages` SET `likes` = `likes`-1 WHERE `messages`.`msg_id` = " . $_GET['disliked'] . "";
				$postunLikesQuery = "DELETE FROM `messages_likes` WHERE `messages_likes`.`msg_id` = " . $_GET['disliked'] . " AND `messages_likes`.`user_id` = " . $userID . "";
				$conn->query($unlikedQuery);
				$conn->query($postunLikesQuery);
			}

			header("Location: home.php?id=" . $groupID . "");
		} else {
			$unDislikedQuery = "UPDATE `messages` SET `dislikes` = `dislikes`-1 WHERE `messages`.`msg_id` = " . $_GET['disliked'] . "";
			$postunDisLikesQuery = "DELETE FROM `messages_dislikes` WHERE `messages_dislikes`.`msg_id` = " . $_GET['disliked'] . " AND `messages_dislikes`.`user_id` = " . $userID . "";
			$conn->query($unDislikedQuery);
			$conn->query($postunDisLikesQuery);
			header("Location: home.php?id=" . $groupID . "");
		}
	}

	if (isset($_POST['submit']) && !empty($_POST['message'])) {
		$message = mysqli_real_escape_string($conn, $_POST['message']);
		$query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`, `likes`, `dislikes`, `parent_id`, `hasChildren`) VALUES (NULL, '" . $userID . "', '" . $message . "', CURRENT_TIMESTAMP, '" . $groupID . "',0,0,0,0);";
		$conn->query($query);
		header("Location: home.php?id=" . $groupID . ""); 
		$conn->close();
	}
/*
	if (isset($_POST['reply_submit'])) {
		$message = mysqli_real_escape_string($conn, $_POST['reply']);
		$query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`, `likes`, `parent_id`) VALUES (NULL, '" . $userID . "', '" . $message . "', CURRENT_TIMESTAMP, '" . $groupID . "',0,".$row['msg_id'].");";
		$conn->query($query);
		header("Location: home.php?id=" . $groupID . ""); 
		$conn->close();
	}*/
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
		<script>
/*
			$(document).ready(function() {
				$("#msg_submit").click(function() {
					var message = {
						message: $message.val();
					};

					$.ajax({
						type: 'POST',
						url: '',
						data: message,
						success: function(newMessage) {
							$
						}
					});
				})

			});*/

			//document.getElementById('msg_submit').addEventListener('')

					function displayMessages() {
					var xhr = new XMLHttpRequest();
					xhr.open('GET', 'messages.php', true);

					xhr.onload = function (){
						if(this.status == 200) {
							var msgs = JSON.parse(this.responseText);
							var output = '';


							/*
							echo "<div class='reply_pos'><form action='home.php?id=" . $groupID . "' method='POST'>
							<input id='reply' type='text' name='reply' value='' placeholder='Post Your Reply...'>
							<input id='reply_submit' type='submit' name='reply_submit' value='Reply!'>
							</form></div>";
							*/

							var ID = "<?php echo $groupID ?>";
							for(var i in msgs){
								if (ID == msgs[i].group_id){
									output+= "<span><img id ='chat_avatar' width='50' height='50' src='uploads/"+msgs[i].img+"' alt='Profile Pic'><h2 id ='userName'>"+msgs[i].username+": "+msgs[i].msg+"</h2><div class='time'>"+msgs[i].post_time+"</div></span><div class='reply_pos'><form action='home.php?id="+ID+"' method='POST'><input id='reply' type='text' name='reply' value='' placeholder='Post Your Reply...'><input id='reply_submit' type='submit' name='reply_submit' value='Reply!'></form></div><form action='home.php?id="+ID+" &liked="+msgs[i].msg_id+"' method='POST'><div class='likeys'><input id='like_input'type='submit' name='like' value='Like'> "+msgs[i].likes+" likes</div></form><form action='home.php?id="+ID+" &disliked="+msgs[i].msg_id+"' method='POST'><div class='dislikeys'><input id='dislike_input'type='submit' name='dislike' value='Dislike'> "+msgs[i].dislikes+" dislikes</div></form><div class='underline'></div>";
								}
							}

							document.getElementsByClassName("feed")[0].innerHTML = output;
						}
					}

					xhr.send();
				}

				window.onload=displayMessages;

		</script>
	</head>
	<body>
		<div class="header">
			<?php 
				echo "<div id='logo'>";
					echo $_SESSION['username'];
				echo "</div>";
				echo "<div id='group_logo'>";
					$queryGroups = "SELECT groups.group_id,groups.group_name FROM groups WHERE groups.group_id = ".$groupID."";
					$userGroups = $conn->query($queryGroups);
					if ($userGroups->num_rows > 0) { 
					// output data of each row
						while($row = $userGroups->fetch_assoc()) {
							if($row['group_id']) {
								echo  $row['group_name']. "";
							}
						}
					} 
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
            </ul>
		</div>
		<div class = "feed">

		</div>
		
		<div class="posting">
		<?php
			echo "<form action='home.php?id=" . $groupID . "' method='POST'>
				<input id='messeging' type='text' name='message' value='' placeholder='Post Your Status...'>
				<input id='msg_submit' type='submit' name='submit' value='Post!'>
				</form>";
		?>
		</div>
	</body>
</html>
