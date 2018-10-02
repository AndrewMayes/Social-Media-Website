<!doctype HTML>
<html>
	<head>
		<title>Social Media Prototype Testing</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow" rel="stylesheet">
	</head>
	<body>
		<div class="header">
			<div id="wrapper">
				<div id="logo">
					<t> :Social Media:</t>
				</div>
				<div id="menu">
					<a href="#">Sign Up</a>
					<a href="#">Log In</a>
				</div>
			</div>
		</div>	
		
		<?php
			echo "<h1 class= " . "notfound" . ">Congratulations. You logged in!</h1>";
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

	</body>
</html>

