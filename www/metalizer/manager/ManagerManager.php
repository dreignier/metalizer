<?php
define('MANAGER_MANAGER_CACHE_FILE', PATH_CACHE . 'managers');

class ManagerManager extends Manager {
	
	static private $instance;
	
	public function __construct() {
		parent::__construct('Manager');
	}
	
	static public function finalize() {
		if (!isDevMode()) {
			ManagerManager::$instance->sleep();
			file_put_contents(MANAGER_MANAGER_CACHE_FILE, serialize(ManagerManager::$instance));	
		} else {
			if (file_exists(MANAGER_MANAGER_CACHE_FILE)) {
				unlink(MANAGER_MANAGER_CACHE_FILE);
			}			
		}
	}
	
	static public function initialize() {
		// Look for the cache file
		if (file_exists(MANAGER_MANAGER_CACHE_FILE)) {
			ManagerManager::$instance = unserialize(file_get_contents(MANAGER_MANAGER_CACHE_FILE));
		} else {
			ManagerManager::$instance = new ManagerManager();
		}
	}
	
	static public function instance() {
		return ManagerManager::$instance;
	}
	
}

function manager($name) {
	return ManagerManager::instance()->get($name);
}
