<?php
require_once 'loginInfo.php'; 
class Users{
	private $username; 
	private $permissions; 
	private $password; 
	
	//initializer
	function __construct($userID, $user_name, $pass_word, $permission){
		$user_name = strtolower($user_name);
		$this->user_id = $userID;
		$this->username = $user_name; 
		$this->password = $pass_word; 
		$this->permissions = $permission; 
	}
	
	//methods
	
	//get methods
	function get_userID(){
		return $this->user_id; 
	}
	function get_username(){
		return $this->username;
	}
	function get_password(){
		return $this->password;
	}
	function get_permissions(){
		return $this->permissions;
	}
	
	//set methods
	/*
		The mutator methods will call update_table, so if the table data gets changed 
		in the main program, the table will stayed updated without having to manually call the function. 
	*/
	function set_username($name){
		if($this->username == NULL){
			$this->username = $name;
		}
		else{
			$this->username = $name; 
			$this->update_table(); 
		}
	}
	function set_password($pass){
		if($this->password == NULL){
			$this->password = $pass;
		}
		else{
			$this->password = $pass; 
			$this->update_table(); 
		}
	}
	function set_permissions($type){
		if($this->permissions == NULL){
			$this->permissions = $type;
		}
		else{
			$this->permissions = $type; 
			$this->update_table(); 
		}
	}
	
	//adds the user to the database
	function add_self_to_database(){
		require_once 'login_functions.php';
		$conn = database_connect();
		if($conn->connect_error){
			die("Connection failed in update_table: " . $conn->connect_error); 
		}
		$query = "SELECT * FROM users WHERE username = '".$this->username ."'";
		$result = $conn->query($query);
		$row = $result->fetch_assoc();
		if($row) {
			return false;
		}
		
		//creates the query to create a user
		$query = "INSERT INTO users( username, password_hash, permissions) VALUES('" . $this->get_username() 
		. "', '" . $this->get_password() . "', " . $this->get_permissions() . ") "; 
		
		$result = $conn->query($query);
		if(!$result) {
			echo 'Query Failed';
			return false;
		}
		
		//closes the query and connection
		//$query->close(); 
		$conn->close(); 
		return true; 
	}
	
	//used to update the table for changed data
	function update_table(){
		require_once 'login_functions.php';
		$conn = database_connect();
		if($conn->connect_error){
			die("Connection failed in update_table: " . $conn->connect_error); 
		}
		//create the query to update table
		$query = "UPDATE users SET username = '" . $this->get_username() . "' , password_hash = '" . $this->get_password()."'";
		$query = $query . " , permissions = " . $this->get_permissions() . " WHERE user_id = " . $this->get_userID(); 
		
		
		$result = $conn->query($query); 
		if(!$result) {
			$conn->close();
			return false;
		}
		
		//closes the query and connection
		//$query->close(); 
		$conn->close(); 
		
		return true;
	}
	
	//used to update the table for changed data
	function delete_from_table(){
		require_once 'login_functions.php';
		$conn = database_connect();
		if($conn->connect_error){
			die("Connection failed in update_table: " . $conn->connect_error); 
		}
		//create the query to update table
		$query = "DELETE FROM users WHERE user_id = " . $this->get_userID(); 
		
		echo $query;
		$result = $conn->query($query); 
		if(!$result) {
			$conn->close();
			return false;
		}
		
		//closes the query and connection
		//$query->close(); 
		$conn->close(); 
		
		return true;
	}
}
?>