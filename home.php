<?php
/*
References: https://github.com/jbrunelle/ODUCS418F18/blob/master/examples/testmysql.php
*/
?>

<!doctype HTML>
<html>
	<head>
        <title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css?">
		<link href="https://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet">
	</head>
	<body>
		<div class="header">
			<div id="wrapper">
				<div id="logo">
					<t> :Social Media:</t>
				</div>
				<div id="menu">
                    <a href="signup.php" />Sign Up</a>
					<a href="index.php" />Log In</a>
				</div>
			</div>
		</div>	



		<div class="footer">
			<p>":Social Media:": A CS418 Project</p><br/>
			<p>Created by: Andrew Mayes and James Lopez</p>
		</div>
	</body>
</html>

<?php

    $servername = "localhost";
    $username = "admin";
    $password = "monarchs";
    $dbname = "cs418";

    // Create connection
    $conn = new mysqli($servername,$username,$password,$dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $group1 = "SELECT * FROM messages WHERE group_id = 1";
    $g1result = $conn->query($group1);

    //echo $query;
    echo "<h1> Global Group";

    if ($g1result->num_rows > 0) { 
        // output data of each row
        while($row = $g1result->fetch_assoc()) {
            //echo "<h1> Global Group";
            echo "<h2> User " . $row['user_id'] . ": " . $row['msg'] . " Posted at: " . $row['post_time'] . "</h2>" . "\n";
            
        } 
    } else {
        echo "<h2>No messages in this channel yet. Come back soon!</h2>";
    }

    $group1 = "SELECT * FROM messages WHERE group_id = 2";
    $g1result = $conn->query($group1);

    //echo $query;
    echo "<h1> Gaming Group";

    if ($g1result->num_rows > 0) { 
        // output data of each row
        while($row = $g1result->fetch_assoc()) {
            //echo "<h1> Global Group";
            echo "<h2> User " . $row['user_id'] . ": " . $row['msg'] . " Posted at: " . $row['post_time'] . "</h2>" . "\n";
            
        } 
    } else {
        echo "<h2>No messages in this channel yet. Come back soon!</h2>";
    }

    $group1 = "SELECT * FROM messages WHERE group_id = 3";
    $g1result = $conn->query($group1);

    //echo $query;
    echo "<h1> Sports Group";

    if ($g1result->num_rows > 0) { 
        // output data of each row
        while($row = $g1result->fetch_assoc()) {
            //echo "<h1> Global Group";
            echo "<h2> User " . $row['user_id'] . ": " . $row['msg'] . " Posted at: " . $row['post_time'] . "</h2>" . "\n";
            
        } 
    } else {
        echo "<h2>No messages in this channel yet. Come back soon!</h2>";
    }

    $group1 = "SELECT * FROM messages WHERE group_id = 4";
    $g1result = $conn->query($group1);

    //echo $query;
    echo "<h1> Movies Group";

    if ($g1result->num_rows > 0) { 
        // output data of each row
        while($row = $g1result->fetch_assoc()) {
            //echo "<h1> Global Group";
            echo "<h2> User " . $row['user_id'] . ": " . $row['msg'] . " Posted at: " . $row['post_time'] . "</h2>" . "\n";
            
        } 
    } else {
        echo "<h2>No messages in this channel yet. Come back soon!</h2>";
    }




?>