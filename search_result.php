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
		$output .= '
				<span>'.$row["fname"].' '.$row["lname"].' | '.$row["username"].' | '.$row["email"].'</span>
			<br />
		';
    }
	echo $output;
}
?>