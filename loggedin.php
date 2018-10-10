<?php
	include ('connection.php');

	session_start();
	
	if(!isset($_SESSION['email'])){
		header("Location: index.php?msg=" . urlencode('needs_to_log_in'));
	}
?>

<!doctype HTML>
<html>
	<head>
		<title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=Exo+2" rel="stylesheet">
	</head>
	<body>
		<div class="header">
				<?php 
					echo "<div id='logo'>";
							echo "<t>";
								echo $_SESSION['fname'];
								echo " " . $_SESSION['lname'];
							echo "</t>";
					echo "</div>"  ;
				?>
				<div id="menu">
					<a href="loggedout.php" />Log Out</a>
				</div>
		</div>	
		<div class= "sidenav">
				<a href="#">Account</a>
				<button class="dropdown1">Group</button>
					<div class="dropdown_menu1">
						<a href="#">Global</a>
						<a href="#">League of Legends</a>
						<a href="#">Dota 2</a>
						<a href="#">CS:GO</a>
					</div>
				
		</div>
		<div class="posting">
			<form action="loggedin.php" method="POST">
					<input id="messeging" type="text" name="message" value="" placeholder="Post Your Status...">
					<input id="msg_submit" type="submit" name="submit" value="Post!">
			</form> 
		</div>
		
		<div class="footer">
				<p>":Social Media:": A CS418 Project</p><br/>
				<p>Created by: Andrew Mayes and James Lopez</p>
		</div>
		
		<script>
			var dropdown = document.getElementsByClassName("dropdown1");
			var i;

			for (i = 0; i < dropdown.length; i++) {
			  dropdown[i].addEventListener("click", function() {
				this.classList.toggle("dd_active");
				var dropdownContent = this.nextElementSibling;
				if (dropdownContent.style.display === "block") {
				  dropdownContent.style.display = "none";
				} else {
				  dropdownContent.style.display = "block";
				}
			  });
			}
		</script>
	</body>
</html>

