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
 * Super class of all model classes.
 * @author David Reignier
 *
 */
class Model extends MetalizerObject {

	/**
	 * Every model object have an unique id. If id is 0, the object is not registered in the database.
	 * @var integer
	 */
	protected $id = 0;
	
	/**
	 * The ModelClassHandler of the object.
	 * @var ModelClassHandler
	 */
	protected $classHandler;
	
	/**
	 * Construct a new empty Model.
	 * @return Model
	 */
	public function __construct() {
		$this->classHandler = model($this->getClass());
	}
	
	/**
	 * @see ModelClasshandler#save 
	 */
	public function save() {
		$this->classHandler->save($this);
	}
	
	/**
	 * @see ModelClasshandler#delete 
	 */
	public function delete() {
		$this->classHandler->delete($this);
	}
	
	/**
	 * @return boolean
	 * 	true if the object is registered in the database, false otherwise.
	 */
	public function isRegistered() {
		return $this->id === 0;
	}
	
	/**
	 * Set classHandler to null.
	 * @see MetalizerObject#onSleep()
	 */
	public function onSleep() {
		$this->classHandler = null;
	}
	
	/**
	 * Initialize the classHandler.
	 * @see MetalizerObject#onWakeUp()
	 */
	public function onWakeUp() {
		$this->classHandler = model($this->getClass());
	}
	
}
