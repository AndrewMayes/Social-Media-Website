<?php

	include ('connection.php');
    session_start();
    
    if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
	}
	
	if(isset($_GET['gid'])) {
		$groupID = $_GET['gid'];	
	} else {
		$groupID = "1";
    }
    
    if(isset($_GET['id'])) {
		$uID = $_GET['id'];	
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

	$numPerPage = 10; //results per page

	$numMsgs = "SELECT COUNT(msg_id) FROM direct_messages WHERE (userid1 = $userID AND userid2 = $uID) OR (userid1 = $uID AND userid2 = $userID)"; //total number of messages (parents only) in the database
	$resultNum = $conn->query($numMsgs);
	if ($resultNum->num_rows > 0) {
		while($row = $resultNum->fetch_assoc()) {
			$numOfMsgs = $row['COUNT(msg_id)'];
		}
	}

	$numOfPages = ceil($numOfMsgs/$numPerPage); //number of total pages

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		$page = 1;
	}


	$pageFirstResult = ($page-1)*$numPerPage; //the limit starting number

	$postFeed = "SELECT img, username, msg, post_time, msg_id, email from users inner join direct_messages on users.id = direct_messages.userid1 WHERE (userid1 = $userID AND userid2 = $uID) OR (userid1 = $uID AND userid2 = $userID) ORDER BY msg_id DESC LIMIT ".$pageFirstResult.",".$numPerPage."";
	$result = $conn->query($postFeed);
	if ($result->num_rows > 0) { 
		// output data of each row
		while($row = $result->fetch_assoc()) {
			
			$messages[] = $row;

		} 
	} else {
		//echo "<h2 id ='userName'>No messages in this channel yet. Come back soon!</h2>";
	}
	
	foreach ($messages as $key => $msg) {
		$messages[$key]['msg'] = htmlspecialchars($messages[$key]['msg']);
	}
	foreach ($messages as $key => $email) {
		$messages[$key]['email'] = md5($messages[$key]['email']);
	}

	$jsonMessages = json_encode($messages);
	echo $jsonMessages;

	$conn->close();
?>