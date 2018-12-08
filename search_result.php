<?php
	include ('connection.php');

	$output = '';
	if(isset($_POST["query"])) {
		$search = mysqli_real_escape_string($conn, $_POST["query"]);
		$query = "
		SELECT * FROM users WHERE username LIKE '%".$search."%' OR fname LIKE '%".$search."%' OR lname LIKE '%".$search."%' OR email LIKE '%".$search."%'";
	}

	$result = mysqli_query($conn, $query);
	if(mysqli_num_rows($result) > 0) {
		
		while($row = mysqli_fetch_array($result)) {
			$userID = $row['id']; 

			if($row['img'] == ''){
				$output .= '
				
					<a class="search_user_a" href= profile.php?id='. $userID . '>
						<div class="search_users">
							<span><img id="search_avatar"width="50" height="50" src="https://www.gravatar.com/avatar/'.md5($row['email']).'?d=retro" alt= "Profile Pic">' .$row["fname"].' '.$row["lname"].' ('.$row["username"].')</span>
						</div>
					</a>			
				
					<br />
				';
			}

			else {
				$output .= '
				
					<a class="search_user_a" href= profile.php?id='. $userID . '>
						<div class="search_users">
							<span><img id="search_avatar"width="50" height="50" src="uploads/'.$row['img'].'" alt= "Profile Pic">'.$row["fname"].' '.$row["lname"].' ('.$row["username"].')</span>
						</div>
					</a>		
					
					<br />
				';
			}


		}

			echo $output;
	}
?>


