<?php
/*
 Metalizer, a MVC php Framework.
 Copyright (C) 2012 David Reignier

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

define('MANAGER_MANAGER_CACHE_FILE', PATH_CACHE . 'managers');

/**
 * The manager of all managers in the application.
 * @author David Reignier
 *
 */
class ManagerManager extends Manager {

	/**
	 * ManagerManager is a singletion
	 * @var ManagerManager
	 */
	static private $instance;

	/**
	 * Construct a new ManagerManager. Because of php, we can't set this constructor in private.
	 * _DO NOT USE IT_
	 * @return ManagerManager
	 */
	public function __construct() {
		parent::__construct('Manager');
	}

	/**
	 * Finalize the ManagerManager. Must be called at the end of the application. The ManagerManager put itself in sleep mode and in a cache file.
	 */
	static public function terminate() {
		ManagerManager::$instance->finalize();
		if (!isDevMode()) {
			ManagerManager::$instance->sleep();
			file_put_contents(MANAGER_MANAGER_CACHE_FILE, serialize(ManagerManager::$instance));
		} else {
			if (file_exists(MANAGER_MANAGER_CACHE_FILE)) {
				unlink(MANAGER_MANAGER_CACHE_FILE);
			}
		}
	}

	/**
	 * Initialize the ManagerManager. After that, you can access to the ManagerManager with the ManagerManager#instance() function.
	 * In production mode, the ManagerManager will try to retrieve itself in its cache file.
	 */
	static public function initialize() {
		// Look for the cache file
		if (file_exists(MANAGER_MANAGER_CACHE_FILE)) {
			ManagerManager::$instance = unserialize(file_get_contents(MANAGER_MANAGER_CACHE_FILE));
		} else {
			ManagerManager::$instance = new ManagerManager();
		}
	}

	/**
	 * The unique instance of ManagerManager.
	 * @return ManagerManager
	 */
	static public function instance() {
		return ManagerManager::$instance;
	}

}

/**
 * Get a manager.
 * @param $name string
 * 	The name of a manager
 * @return Manager
 * 	The manager corresponding to the given name.
 */
function manager($name) {
	return ManagerManager::instance()->get($name);
}
