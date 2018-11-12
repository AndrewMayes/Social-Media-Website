<?php

	include ('connection.php');
    session_start();
    
    if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
    }
    


				$postFeed = "SELECT * from users inner join messages on users.id = messages.user_id ORDER BY msg_id DESC";
				$result = $conn->query($postFeed);
				if ($result->num_rows > 0) { 
					// output data of each row
					while($row = $result->fetch_assoc()) {
						
						$messages[] = $row;
						/*
						if($row['img'] == '') {
							echo "<span>"."<img id ='chat_avatar' width='50' height='50' src='uploads/profiledefault.png' alt='Default Profile Pic'>" . "<h2 id ='userName'>" . $row['username'] . ": " . htmlspecialchars($row['msg'])."</h2>" . "<div class='time'>" . $row['post_time'] . "</div>"."</span>";
							echo "<div class='reply_pos'><form action='home.php?id=" . $groupID . "' method='POST'>
							<input id='reply' type='text' name='reply' value='' placeholder='Post Your Reply...'>
							<input id='reply_submit' type='submit' name='reply_submit' value='Reply!'>
							</form></div>";
						} else {
							echo "<span>"."<img id ='chat_avatar' width='50' height='50' src='uploads/".$row['img']."' alt='Profile Pic'>" . "<h2 id ='userName'>" . $row['username'] . ": " . htmlspecialchars($row['msg'])."</h2>" . "<div class='time'>" . $row['post_time'] . "</div>"."</span>";
							echo "<div class='reply_pos'><form action='home.php?id=" . $groupID . "' method='POST'>
							<input id='reply' type='text' name='reply' value='' placeholder='Post Your Reply...'>
							<input id='reply_submit' type='submit' name='reply_submit' value='Reply!'>
							</form></div>";
						}
						
						echo "<form action='home.php?id=" . $groupID . "&liked=" . $row['msg_id'] . "' method='POST'>
						<div class='likeys'><input id='like_input'type='submit' name='like' value='Like'>"." ".$row['likes']." likes</div>
						</form>";
						echo "<form action='home.php?id=" . $groupID . "&disliked=" . $row['msg_id'] . "' method='POST'>
						<div class='dislikeys'><input id='dislike_input'type='submit' name='dislike' value='Dislike'>"." ".$row['dislikes']." dislikes</div>
						</form>";
						echo "<div class='underline'>";
						echo "</div>";*/
					} 
				} else {
                    echo "<h2 id ='userName'>No messages in this channel yet. Come back soon!</h2>"; 
				}
				
				foreach ($messages as $key => $msg) {
					$messages[$key]['msg'] = htmlspecialchars($messages[$key]['msg']);
				}

				$jsonMessages = json_encode($messages);
                echo $jsonMessages;

			?>