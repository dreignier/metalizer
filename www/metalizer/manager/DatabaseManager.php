<?php
/**
 * The manager of Database objects.
 * @author David Reignier
 *
 */
class DatabaseManager extends Manager {
	
	/**
	 * Construct a new DatabaseManager. Just call the Manager construct with "Database".
	 * @return DatabaseManager
	 */
	public function __construct() {
		parent::__construct("Database");
	}

	/**
	 * Override the load method of Manager.
	 * @see Manager#load($name)
	 */
	protected function load(string $name) {
		$database = new Database($name);
		$this->items[$name] = $database;
	}
	
}

/**
 * A helper function.
 * @param $name string
 * 	Optional. A database name. 'metalizer' by default.
 * @return Database 
 * 	The Database object with the given name.
 */
function database($name = 'metalizer') {
	return manager('Database')->get($name);
}