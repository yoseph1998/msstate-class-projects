<?php
	require "loginInfo.php"; 

	/**
	 * Connects to the database
	 * Returns: connection object of mysqli.
	 */
	function database_connect() {
		require 'loginInfo.php';
		$conn = new mysqli($hn, $un, $pw, $db);
		
		if($conn->connect_error) {
			echo "Connection failed<br>";
		}
		
		return $conn;
	}
	
	/**
	 * Hashes the given password using ripemd128
	 * Returns: the hashed password
	 */
	function hash_password($password) {
		require 'loginInfo.php';
		$hashed_password = hash('ripemd128', $salt1 . $password . $salt2);
		return $hashed_password;
	}
	
	/**
	 * Validates the given credentials with the users in the database.
	 * Returns: true if user is valid, false if not.
	 */
	function validate_user($username, $password) {
		$username = strtoupper($username);
		require 'loginInfo.php';
		$conn = database_connect();
		if(!$conn) {
			echo 'Connection Failed<br>';
		}
		$hashed_password = hash_password($password);
		$query = "SELECT * FROM users WHERE username = '$username' AND password_hash = '$hashed_password'";
		$result = $conn->query($query);
		if(!$result) {
			echo 'Query Failed (Validate user)<br>';
		}
		$row = $result->fetch_assoc();
		$conn->close();
		if($row)
			return true;
		return false;
	}
	
	/**
	 * Ends the currently running session and clears it from the database.
	 * Returns: Void
	 */
	function end_session() {
		require_once "sessionsClass.php";
		if (!isset($_SESSION)) {
			session_start();
		}
		$session_id = session_id();
		
		//GET USER DATA
		$conn = database_connect();
		$query = "SELECT * FROM sessions WHERE session_id = '$session_id'";
		$result = $conn->query($query);
		
		if(!$result) {
			echo 'Query failed.<br>';
		}
		
		$row = $result->fetch_assoc();
		
		if($row) {
			$session = new Sessions($row['user_id'], $session_id, $row['session_start'], date("Y-m-d H:i:s", time()-1));
			$session -> update_table();
		}
		
		$conn->close();
		
		session_unset();
		session_destroy();
	}
	
	/**
	 * Checks if a user is currently logged in.
	 * Returns true if a user is logged in, else otherwise.
	 */
	function is_logged_in() {
		if (session_status()!= PHP_SESSION_ACTIVE) {
				session_start();
		}
		
		$session_id = session_id();
		
		$conn = database_connect();
		$query = "SELECT * FROM sessions WHERE session_id = '$session_id' AND session_end > '".date("Y-m-d H:i:s", time())."'";
		$results = $conn->query($query);
		if(!$results) {
			echo '<br>Query Failed(is_looged_in)<br>';
		}
		$row = $results->fetch_assoc();
		if(!$row) {
			if(isset($_SESSION['user_id'])) {
				end_session();
			}
			return false;
		}
		
		return true;
	}
	
	/**
	 * Returns the existing session. If session does not exist it will create a session and populate the database.
	 * If the session has expired on the database it will end the session.
	 * Returns: session object of the sessionsClass.
	 */
	function get_session($username) {
		require_once "sessionsClass.php";
		
		$username = strtoupper($username);
		
		//GET USER DATA
		$conn = database_connect();
		$query = "SELECT * FROM users WHERE username = '$username'";
		$result = $conn->query($query);
		$row = $result->fetch_assoc();
		$conn->close();
		
		if (session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
		if(!empty($_SESSION['session_id'])) { //IF SESSION ALREADY EXISTS
			//GET SESSION DATA
			$session_id = $_SESSION['session_id'];
			session_id($session_id);
			$user_id = $_SESSION['user_id'];
			
			// Not Implemented
			//CONNECT TO DATABASE
			$conn = database_connect();
			$query = "SELECT * FROM sessions WHERE session_id = '$session_id' AND user_id = '$user_id'";
			$result = $conn->query($query);
			$row = $result->fetch_assoc();
			$conn->close();
			
			
			//if session doesn't exist or has expired delete session and create a new one.
			if(!$row OR strtotime($row['session_end']) < time()) {
				end_session();
				return null;
			}
			
			$session = new Sessions($row['user_id'], $row['session_id'], $row['session_start'], $row['session_end']);
			return $session;
			
		} else { //IF NO SESSION EXISTS
			
			session_regenerate_id();
			$session_id = session_id();
			$_SESSION['user_id'] = $row['user_id'];
			$_SESSION['username'] = $username;
			$_SESSION['permissions'] = $row['permissions'];
			$_SESSION['session_id'] = $session_id;
			
			$session = new Sessions($_SESSION['user_id'], $_SESSION['session_id'], date("Y-m-d H:i:s", time()), date("Y-m-d H:i:s", time() + 3600));
			$session -> add_self_to_database();
			return $session;
		}
	}
	
	
	function get_user($user_id = "") {
		require_once 'usersClass.php';
		if(!is_logged_in()) {
			return null;
		}
		
		if (session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
		
		$conn = database_connect();
		if($user_id == "")
			$query = "SELECT * FROM users WHERE user_id=".$_SESSION['user_id'];
		else
			$query = "SELECT * FROM users WHERE user_id=".$user_id;
		$result = $conn->query($query);
		$row = $result->fetch_assoc();
		$conn->close();
		
		$users = new Users($row['user_id'], $row['username'], $row['password_hash'], $row['permissions']);
		return $users;
	}
	
	// ITEM RELATED //
	
	function search_items($id = null, $item_number = null, $name = null, $price_min = null, $price_max = null, $quantity_min = null, $quantity_max = null, $department = null, $store = null, $description = null) {
		require_once 'itemClass.php';
		$nf = false;
		
		$conn = database_connect();
		
		// BUILD QUERY //
		$query = "SELECT * FROM items";
		
		if($id != null or $item_number != null or $name != null or ($price_min != null and $price_max != null) or ($quantity_min != null and $quantity_max != null) or $department != null or $store != null or $description != null)
			$query = $query . " WHERE";
		
		if($item_number != null) {
			$query = $query . " item_number = $item_number";
			$nf = true;
		}
		
		if($id != null) {
			if($nf)
				$query = $query . " AND";
			$query = $query . " id = $id";
			$nf = true;
		}
		
		if($name != null) {
			if($nf)
				$query = $query . " AND";
			$query = $query . " name LIKE '%($name)%'";
			$nf = true;
		}
		
		if($price_min != null and $price_max != null) {
			if($nf)
				$query = $query . " AND";
			$query = $query . " price <= $price_max AND price >= $price_min";
			$nf = true;
		}
		if($quantity_min != null and $quantity_max != null) {
			if($nf)
				$query = $query . " AND";
			$query = $query . " quantity <= $quantity_max AND quantity >= $quantity_min";
			$nf = true;
		}
		if($department != null) {
			if($nf)
				$query = $query . " AND";
			$query = $query . " department = '$department'";
			$nf = true;
		}
		
		if($store != null) {
			if($nf)
				$query = $query . " AND";
			$query = $query . " department = '$store'";
			$nf = true;
		}
		
		if($department != null) {
			if($nf)
				$query = $query . " AND";
			$query = $query . " description LIKE '%($department)%'";
			$nf = true;
		}
		
		//echo "<br>$query";
		
		//Executing Query
		$results = $conn->query($query);
		if(!$results)
			echo "<br>Query failed";
		
		// Compiling Results
		$items = array();
		
		$empty = true;
		while($row = $results->fetch_assoc()) {
			$empty = false;
			$item = new Item($row['id'], $row['item_number'], $row['name'], $row['price'], $row['quantity'], $row['department'], $row['store'], $row['description']);
			array_push($items, $item);
		}
		return $items;
	}
?>