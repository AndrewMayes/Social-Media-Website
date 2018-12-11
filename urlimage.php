<?php
    include ('home.php');
    include ('connection.php');
    session_start();

	if(isset($_GET['gID'])) {
		$groupID = $_GET['gID'];	
	} else {
		$groupID = "1";
    }
    
    /*
        Reference: http://talkerscode.com/webtricks/upload-image-from-url-using-php.php
    */
    if(isset($_POST['urlimg']))
    {
        $url=$_POST['img_url'];
        $data = file_get_contents($url);
        $uniquename = uniqid() . basename($url);
        $cleanname = basename($url);
        $dir = "uploads/";
        $path = $dir . $uniquename;
        file_put_contents($path, $data);


        $archivedQuery = "SELECT isArchived FROM groups WHERE group_id = $groupID";
        $archived = $conn->query($archivedQuery);
        if ($archived->num_rows > 0) {
            while ($row = $archived->fetch_assoc()) {
                $resultArchived = $row['isArchived'];
            }
        }
        if ($resultArchived == 0) {
            $isinQuery = "SELECT * FROM group_users a WHERE a.user_id = $userID AND a.group_id = $groupID";
            $isinResult = $conn->query($isinQuery);
            if ($isinResult->num_rows > 0) {
                $query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`, `likes`, `dislikes`, `parent_id`, `hasChildren`, `image`) VALUES (NULL, '" . $userID . "', 'test', CURRENT_TIMESTAMP, '" . $groupID . "',0,0,0,0,'".$uniquename."');";
                $conn->query($query); 
                $conn->close();
                header("Location: home.php"); 
            } else {
            }
        }
    }
?>