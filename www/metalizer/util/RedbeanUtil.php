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

/**
 * Provide easy access to the redbean class.
 * @author David Reignier
 *
 */
class RedbeanUtil extends Util {
	
	/**
	 * Used in the get method.
	 */
	private $dynamicToStatic;
	
	/**
	 * Construct a new RedbeanUtil
	 */
	public function __construct() {
		$this->dynamicToStatic = new RedbeanUtil_DynamicToStatic();
		$this->connect();
	}
	
	/**
	 * Require the redbean file and connect the database.
	 */
	public function connect() {
		require_once PATH_METALIZER_EXTERNAL . 'redbean/redbean.php';
		R::setup(config('database.connection_string'), config('database.user'), config('database.password'));
	}
	
	/**
	 * We need to require the redbean file and connect to the database.
	 */
	public function onWakeUp() {
		$this->connect();	
	}
	
	/**
	 * Close the database connection.
	 */
	public function onSleep() {
		if (class_exists('R')) {
			R::close();
		}
	}
	
	/**
	 * @return RedbeanUtil_DynamicToStatic
	 * 	You can use this to access to the "R" classes without the "static" way.
	 */
	public function get() {
		return $this->dynamicToStatic;
	}
	
}

/**
 * All method call are passed to the R class.
 */
class RedbeanUtil_DynamicToStatic extends MetalizerObject {
	
	/**
	 * Override the __call method
	 */
	public function __call($name, $arguments) {
		return call_user_func_array("R::$name", $arguments);
	}
}

/**
 * @see RedbeanUtil#get
 */
function R() {
	return Util('Redbean')->get();
}
	