<?php
/* 
	References: https://stackoverflow.com/questions/44818357/how-to-make-logout-button-properly-work-in-html
*/

	session_start();
	session_destroy();
	header("location: index.php");
?>