<?php
/*
	References: https://www.youtube.com/watch?v=pfFdbpPgg4M&list=PLBOh8f9FoHHhRk0Fyus5MMeBsQ_qwlAzG&index=14
				https://www.youtube.com/watch?v=tVLHGHshNdU&index=15&list=PLBOh8f9FoHHhRk0Fyus5MMeBsQ_qwlAzG
				I referenced these 2 videos when writing the 'likes' code 
				https://www.youtube.com/watch?v=82hnvUYY6QA   <- this one for ajax 
				https://www.youtube.com/watch?v=gdEpUPMh63s&index=31&list=WL&t=0s  <- this one for pagination in home.php and messages.php
*/
	include ('connection.php');
	session_start();
	
	if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
	}
	if(isset($_GET['id'])) {
		$uID = $_GET['id'];	
	}

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		$page = 1;
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

	//admin query (the logic for all the admin checks currently holds for only 1 admin. If more are added then it may break)
	$adminQuery = "SELECT id FROM users WHERE admin = 1";
	$result = $conn->query($adminQuery);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$adminID = $row['id'];
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
			$accessGroupIDsArray[] = $row['group_id']; //group IDs that a user is in or public
		}
	}
	$getAllGroupIDs = "select group_id from groups";
	$resultAllGroupIDs = $conn->query($getAllGroupIDs);
	if ($resultAllGroupIDs->num_rows > 0) {
		while ($row = $resultAllGroupIDs->fetch_assoc()) {
			$allGroupIDsArray[] = $row['group_id']; //all group IDs
		}
	}
	
	//allows admin users to access every group
	if ($adminID == $userID) {
		$restrictedGroupIDs = 0;
	} else {
		$restrictedGroupIDs = array_diff($allGroupIDsArray,$accessGroupIDsArray);
	} 

	$_SESSION['restricted'] = $restrictedGroupIDs; //group IDs which a user does not have access to
	foreach ($_SESSION['restricted'] as $key=>$value) {
		$restrictedID = $value;
		if ($groupID == $restrictedID) {
			header("Location: home.php?msg=" . urlencode('access_denied'));
		}
	}

	
	if (isset($_POST['msg'])) {

        $isinQuery = "SELECT * FROM group_users a WHERE a.user_id = $userID";
        $isinResult = $conn->query($isinQuery);
        if ($isinResult->num_rows > 0) {
            $message = mysqli_real_escape_string($conn, $_POST['msg']);
            $query = "INSERT INTO `direct_messages` (`msg_id`, `userid1`, `userid2`, `msg`, `post_time`) VALUES (NULL, '" . $userID . "', '" . $uID . "', '" . $message . "', CURRENT_TIMESTAMP);";
            $conn->query($query); 
            $conn->close();
        } else {

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
		<script>


		function displayMessages() {
			var xhr = new XMLHttpRequest();
			var page = "<?php echo $page ?>";
			var userID = "<?php echo $userID ?>";
            var uID = "<?php echo $uID ?>";
			xhr.open('GET', 'directmessages.php?id='+uID+'&page='+page, true);

			xhr.onload = function (){
				if(this.status == 200) {
					var msgs = JSON.parse(this.responseText);
					var output = '';

					for(var i in msgs){

							if(msgs[i].img == ''){
								var gravatar = "https://www.gravatar.com/avatar/"+msgs[i].email+"?d=retro";

								if(gravatar){
									output+= "<div id='msgWrapper"+msgs[i].msg_id+"'><span><img id ='chat_avatar' width='50' height='50' src="+gravatar+" alt='Profile Pic'><h2 id ='userName'>"+msgs[i].username+": "+msgs[i].msg+"</h2><div class='time'>"+msgs[i].post_time+"</div></span><div class='underline'></div></div>";
								} else {
									output+= "<div id='msgWrapper"+msgs[i].msg_id+"'><span><img id ='chat_avatar' width='50' height='50' src='uploads/profiledefault.png' alt='Profile Pic'><h2 id ='userName'>"+msgs[i].username+": "+msgs[i].msg+"</h2><div class='time'>"+msgs[i].post_time+"</div></span><div class='underline'></div></div>";
	
								}
							} else {
								output+= "<div id='msgWrapper"+msgs[i].msg_id+"'><span><img id ='chat_avatar' width='50' height='50' src='uploads/"+msgs[i].img+"' alt='Profile Pic'><h2 id ='userName'>"+msgs[i].username+": "+msgs[i].msg+"</h2><div class='time'>"+msgs[i].post_time+"</div></span><div class='underline'></div></div>";
							}
						
					}

					document.getElementsByClassName("feed")[0].innerHTML = output;

					if (msgs == null){
						var noMsgs = "<h2 id ='userName'>No message history. Start the conversation!</h2>";
						document.getElementsByClassName("feed")[0].innerHTML = noMsgs; 
					}
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
		<div class = "feed">

		</div>
		
		<div class="posting">
		<?php			
			echo "<form id=enterMsg>
			<input id='messeging' type='text' required='required' name='message' placeholder='Post Your Message...'>
			<input id='msg_submit' type='submit' name='submit' value='Post!'>
			</form>";

		?>
		</div>
		<div class='pagination'>
		<?php

			/*
				Section to display pagination links
				(The actual msg retrieval and display is done through messages.php and displayMessages(), this is simply to show the links 1.2.3.4....)
			*/

			$numPerPage = 10; //results per page

			$numMsgs = "SELECT COUNT(msg_id) FROM direct_messages WHERE (userid1 = $userID AND userid2 = $uID) OR (userid1 = $uID AND userid2 = $userID)"; //total number of messages (parents only) in the database
			$resultNum = $conn->query($numMsgs);
			if ($resultNum->num_rows > 0) {
				while($row = $resultNum->fetch_assoc()) {
					$numOfMsgs = $row['COUNT(msg_id)'];
				}
			}

			$numOfPages = ceil($numOfMsgs/$numPerPage); //number of total pages

			$pageFirstResult = ($page-1)*$numPerPage; //the limit starting number

			for ($page=1;$page<=$numOfPages;$page++) {
				echo '<a href="dm.php?id='.$uID.'&page='.$page.'">' .$page. '</a>'; //display page links
			}

		?>
		</div>
		<script>
		
			document.getElementById('enterMsg').addEventListener('submit', postMessage);
			document.getElementById('enterMsg').addEventListener('submit', displayMessages);
			/*
			function myFunction() {
				//document.getElementById('logo').innerHTML = 'timeee';
				var x = document.getElementsByClassName("time");
    			x[1].innerHTML = "Hello World!";
			}*/

			//document.getElementById('816').addEventListener('submit', postReply);
			//document.getElementById('816').addEventListener('submit', displayMessages);

			function postMessage(e) {
				e.preventDefault();
				
				var msg = document.getElementById('messeging').value;
				var params = "msg="+msg;

				var ID = "<?php echo $uID ?>";

				var xhr = new XMLHttpRequest();
				xhr.open('POST', 'dm.php?id='+ID,false);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

				xhr.send(params);
				document.getElementById('enterMsg').reset();
			}


		</script>
	</body>
</html>

