<?php
	require_once "php/login_functions.php";
	if(!is_logged_in()) {
		header("Location: login.php");
		exit();
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>TIM - Home</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="icon" href="imgs/favicon.ico">
		<!-- CSS style files -->
		<!-- Load base stylesheet -->
		<link rel="stylesheet" type="text/css" href="css/ui.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!-- Javscript files -->
		<!-- Load application files -->
		<script type="text/javascript" src="js/main.js"></script>
	</head>
	<body  background="imgs/background.jpg">
		<div class="topnav">
			<a href="index.php" class="active"><i class="fa fa-home"></i></a>
			<a href="my_account.php"><i class="fa fa-users"></i></a>
			<div class="dropdown">
				<button onclick="dropdownMenu()" class="dropbtn" id="locBtn">Location
					<i class="fa fa-caret-down"></i>
				</button>
				<div id="dropdownList" class="dropdown-content">
					<a href="#" class="locale" id="All" onclick="changeLocale();" selected>All</a>
					<a href="#" class="locale"  id="Starkville" onclick="changeLocale();">Starkville</a>
					<a href="#" class="locale" id="Atlanta" onclick="changeLocale();">Atlanta</a>
					<a href="#" class="locale" id="Memphis" onclick="changeLocale();">Memphis</a>
				</div>
			</div>
			<script>changeLocale();</script>
			<div class="search-container" style="margin: 0 auto; width: 50%; text-align: center; display: block;"">
				<input type="text" placeholder="Search.." name="search" id="searchfield">
				<button onclick="showSearch();"><i class="fa fa-search"></i></button>
			</div>
			
			<a href="logout.php" style="float: right;"><i class="fa fa-sign-out"></i></a>
		</div>
		<div id="output"></div>
	</body>
</html>