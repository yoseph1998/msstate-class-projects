<?php
class Dept{
	private $departmentName; 
	private $storeName; 
	private $itemCount; 
	
	//initialize
	function __construct($department_name, $store_name, $item_count){
		$this->departmentName = $department_name; 
		$this->storeName = $store_name; 
		$this->itemCount = $item_count; 
	}
	//accessor methods
	function get_deptName(){
		return $this->departmentName; 
	}
	function get_storeName(){
		return $this->storeName; 
	}
	function get_itemCount(){
		return $this->itemCount; 
	}
	//other methods
	function set_deptName($name){
		$this->departmentName = $name;
	}
	function set_storeName($name){
		$this->storeName = $name;
	}
	function set_itemCount($value){
		$this->itemCount = $value;
	}
}
?>