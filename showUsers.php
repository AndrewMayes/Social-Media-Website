<?php
/*
References: https://github.com/jbrunelle/ODUCS418F18/blob/master/examples/testmysql.php
*/

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

$sql = "SELECT * FROM users;";
$result = $conn->query($sql);

echo "<html><body>\n";

if ($result->num_rows > 0) {
    echo "<table padding=2 border=1>\n";
    echo "<tr><th>ID<th>fname<th>lname<th>password<th>email\n";
   
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"] . "<td>" . $row["fname"] . "<td>" . $row["lname"] . "<td>" . $row["password"] . "<td>" . $row["email"] . "\n";
    }
} else {
    echo "0 results";
}


$conn->close();
echo "</body></html>";









?>