<?php
	session_start();
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
			<div id="wrapper">
				<div id="logo">
					<t> :Social Media:</t>
				</div>
				<div id="menu">
					<a href="loggedout.php" />Log Out</a>
				</div>
			</div>
		</div>	
		
		<?php
			echo "<h1 class= " . "notfound" . ">Congratulations. You logged in!</h1><br /> <br />";
		?>
		<table>
			<tr>
				<td>
					<form action="#" method="POST">
						<input type="text" name="post" size="25" placeholder="Post Your Status..." /> <br />
						<input type="submit" name="submit" value="Post"/>
					</form>
				</td>
			</tr>		
		</table>
		<div class="footer">
				<p>":Social Media:": A CS418 Project</p><br/>
				<p>Created by: Andrew Mayes and James Lopez</p>
		</div>
	</body>
</html>

