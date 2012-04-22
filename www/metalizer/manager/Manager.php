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
 * A Manager handle a specific class.
 * @author David Reignier
 *
 */
abstract class Manager extends MetalizerObject {

	/**
	 * The handled class
	 * @var string
	 */
	protected $class;

	/**
	 * All known instances of the class.
	 * @var array[MetalizerObject]
	 */
	protected $items;

	/**
	 * Construct a new Manager
	 * @param $class string
	 * 	The handled class
	 * @return Manager
	 */
	public function __construct($class) {
		$this->class = $class;
		$this->items = array();
	}

	/**
	 * On sleep, the manager order to all items to sleep too.
	 * @see MetalizerObject#onSleep()
	 */
	public function onSleep() {
		foreach ($this->items as $item) {
			$item->sleep();
		}
	}
	
	/**
	 * Call MetalizerObject#finalize on all items.
	 * @see MetalizerObject#onFinalize()
	 */
	public function onFinalize() {
		$this->logInfo('Finalize');
		foreach ($this->items as $item) {
			$item->finalize();
		}
	}

	/**
	 * Get a item. It return the object of the class '$name' . '$handledClass'. The result is awaken.
	 * @param $name string
	 * 	The name of the item.
	 * @return MetalizerObject
	 */
	public function get($name) {
		if (!isset($this->items[$name])) {
			$this->load($name);
		}

		$result = $this->items[$name];

		if ($result) {
			$result->wakeUp($this);
		}

		return $result;
	}

	/**
	 * If an objet can't be found by Manager#get, we try to load it with this function.
	 * It must add the new object in the $items member. Or  Manager#get will failed.
	 * @param $name string
	 * 	The name of the item.
	 */
	protected function load($name) {
		$class = $name . $this->class;
		$item = new $class();
		$this->items[$name] = $item;
	}

}