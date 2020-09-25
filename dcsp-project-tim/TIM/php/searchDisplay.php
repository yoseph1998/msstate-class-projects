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

		tr:hover {
			cursor: pointer;
			background-color: #f5f5f5;}

	</style>
</head>
<body>

	<?php
	$search = ($_GET["search"]);

	if ($search == "") {
		$query = "SELECT * FROM items";
	}
	else {
		$query = "SELECT * FROM items WHERE item_number LIKE '%".$search."%'"."OR name LIKE '%".$search."%'"."OR description LIKE '%".$search."%'"."OR quantity LIKE '%".$search."%'"."OR price LIKE '%".$search."%'"."OR store LIKE '%".$search."%'";
	}

	require_once 'login_functions.php';
	$conn = database_connect();
	if ($conn->connect_error) {
		echo "Connection failed<br>";
	}
	$results = $conn->query($query);
	if(!$results) {
		echo "Query failed";
	}
	
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
		echo "<tr onclick=\"window.location.href = 'items_page.php?id=".$row['id']."';\">";
		echo "<td>" . $row['item_number'] . "</td>";
		echo "<td>" . $row['name'] . "</td>";
		echo "<td>" . $row['description'] . "</td>";
		echo "<td>" . $row['quantity'] . "</td>";
		echo "<td>" . $row['price'] . "</td>";
		echo "<td>" . $row['store'] . "</td>";
		echo "</tr>";
	}
	echo "</table>";
	$conn->close();
	?>

</body>
</html>