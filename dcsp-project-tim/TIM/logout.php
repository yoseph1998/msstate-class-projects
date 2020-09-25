<?php
	require_once "php/login_functions.php";
	if(!is_logged_in()) { //End session if user is already logged in.
		header('Location: login.php'); //Redirect to login page
		exit;
	} else {
		end_session();
		header('Location: login.php'); //Redirect to login page
		exit;
	}
	
?>
