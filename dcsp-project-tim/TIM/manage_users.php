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
	
	if($permissions != 1) {
		header("Location: index.php");
		exit();
	}
	
	if(isset($_POST['add_user'])) {
		
		$_POST['username'] = strtolower($_POST['username']);
		
		$conn = database_connect();
		$query = "SELECT * FROM users WHERE username = '".$_POST['username']."'";
		$result = $conn->query($query);
		$row = $result->fetch_assoc();
		if($row) {
			$au_message = "<p style=\"color: red;\">Username is taken</p>";
			goto end_if;
		}
		$conn->close();
		
		if($_POST['password'] != $_POST['confirm_password']) {
			$au_message = "<p style=\"color: red;\">Passwords do not match</p>";
			goto end_if;
		}
		
		$uname = $_POST['username'];
		$pword = hash_password($_POST['password']);
		$perm = $_POST['permissions'];
		
		$user = new Users(-1, $uname, $pword, $perm);
		$result = $user->add_self_to_database();
		if(!$result)
			$au_message = "<p style=\"color: red;\">Could Not Add User</p>";
		else
			$au_message = "<p style=\"color: blue;\">User Added</p>";
		
	} else if(isset($_POST['lookup_form'])) {
		$conn = database_connect();
		
		$query = "SELECT * FROM users";
		if($_POST['username'] != "" and $_POST['permissions'])
			$query = "SELECT * FROM users WHERE username = '".$_POST['username']."' AND permissions = '".$_POST['permissions']."'";
		else if($_POST['username'] != "")
			$query = "SELECT * FROM users WHERE username = '".$_POST['username']."'";
		else if($_POST['permissions'])
			$query = "SELECT * FROM users WHERE permissions = '".$_POST['permissions']."'";
		
		$lookup_result = $conn->query($query);
		goto end_if;
		
	} else if(isset($_POST['edit_user_edit']))  {
		$pword = get_user($_POST['user_id'])->get_password();
		$uid = $_POST['user_id'];
		$uname = $_POST['username'];
		if($_POST['password'] != "")
			$pword = hash_password($_POST['password']);
		$perm = $_POST['permissions'];
		
		$user = new Users($uid, $uname, $pword, $perm);
		$result = $user->update_table();
		if(!$result)
			$eu_message = '<p style="color: red;"> Couldn\'t Edit User</p>';
		else
			$eu_message = '<p style="color: blue;"> User Updated </p>';
		
		$_POST['edit_user'] = $_POST['user_id'];
		
	} else if(isset($_POST['edit_user_delete']))  {
		$pword = get_user($_POST['user_id'])->get_password();
		$uid = $_POST['user_id'];
		$uname = $_POST['username'];
		if($_POST['password'] != "")
			$pword = hash_password($_POST['password']);
		$perm = $_POST['permissions'];
		
		$user = new Users($uid, $uname, $pword, $perm);
		$result = $user->delete_from_table();
		$_POST['edit_user'] = $_POST['user_id'];
		
		if(!$result)
			$eu_message = '<p style="color: red;"> Couldn\'t Delete User</p>';
		else {
			$eu_message = '<p style="color: blue;"> User Deleted! </p>';
			unset($_POST['edit_user']);
		}
		
	}
	end_if:
	
	if(isset($_POST['edit_user'])) {
		$conn = database_connect();
		$query = "SELECT * FROM users WHERE user_id = '".$_POST['edit_user']."'";
		$result = $conn->query($query);
		$row = $result->fetch_assoc();
		$edit_user = new Users($row['user_id'], $row['username'], $row['password_hash'], $row['permissions']);
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
				<div class="adduser-div" id="adduser-div" name="adduser-div" style="margin-bottom: 10px; background-color: white; border: 1px solid #000; padding: 12px 20px">
					<text style="font-family: Arial; font-weight: bold; font-size: 2em;">Manage Users</text>
				</div>
				
				<!-- ADD USER HTML -->
				<div class="adduser-div" id="adduser-div" name="adduser-div" style="background-color: white; border: 1px solid #000; padding: 12px 20px">
					<h1 style="font-family: arial; font-size: 2em; font-style: italic;">Create User</h1>
					<p><?php if(isset($au_message)) echo $au_message; ?></p>
					<form class="adduser-form" method="post"> <!--Insert php file that performs login validation -->
						<div>						
							<label for="username" style="font-family: Arial, Helvetica, sans-serif;"><b>Username:</b></label>
							<input type="text" placeholder="Enter Username" name="username" required><br>

							<label for="password" style="font-family: Arial, Helvetica, sans-serif;"><b>Password</b></label>
							<input type="password" placeholder="Enter Password" name="password" required><br>
							
							<label for="confirm_password" style="font-family: Arial, Helvetica, sans-serif;"><b>Confirm Password</b></label>
							<input type="password" placeholder="Re Enter Password" name="confirm_password" required><br>
							
							
							<label for="password" style="font-family: Arial, Helvetica, sans-serif;"><b>Account Type</b></label>
							<select name="permissions">
								<option value="3" select>User</option>
								<option value="2">Moderator</option>
								<option value="1">Admin</option>
							</select><br>
							
							<button type="submit" name="add_user" style="font-family: Arial, Helvetica, sans-serif;">Create User</button>
						</div>
					</form>
				</div><br/>
				<!-- LOOKUP USER HTML -->
				<div class="adduser-div" id="lookup-div" name="lookup-div" style="background-color: white; border: 1px solid #000; padding: 12px 20px">
					<h1 style="font-family: arial; font-size: 2em; font-style: italic; text-indent: 1em">Lookup Users</h1>
					<p><?php if(isset($lu_message)) echo $lu_message; ?></p>
					<form class="lookupusers-form" method="post"> <!--Insert php file that performs login validation -->
						<div>
							<label for="username" style="font-family: Arial, Helvetica, sans-serif;"><b>Username</b></label>
							<input type="text" placeholder="Username" name="username"><br>
							<select name="permissions">
								<option value="" selected>Select</option> 
								<option value="3">User</option>
								<option value="2">Moderator</option>
								<option value="1">Admin</option>
							</select><br>
							
							<button type="submit" name="lookup_form" style="font-family: Arial, Helvetica, sans-serif;">Find</button>
						</div>
					</form>
					
					<?php
						if(isset($lookup_result)) {
							$permissions = ["", "Admin", "Moderator", "User"];
							$row=$lookup_result->fetch_assoc();
							
							if(!$row) {
								echo "<p style=\"color: red\">No Results</p>";
								goto end_do;
							}
							echo '<form name="lookup-form" method="post">';
							echo '<table>';
							echo '<tr>';
							echo '	<th style="width: 30px;">User ID</th>';
							echo '	<th>Username</th>';
							echo '	<th>Permissions</th>';
							echo '</tr>';
							
							do {
								$num = $row['permissions'];
								$type = $permissions[$num];
								echo '<tr>';
								echo '	<td><button type="submit" name="edit_user" value="'.$row['user_id'].'">Edit</button></td>';
								echo '	<td>'.$row['username'].'</td>';
								echo '	<td>'.$type.'</td>';
								echo '</tr>';
							} while($row=$lookup_result->fetch_assoc());
							echo '</table>';
							echo '</form>';
							end_do:
						} else if(isset($edit_user)) {
							$admin = "";
							$mod = "";
							$user = "";
							
							if($edit_user->get_permissions() == 1)
								$admin = 'selected';
							else if($edit_user->get_permissions() == 2)
								$mod = 'selected';
							else if($edit_user->get_permissions() == 3)
								$user = 'selected';
							if(!isset($eu_message))
								$eu_message = "";
							echo '
								<form class="edit-form" method="post" style="border: 1px solid;width: 75%;">
									<h2 style="font-family: arial; font-style: italic; text-indent: 1em">Edit User</h2>
									'.$eu_message.'
									<table>
									<tr>
										<th><label for="user_id">User_ID:</label></th>
										<td><input type="text" name="user_id" value="'.$edit_user->get_userID().'" readonly></td>
									</tr>
									<tr>
										<th><label for="username">Username:</label></th>
										<td><input type="text" name="username" value="'.$edit_user->get_username().'" required></td>
									</tr>
									<tr>
										<th><label for="password">Password:</label></th>
										<td><input type="password" name="password" value=""></td>
									</tr>
									<tr>
										<th><label for="permissions">Acc. Type:</label></th>
										<td>
											<select name="permissions">
												<option value="3" '.$user.'>User</option>
												<option value="2" '.$mod.'>Moderator</option>
												<option value="1" '.$admin.'>Admin</option>
											</select>
										</td>
									</tr> <br>
									</table>
									<button style="width: 100px;" type="submit" name="edit_user_delete" style="font-family: Arial, Helvetica, sans-serif;">Delete</button>
									<button style="width: 100px; float: right;" type="submit" name="edit_user_edit" style="font-family: Arial, Helvetica, sans-serif;">Edit</button>
									
								</form>
							';
							end_edit_user_if:
						}
					?>
					
				</div>
			</div>
		
		</div>
		
	</body>
</html>
