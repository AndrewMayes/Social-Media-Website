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
    Reference: https://www.w3schools.com/php/php_file_upload.asp
    */
    $target_dir = "uploads/";
    $filename = uniqid() . basename($_FILES["fileToUpload"]["name"]);
    $cleanname = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" && $imageFileType != "txt" && $imageFileType != "pdf" && $imageFileType != "docx" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
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
                    if ($imageFileType == 'png' || $imageFileType == 'jpg' || $imageFileType == 'jpeg' || $imageFileType == 'gif') {
                        //$message = mysqli_real_escape_string($conn, $_POST['msg']);
                        $query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`, `likes`, `dislikes`, `parent_id`, `hasChildren`, `image`) VALUES (NULL, '" . $userID . "', 'test', CURRENT_TIMESTAMP, '" . $groupID . "',0,0,0,0,'".$filename."');";
                        $conn->query($query); 
                        $conn->close();
                        header("Location: home.php");
                    } else if ($imageFileType == 'txt' || $imageFileType == 'pdf' || $imageFileType == 'docx') {
                        $query = "INSERT INTO `messages` (`msg_id`, `user_id`, `msg`, `post_time`, `group_id`, `likes`, `dislikes`, `parent_id`, `hasChildren`, `file`, `cleanName`) VALUES (NULL, '" . $userID . "', 'test', CURRENT_TIMESTAMP, '" . $groupID . "',0,0,0,0,'".$filename."','".$cleanname."');";
                        $conn->query($query); 
                        $conn->close();
                        header("Location: home.php");
                    }

                } else {
                }
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
?>