<?php
class DatabaseManager extends Manager {
	
	public function __construct() {
		parent::__construct("Database");
	}

	protected function load($name) {
		$database = new Database($name);
		$this->items[$name] = $database;
	}
	
}