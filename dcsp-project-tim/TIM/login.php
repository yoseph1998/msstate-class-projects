<?php
	$login_error = "";
	require_once "php/login_functions.php";
	
	if(is_logged_in()) { // Check if a user is logged in
		header('Location: index.php');
		exit; // Redirect to the homepage.
	} else if (isset($_POST['username'])){ // Check if login form has been submitted
		if(validate_user($_POST['username'], $_POST['password'])) { // Validate login
			$session = get_session($_POST['username']); // Login and create session
			header('Location: index.php');
			exit; // Redirect to the homepage.
		} else {
			$login_error = "Invalid credentials";
		}
	}
	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>TIM - Login</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="icon" href="imgs/favicon.ico">
		<!-- CSS style files -->
		<!-- Load base stylesheet -->
		<link rel="stylesheet" type="text/css" href="css/ui.css">
	</head>
	<body background="imgs/background.jpg">
		<header>
		</header>
		<main>
		<div class="login-div" style="background-color: white; border: 1px solid #000; padding: 12px 20px">
			<h1 style="font-family: bellBottom; font-size: 2em; font-style: italic; text-indent: 1em">Top Inventory Management</h1>
			<p style="font-family: bellBottom; font-size: 1em; font-style: italic; text-indent: 7.75em">Bell Bottoms, LLC</p>
			<p><?php echo $login_error ?></p>
			<form class="login-form" method="post"> <!--Insert php file that performs login validation -->
				<div>
					<label for="username" style="font-family: Arial, Helvetica, sans-serif;"><b>Username</b></label>
					<input type="text" placeholder="Enter Username" name="username" required>

					<label for="password" style="font-family: Arial, Helvetica, sans-serif;"><b>Password</b></label>
					<input type="password" placeholder="Enter Password" name="password" required>

					<button type="submit" style="font-family: Arial, Helvetica, sans-serif;">Login</button>
				</div>
			</form>
		</div>
		</main>
	</body>
</html>