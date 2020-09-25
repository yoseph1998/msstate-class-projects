<?php
require 'loginInfo.php'; 
class Sessions{
	private $userID; 
	private $sessionID; 
	private $startTime; 
	private $endTime; 
	
	//initializer
	function __construct($user_id, $session_id, $start_time, $end_time){
		$this->userID = $user_id; 
		$this->sessionID = $session_id; 
		$this->startTime = $start_time; 
		$this->endTime = $end_time; 
	}
	//accessor methods
	function get_userID(){
		return $this->userID; 
	}
	function get_sessionID(){
		return $this->sessionID;
	}
	function get_startTime(){
		return $this->startTime; 
	}
	function get_endTIme(){
		return $this->endTime; 
	}
	//setter methods
	/*
		The mutator methods except set_sessionID will call update_table, so if the table data gets changed 
		in the main program, the table will stayed updated without having to manually call the function. 
	*/
	function set_userID($id){
		if($this->userID == NULL){
			$this->userID = $id; 
		}
		else{
			$this->userID = $id; 
			$this->update_table(); 
		}
	}
	function set_sessionID($session){
		$this->sessionID = $session; 
	}
	function set_startTime($start){
		if($this->startTime == NULL){
			$this->startTime = $start; 
		}
		else{
			$this->startTime = $start; 
			$this->update_table(); 
		}
	}
	function set_endTIme($end){
		if($this->endTIme == NULL){
			$this->endTIme = $end; 
		}
		else{
			$this->endTIme = $end;
			$this->update_table(); 
		}
	}
	//methods
	
	//adds the session data to the database
	function add_self_to_database(){
		require_once "login_functions.php";
		
		$conn = database_connect();
		
		//creates the query to create a session
		$query = "INSERT INTO sessions( session_id, user_id, session_start, session_end ) VALUES( "; 
		$query = $query . "'" . $this->get_sessionID() . "', " . $this->get_userID() . ", "; 
		$query = $query . "'" . $this->get_startTime() . "', '" . $this->get_endTIme() . "') ";
		
		$result = $conn->query($query);
		if(!$result) {
			echo 'Query Failed';
		}
		
		//closes the query and connection
		//$query->close(); 
		$conn->close(); 
		
		return true; 

	}
	//used to update the table for changed data
	function update_table(){
		require_once "login_functions.php";
		$conn = database_connect();
		
		//create the query to update table
		$query = "UPDATE sessions SET session_end = '" . $this->get_endTIme() . "' WHERE session_id = '" . $this->get_sessionID() ."'";
		$result = $conn->query($query); 
		if(!$result) {
			echo 'Query Failed (Update Table)<br>';
		}
		
		//closes the query and connection
		//$query->close(); 
		$conn->close(); 
		
		return true;
	}
	
}
?>
