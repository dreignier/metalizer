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
 * Provide an easy way to store data in files (in the data folder)
 * @author David Reignier
 *
 */
class StoreUtil extends Util {
	
	/**
	 * The StoreUtil use a local cache.
	 * @var array[mixed]
	 */
	private $cache = array();
	
	private function getFilePath($key) {
		return PATH_DATA . str_replace('.', '/', $key);
	}
 	
	/**
	 * Store a value.
	 * @param $name string
	 * 	The name of the value.
	 * @param $value mixed
	 *  The name. Must be serializable.
	 */
	public function store($name, $value) {
		$file = $this->getFilePath($name);
		$this->cache[$file] = $value;
		util('File')->checkDirectory($file);
		file_put_contents($file, serialize($value));
	}
	
	/**
	 * Load a stored value.
	 * @param $name string
	 * 	The name of the value.
	 * @return mixed
	 * 	The value. Or null if no value with $name is found.
	 */
	public function load($name) {
		if (!$this->exists($name)) {
			return null;
		}
		
		$file = $this->getFilePath($name);
		
		if (isset($this->cache[$file])) {
			return $this->cache[$file];
		}
		
		$result = unserialize(file_get_contents($file));
		$this->cache[$file] = $result;
		
		return $result;
	}
	
	/**
	 * Delete a value or a folder of values.
	 * @param $name string
	 * 	The name of a value or a folder of values.
	 */
	public function delete($name) {
		$file = $this->getFilePath($name);

		unset($this->cache[$file]);
		
		if ($this->exists($name)) {
			unlink($file);
			return;
		}

		// Maybe it's a folder
		if (is_dir($file)) {
			rmdir($file);
		}
	}
	
	/**
	 * @param $name string
	 * 	The name of a value
	 * @return bool
	 * 	true if the value is in the store, false otherwise.
	 */
	public function exists($name) {
		$file = $this->getFilePath($name);
		
		if (isset($this->cache[$file])) {
			return true;
		}
		
		return file_exists($file);
	}
	
}

/**
 * @return StoreUtil
 * 	The StoreUtil
 */
function store() {
	return util('Store');
}