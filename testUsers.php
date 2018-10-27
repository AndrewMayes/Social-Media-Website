<?php
include ('connection.php');

$query = "SELECT * FROM users";
$result = $conn->query($query);

echo $query . "<br>";

echo "<html><body>\n";

if ($result->num_rows > 0) {
    echo "<table padding=2 border=1>\n";
    echo "<tr><th>ID<th>fname<th>lname<th>password<th>email<th>username<th>img\n";
   
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["id"] . "<td>" . $row["fname"] . "<td>" . $row["lname"] . "<td>" . $row["password"] . "<td>" . $row["email"] . "<td>" . $row["username"] . "<td>" . $row['img'] . "\n";
    }
} else {
    echo "0 results";
}


$conn->close();
echo "</body></html>";
?>