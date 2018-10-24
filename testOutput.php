<?php
include ('connection.php');

$query = "SELECT * FROM messages";
$result = $conn->query($query);

echo $query . "<br>";

echo "<html><body>\n";

if ($result->num_rows > 0) {
    echo "<table padding=2 border=1>\n";
    echo "<tr><th>ID<th>fname<th>lname<th>password<th>email\n";
   
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["msg_id"] . "<td>" . $row["user_id"] . "<td>" . $row["msg"] . "<td>" . $row["post_time"] . "<td>" . $row["group_id"] . "<td>" . $row["likes"] . "\n";
    }
} else {
    echo "0 results";
}


$conn->close();
echo "</body></html>";
?>
