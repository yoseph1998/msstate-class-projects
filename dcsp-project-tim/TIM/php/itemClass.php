<?php
require_once 'loginInfo.php'; 
class Item
{
	//initializer 
	private $itemID; 
	private $itemNumber; 
	private $itemName; 
	private $itemPrice; 
	private $itemQuantity; 
	private $itemDepartment; 
	private $itemStore; 
	private $itemDescription; 
	
	function __construct($id, $item_number, $name, $price, $quantity, $department, $store, $description){
		$this->itemID = $id; 
		$this->itemNumber = $item_number; 
		$this->itemName = $name; 
		$this->itemPrice = $price; 
		$this->itemQuantity = $quantity; 
		$this->itemDepartment = $department; 
		$this->itemStore = $store; 
		$this->itemDescription = $description; 
	}
	//setter methods
	function set_itemID($id){
		$this->itemID = $id; 
	}
	function set_itemNumber($number){
		if($this->itemNumber == NULL){
			$this->itemNumber = $number;
		}
		else{
			$this->itemNumber = $number; 
			$this->update_table(); 
		}
	}
	function set_itemName($name){
		if($this->itemName == NULL){
			$this->itemName = $name;
		}
		else{
			$this->itemName = $name; 
			$this->update_table(); 
		}
	}
	function set_itemPrice($price){
		if($this->itemPrice == NULL){
			$this->itemPrice = $price;
		}
		else{
			$this->itemPrice = $price;
			$this->update_table(); 
		}
	}
	function set_itemQuantity($quantity){
		if($this->itemQuantity == NULL){
			$this->itemQuantity = $quantity;
		}
		else{
			$this->itemQuantity = $quantity;
			$this->update_table(); 
		}
	}
	function set_itemDepartment($dept){
		if($this->itemDepartment == NULL){
			$this->itemDepartment = $dept;
		}
		else{
			$this->itemDepartment = $dept;
			$this->update_table(); 
		}
	}
	function set_itemStore($store){
		if($this->itemStore == NULL){
			$this->itemStore = $store;
		}
		else{
			$this->itemStore = $store;
			$this->update_table(); 
		}	
	}
	function set_itemDescription($descript){
		if($this->itemDescription == NULL){
			$this->itemDescription = $descript;
		}
		else{
			$this->itemDescription = $descript;
			$this->update_table(); 
		}
	}
	
	//getter methods
	function get_itemID(){
		return $this->itemID; 
	}
	function get_itemNumber(){
		return $this->itemNumber; 
	}
	function get_itemName(){
		return $this->itemName; 
	}
	function get_itemPrice(){
		return $this->itemPrice; 
	}
	function get_itemQuantity(){
		return $this->itemQuantity; 
	}
	function get_itemDepartment(){
		return $this->itemDepartment; 
	}
	function get_itemStore(){
		return $this->itemStore; 
	}
	function get_itemDescription(){
		return $this->itemDescription; 
	}
	
	//methods
	//adds the data to the database
	function add_database(){
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error){
			die("Connection failed: " . $conn->connect_error); 
		}
		
		//This creates the query to add the item to the item table. 
		$query = "INSERT INTO items( id, item_number, name, price, quantity, department, store, description )"; 
		$query = $query . "VALUES(". $this->get_itemID() . ", " . $this->get_itemNumber() . ", "; 
		$query = $query . $this->get_itemPrice() . ", " . $this->get_itemQuantity() . ", "; 
		$query = $query . $this->get_itemDepartment() . ", " . $this->get_itemStore() . ", "; 
		$query = $query . $this->get_itemDescription() . ") "; 
		
		$result = $conn->query($query);
		if(!$result) {
			echo 'Query Failed';
		}
		
		//closes the query and connection
		$query->close(); 
		$conn->close(); 
		
		return true; 
	}
	//sets the quantity to 0
	function out_of_stock(){
		$this->set_itemQuantity(0);  
		$this->update_table(); 
		return true; 
	}
	//adds to the archive database
	function archive_item(){
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error){
			die("Connection failed: " . $conn->connect_error); 
		}
		
		//This creates the query to add the item to the item table. 
		$query = "INSERT INTO archives( id, item_number, name, price, quantity, department, store, description )"; 
		$query = $query . "VALUES(". $this->get_itemID() . ", " . $this->get_itemNumber() . ", "; 
		$query = $query . $this->get_itemPrice() . ", " . $this->get_itemQuantity() . ", "; 
		$query = $query . $this->get_itemDepartment() . ", " . $this->get_itemStore() . ", "; 
		$query = $query . $this->get_itemDescription() . ") ";
		
		$result = $conn->query($query);
		if(!$result) {
			echo 'Query Failed';
		}
		
		//deletes the item from the item table
		$deletionQuery = "DELETE FROM items WHERE id = '$this->get_itemID'";
		$deletionResult = $conn->query($query);
		if(!$deletionResult) {
			echo 'Query Failed';
		}
		$deletionResult->close(); 
		$result->close(); 
		$conn->close(); 
	}
	//used to update the table for changed data
	private function update_table(){
		$conn = new mysqli($hn, $un, $pw, $db);
		if($conn->connect_error){
			die("Connection failed in update_table: " . $conn->connect_error); 
		}
		//create the query to update table
		$query =  "UPDATE items SET item_number = " . $this->get_itemNumber() . " , name = " . $this->get_itemName();
		$query = $query . " , price = " . $this->get_itemPrice() . " , quantity = " . $this->get_itemQuantity(); 
		$query = $query . " , department = " . $this->get_itemDepartment() . " , store = " . $this->get_itemStore(); 
		$query = $query . " , description = " . $this->get_itemDescription() . "WHERE id = " . $this->get_itemID(); 
		
		
		$result = $conn->query($query); 
		if(!$result) {
			echo 'Query Failed';
		}
		
		//closes the query and connection
		$query->close(); 
		$conn->close(); 
		
		return true;
	}
}

?>