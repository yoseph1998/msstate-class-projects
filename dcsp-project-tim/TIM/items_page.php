<?php
	require_once "php/login_functions.php";
	require_once "php/itemClass.php";

	/*Checking permissions to edit items*/
	if(is_logged_in()) {
		$user = get_user();
		$permissions=$user->get_permissions();
	} else {
		header("Location: index.php");
		exit();
	}
    
	/*Checking if id is set*/
	if(!isset($_GET['id']))
		goto item_not_set;

	$items=search_items($_GET['id']);
	if((sizeof($items)==0))
	{
		/*If item is in the database display this*/
		item_not_set:
		$itemName="NO SUCH ITEMS EXISTS";
		$itemNumber="";
		$itemDescription="";
		$itemPrice="";
		$quantity ="";
		$stores=array();
	}
	else
	{
		/*If Id is set then display the name, price, description, Number, all store its available at, and total quantity of all stores*/
		$item = $items[0];
		$itemId = $_GET['id'];
		$itemName=$item->get_itemName();
		$itemNumber=$item->get_itemNumber();
		$itemDescription=$item->get_itemDescription();
		$itemPrice=$item->get_itemPrice();
		$itemStore=search_items(null,$itemNumber);
		$quantity=0;
		$stores=array();
		$i=0;
		while ($i<sizeof($itemStore)){
			if($itemStore[$i]->get_itemQuantity() != 0)
				array_push($stores,$itemStore[$i]->get_itemStore());
			$quantity=$quantity+$itemStore[$i]->get_itemQuantity();
			$i=$i+1;
	}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>TIM - Item</title>
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link rel="icon" href="imgs/favicon.ico">
		<!-- CSS style files -->
		<!-- Load base stylesheet -->
		<link rel="stylesheet" type="text/css" href="css/ui.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!-- Javscript files -->
		<!-- Load application files -->
		<script type="text/javascript" src="js/main.js"></script>
		<style type="text/css">
		
		body {
			margin: 0;
			padding: 0;
			overflow: hidden;
			height: 100%; 
			max-height: 100%; 
			font-family:Sans-serif;
			line-height: 1.5em;
		}

		main {
			position: fixed;
			top: 50px;
			bottom: 50px;
			left: 230px; 
			right: 0;
			overflow: auto; 
			background:#ffff;
		}
		
		#footer {
			position: absolute;
			left: 0;
			bottom: 0;
			width: 100%;
			height: 50px; 
			overflow: hidden;
			background: #808080;
		}
				
		#nav {
			position: absolute; 
			top: 50px;
			bottom: 50px;
			left: 0; 
			width: 230px;
			overflow: hidden;
			background: #D3D3D3; 		
		}
		
		.innertube {
			margin: 15px;
		}
		
		p {
			color: black;
		}
		
		p1 {
			color: black;
			font-weight: bold;
			font-style: italic;
		}
				
		/*IE6 fix*/
		* html body{
			padding: 50px 0 50px 230px;
		}
		
		* html main{ 
			height: 100%; 
			width: 100%; 
		}
		
		</style>
	
	</head>
	
	<body>		
	
		<div class="topnav">
			<a href="index.php" class="active"><i class="fa fa-home"></i></a>
			<a href="my_account.php"><i class="fa fa-users"></i></a>
			<a href="logout.php" style="float: right;"><i class="fa fa-sign-out"></i></a>
	    </div>
		
		<main>
			<div class="innertube">
				
				<h1><?php echo "$itemName";?></h1>
				<p><?php echo"$itemDescription";?></p>
				
			</div>
		</main>

		<nav id="nav">
			<div class="innertube">
				<p1>Item Number: <?php echo "$itemNumber";?></p1>
				<p>Price: <?php echo "$"."$itemPrice";?></p>
				<p>Quantity: <?php echo "$quantity";?></p>
				<div class="additem-div">
					<?php
							if(isset($permissions) && $permissions <= 2 && isset($itemId))
								echo '
										<button onclick="window.location.href=\'edit_item.php?id='.$itemId.'\'" style="margin-left: 60px; width: 100px; font-family: Arial, Helvetica, sans-serif;">Edit Item</button>
								';
							
						?>
				</div>
			</div>
		</nav>	
		
		<footer id="footer">
			<div class="innertube">
				<p1>Available At: <?php for($i=0;$i<sizeof($stores);$i++){ echo "($stores[$i]) ";} ?> </p1>
			</div>
		</footer>
		
	</body>
</html>
