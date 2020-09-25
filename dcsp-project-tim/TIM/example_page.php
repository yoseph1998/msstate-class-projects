<?php
	require "php/login_functions.php";
	
	if(is_logged_in()) { // Check if a user is logged in
		get_session($_SESSION['username']);
		$username = $_SESSION['username'];
		$permissions = $_SESSION['permissions'];
	} else {
		$username = null;
		$password = null;
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>TIM - Example Page</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="icon" href="imgs/favicon.ico">
	</head>
	<body>
		<?php
			if($username == null) {
				echo "<p>no user is logged in.</p><br><a href=\"/login.php\">Login</a>";
			} else {
				echo "<p>$username is logged in! <br> He is a";
				switch($permissions) {
					case 1: echo "n admin!"; break;
					case 2: echo " moderator!"; break;
					case 3: echo " user."; break;
				}
				echo "</p><br><a href=\"/logout.php\">Logout</a>";
			}
			
			
		?>
		
	</body>
</html>