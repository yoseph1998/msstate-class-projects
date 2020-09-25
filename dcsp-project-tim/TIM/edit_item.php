<?php
	require_once "php/login_functions.php";
	if(!is_logged_in()) { // Check if a user is logged in
		header('Location: index.php');
		exit; // Redirect to the homepage.
	}
	if(isset($_GET['id'])) {
		$user = get_user();
		$permissions = $user->get_permissions();
		if($permissions > 2) {
			header("Location: index.php");
			exit();
		}
		$id = $_GET['id'];
		if(isset($_POST['delete'])) {
			//GETTING ITEM INFO
			$conn = database_connect();
			$query = "SELECT * FROM items WHERE id=$id";
			$results = $conn->query($query);
			if(!$results) {
				$err_message = "<text style=\"color: red;\">Invalid Item ID</text>";
			} else {
				$row = $results->fetch_assoc();
				$number = $row['item_number'];
				$name = $row['name'];
				$price = $row['price'];
				$quantity = $row['quantity'];
				$department = $row['department'];
				$store = $row['store'];
				$description = $row['description'];
				
				//ARCHIVING ITEM
				$query = "INSERT INTO archives (id, item_number, name, price, quantity, department, store, description)
					VALUES ($id, $number, '$name', $price, $quantity, '$department', '$store', '$description')";
				$results = $conn->query($query);
				if(!$results)
					$err_message = "<text style=\"color: red;\">Could Not Archive Item</text>";
				// DELETING ITEM
				$query = "DELETE FROM items WHERE id=$id";
				$results = $conn->query($query);
				if(!$results)
					$err_message = "<text style=\"color: red;\">Could Delete Item</text>";
				else
					$err_message = "<text style=\"color: blue;\">Item Deleted</text>";
			}
		} else if (isset($_POST['edit'])){ // EDIT ITEM
			///GET POSTED DATA
			$number = $_POST['number'];
			$name = $_POST['name'];
			$price = $_POST['price'];
			$quantity = $_POST['quantity'];
			$department = $_POST['department'];
			$store = $_POST['store'];
			$description = $_POST['description'];
			
			
			unset($store_none);
			if($store == "Starkville")
				$store_starkville = "selected";
			else if($store == "Atlanta")
				$store_atlanta = "selected";
			else if($store == "Memphis")
				$store_memphis = "selected";
			else
				$store_none = "selected";
			
			unset($dept_none);
			if($department == "Sports")
				$dept_sports = "selected";
			else if($department == "Food")
				$dept_food = "selected";
			else if($department == "Electronics")
				$dept_electronics = "selected";
			else if($department == "Misc")
				$dept_misc = "selected";
			else if($department == "Pharmacy")
				$dept_pharmacy = "selected";
			else
				$store_none = "selected";
			
			// EDIT ITEM
			$conn = database_connect();
			$query = "UPDATE items SET item_number=$number, name='$name', price=$price, quantity=$quantity, 
					department='$department', store='$store', description='$description' WHERE id=$id";
			$results = $conn->query($query);
			if(!$results) {
				$err_message = "<text style=\"color: red;\">Invalid Input</text>";
			} else
				$err_message = "<text style=\"color: blue;\">Item Modified</text>";
		} else { //DISPLAY ITEM INFO
			// GET ITEM INFO
			$conn = database_connect();
			$query = "SELECT * FROM items WHERE id=$id";
			$results = $conn->query($query);
			if(!$results) {
				$err_message = "<text style=\"color: red;\">Invalid Item ID</text>";
			} else {
				//STORE ITEM INFO
				$row = $results->fetch_assoc();
				if(!$row)
					$err_message = "<text style=\"color: red;\">No Item Width ID of $id</text>";
				$number = $row['item_number'];
				$name = $row['name'];
				$price = $row['price'];
				$quantity = $row['quantity'];
				$department = $row['department'];
				$store = $row['store'];
				$description = $row['description'];
				
				unset($store_none);
				if($store == "Starkville")
					$store_starkville = "selected";
				else if($store == "Atlanta")
					$store_atlanta = "selected";
				else if($store == "Memphis")
					$store_memphis = "selected";
				else
					$store_none = "selected";
				
				unset($dept_none);
				if($department == "Sports")
					$dept_sports = "selected";
				else if($department == "Food")
					$dept_food = "selected";
				else if($department == "Electronics")
					$dept_electronics = "selected";
				else if($department == "Misc")
					$dept_misc = "selected";
				else if($department == "Pharmacy")
					$dept_pharmacy = "selected";
				else
					$store_none = "selected";
			}
		}
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>TIM - Add Item</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="icon" href="imgs/favicon.ico">
		<!-- CSS style files -->
		<!-- Load base stylesheet -->
		<link rel="stylesheet" type="text/css" href="css/ui.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	<body background="imgs/background.jpg">
		<header style="margin-bottom: 20px;">
			<div class="topnav">
				<a href="index.php" class="active"><i class="fa fa-home"></i></a>
				<a href="my_account.php"><i class="fa fa-users"></i></a>
				<a href="logout.php" style="float: right;"><i class="fa fa-sign-out"></i></a>
			</div>
		</header>
		<!-- PAGE TITLE -->
		<div class="adduser-div" id="adduser-div" name="adduser-div" style="margin-bottom: 10px; background-color: white; border: 1px solid #000; padding: 12px 20px">
			<text style="font-family: Arial; font-weight: bold; font-size: 2em;">Edit Item</text>
		</div>
		
		<!-- EDIT ITEM FORM -->
		<main>
		<div class="additem-div" id="additem-div" name="additem-div" style="background-color: white; border: 1px solid #000; padding: 12px 20px">
			<p><?php if(isset($err_message)) echo $err_message; ?></p>
			<form class="additem-form" method="post" id="additem-form" name="additem-form"> <!--Insert php file that performs login validation -->
				<div>
					<table>
					
						<tr>
							<td style="width: 20%"><label for="number" style="font-family: Arial, Helvetica, sans-serif;"><b>Item Number</b></label></td>
							<td><label for="name" style="font-family: Arial, Helvetica, sans-serif;"><b>Item Name</b></label></td>
						</tr>
						<tr>
							<td><input type="text" placeholder="Enter Number" name="number" value="<?php if(isset($number)) echo $number; ?>" required></td>
							<td><input type="text" placeholder="Enter Name" name="name" value="<?php if(isset($name)) echo $name; ?>" required></td>
						</tr>
					</table>
					
					<table>
						<tr>
							<td ><label for="price" style="font-family: Arial, Helvetica, sans-serif;"><b>Item Price</b></label></td>
							<td><label for="quantity" style="font-family: Arial, Helvetica, sans-serif;"><b>Item Quantity</b></label></td>
							<td><label for="department" style="font-family: Arial, Helvetica, sans-serif;"><b>Select Department</b></label></td>
							<td><label for="store" style="font-family: Arial, Helvetica, sans-serif;"><b>Select Store</b></label></td>
							
						</tr>
						<tr>
							<td><input type="text" placeholder="Enter Price" name="price" value="<?php if(isset($price))echo $price; ?>" required></td>
							<td><input type="text" placeholder="Enter Quantity" name="quantity" value="<?php if(isset($quantity)) echo $quantity; ?>" required></td>
							<td> 
								<select name="department" required>
									<option value="" <?php if(isset($dept_none)) echo $dept_none; ?>>Select Dept.</option>
									<option value="Misc" <?php if(isset($dept_misc)) echo $dept_misc; ?>>Misc</option>
									<option value="Sports" <?php if(isset($dept_sports)) echo $dept_sports; ?>>Sports</option>
									<option value="Food" <?php if(isset($dept_food)) echo $dept_food; ?>>Food</option>
									<option value="Pharmacy" <?php if(isset($dept_pharmacy)) echo $dept_pharmacy; ?>>Pharmacy</option>
									<option value="Electronics" <?php if(isset($dept_electronics)) echo $dept_electronics; ?>>Electronics</option>
								</select>
							</td>
							<td>
								<select name="store" required>
									<option value="" <?php if(isset($store_none)) echo $store_none; ?>>Select Store</option>
									<option value="Starkville" <?php if(isset($store_starkville)) echo $store_starkville; ?>>Starkville</option>
									<option value="Atlanta" <?php if(isset($store_atlanta)) echo $store_atlanta; ?>>Atlanta</option>
									<option value="Memphis" <?php if(isset($store_memphis)) echo $store_memphis; ?>>Memphis</option>
								</select>
							</td>
							
						</tr>
					</table>
					
					<table>
						<tr>
							<td><label for="description" style="font-family: Arial, Helvetica, sans-serif;"><b>Item Description</b></label></td>
						</tr>
						<tr>
							<td><textarea name="description" form="additem-form"><?php if(isset($description)) echo $description; ?></textarea></td>
						</tr>
					<table>
					
					<button style="width: 25%; float: left;" type="submit" name="delete" style="font-family: Arial, Helvetica, sans-serif;">Delete</button>
					<button style="width: 25%; float: right;" type="submit" name="edit" style="font-family: Arial, Helvetica, sans-serif;">Edit</button>
					
				</div>
			</form>
		</div>
		</main>
	</body>
</html>