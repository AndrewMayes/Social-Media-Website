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

	$numPerPage = 10; //results per page

	$numMsgs = "SELECT COUNT(msg_id) FROM messages WHERE parent_id = 0 AND group_id = $groupID"; //total number of messages (parents only) in the database
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

	/*
	$query2 = "SELECT * FROM messages WHERE parent_id = 0 AND group_id = $groupID LIMIT ".$pageFirstResult.",".$numPerPage."";
	$resultQuery2 = $conn->query($query2);
	if ($resultQuery2->num_rows > 0) {
		while($row = $resultQuery2->fetch_assoc()) {
			echo $row['msg_id'] . ' ' . $row['msg'] . '<br>';
		}
	}*/

	/*
	for ($page=1;$page<=$numOfPages;$page++) {
			echo '<a href="messages.php?id='.$groupID.'&page='.$page.'">' .$page. '</a>'; //display page links
		}
	*/



	$postFeed = "SELECT group_id, img, username, msg, post_time, msg_id, likes, dislikes from users inner join messages on users.id = messages.user_id WHERE group_id = $groupID AND parent_id = 0 ORDER BY msg_id DESC LIMIT ".$pageFirstResult.",".$numPerPage."";
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

	$jsonMessages = json_encode($messages);
	echo $jsonMessages;

	$conn->close();
?>