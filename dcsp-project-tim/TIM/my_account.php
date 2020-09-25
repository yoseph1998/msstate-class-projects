<?php
	require_once 'php/login_functions.php';
	require_once 'php/usersClass.php';
	
	if (session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
	
	if(is_logged_in()) {
		$user = get_user();
		$username = $user->get_username();
		$passhash = $user->get_password();
		$user_id = $user->get_userID();
		$permissions = $user->get_permissions();
	} else {
		header("Location: index.php");
		exit();
	}
	
	if(isset($_POST['username'])) { //CHANGE Username
		$_POST['username'] = strtolower($_POST['username']);
		
		$conn = database_connect();
		$query = "SELECT * FROM users WHERE username = '".$_POST['username']."'";
		$result = $conn->query($query);
		$row = $result->fetch_assoc();
		if(!validate_user($_SESSION['username'], $_POST['password'])) {
			$username_message = "<p style=\"color: red;\">Invalid Credentials</p>";
		} else if($row) {
			$username_message = "<p style=\"color: red;\">Username is taken</p>";
		} else {
		
			$user = new Users($user_id, $_POST['username'], $passhash, $permissions);
			$result = $user->update_table();
			$username = $user->get_username();
			$_SESSION['username'] = $username;
			$username_message = "<p style=\"color: blue;\">Username Changed</p>";
			unset($_POST['username']);
		}
	} else if(isset($_POST['new_password'])) { // CHANGE PASSWORD
		if(!validate_user($username, $_POST['current_password'])) {
			$password_message = "<p style=\"color: red;\">Invalid Credentials</p>";
		} else if($_POST['new_password'] != $_POST['confirm_password']) {
			$password_message = "<p style=\"color: red;\">Password's don't match</p>";
		} else {
			echo "3";
			$passhash = hash_password($_POST['new_password']);
			
			$user = new Users($user_id, $username, $passhash, $permissions);
			$result = $user->update_table();
			$password_message = "<p style=\"color: blue;\">Password Changed</p>";
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>TIM - Manage Users</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="icon" href="imgs/favicon.ico">
		<!-- CSS style files -->
		<!-- Load base stylesheet -->
		<link rel="stylesheet" type="text/css" href="css/ui.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	
	<body background="imgs/background.jpg">
		<div class="wrapper">
			<div class="body">
				<header style="margin-bottom: 20px;">
					<div class="topnav">
						<a href="index.php" class="active"><i class="fa fa-home"></i></a>
						<a href="my_account.php"><i class="fa fa-users"></i></a>
						<a href="logout.php" style="float: right;"><i class="fa fa-sign-out"></i></a>
					</div>
				</header>
				
				<!-- PAGE TITLE -->
				<div class="adduser-div" id="adduser-div" name="adduser-div" style="margin-bottom: 10px; background-color: white; border: 1px solid #000; padding: 12px 20px;">
					<text style="font-family: Arial; font-weight: bold; font-size: 2em;">Welcome <?php echo $username; ?> To Your Account</text>
				</div>
				
				<!-- MANAGE PERSONAL ACCOUNT -->
				<div class="adduser-div" style="background: white; border: 1px solid; padding: 12px 20px;">
					<?php
						if($permissions <= 2)
							echo '
								<form action="add_item.php">
									<button type="submit" style="float: left; width: 100px; font-family: Arial, Helvetica, sans-serif;">Add Item</button>
								</form>
							';
						if($permissions <= 1)
							echo '
								<form action="manage_users.php">
									<button type="submit" style="float: right; width: 200px; font-family: Arial, Helvetica, sans-serif;">Manage Users</button>
								</form>
							';
						
					?>					
					<br><br><br>
					
					<h2> Settings:</h2>
					<h2> 1. Change Username </h2>
					<?php 
						if(isset($username_message)) 
							echo "$username_message"; 
						unset($username_message);
					?>
					<form class="login-form" method="post"> <!--Insert php file that performs login validation -->
						<div>
							<label for="password" style="font-family: Arial, Helvetica, sans-serif;"><b>Password</b></label>
							<input type="password" placeholder="Enter Password" name="password" required><br>
							
							<label for="username" style="font-family: Arial, Helvetica, sans-serif;"><b>New Username:</b></label>
							<input type="text" placeholder="Enter Username" name="username" value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>" required><br>

							<button type="submit" style="font-family: Arial, Helvetica, sans-serif;">Change</button>
						</div>
					</form>
					
					<h2> 2. Change Password </h2>
					<?php 
						if(isset($password_message)) 
							echo "$password_message"; 
						unset($password_message);
					?>
					<form class="login-form" method="post"> <!--Insert php file that performs login validation -->
						<div>
							<label for="current_password" style="font-family: Arial, Helvetica, sans-serif;"><b>Current Password</b></label>
							<input type="password" placeholder="Enter Current Password" name="current_password" value="" required><br>
							
							<label for="new_password" style="font-family: Arial, Helvetica, sans-serif;"><b>New Password</b></label>
							<input type="password" placeholder="Enter New Password" name="new_password" required><br>
							
							<label for="confirm_password" style="font-family: Arial, Helvetica, sans-serif;"><b>Confirm Password</b></label>
							<input type="password" placeholder="Confirm Password" name="confirm_password" required><br>
							
							<button type="submit" style="font-family: Arial, Helvetica, sans-serif;">Change</button>
						</div>
					</form>
				</div>
			</div>
		
		</div>
		
	</body>
</html>
