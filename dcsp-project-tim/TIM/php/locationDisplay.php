<!DOCTYPE html>
<html>
<head>
	<style>
		table {
			width: 100%;
			border-collapse: collapse;
		}

		table, td, th {
			border: 1px solid black;
			padding: 5px;
		}

		th {
			background-color: #0080ff;
			color: white;
			text-align: left;}

		td {
			vertical-align: center;
		}

		tr {
			background-color: white;
			height: 50px;
		}

		tr:hover {background-color: #f5f5f5;}

	</style>
</head>
<body>

	<?php

	$q = intval($_GET['q']);
	//echo "$q";
	require_once 'login_functions.php';
	$conn = database_connect();
	if ($conn->connect_error) {
		echo "Connection failed<br>";
	}
	$query = "SELECT * FROM items WHERE store = Starkville";
	$results = $conn->query($query);
	if(!$results) {
		echo "Query failed";
	}
	/*
	echo "<br><table>
		<tr>
		<th>Item No.</th>
		<th>Name</th>
		<th>Description</th>
		<th>Quantity</th>
		<th>Price</th>
		<th>Location</th>
		</tr>";

	while($row = $results->fetch_assoc()) {
		
		echo "<tr>";
		echo "<td>" . $row['item_number'] . "</td>";
		echo "<td>" . $row['name'] . "</td>";
		echo "<td>" . $row['description'] . "</td>";
		echo "<td>" . $row['quantity'] . "</td>";
		echo "<td>" . $row['price'] . "</td>";
		echo "<td>" . $row['store'] . "</td>";
		echo "</tr>";
	}
	echo "</table>";*/
	$conn->close();
	?>

</body>
</html>