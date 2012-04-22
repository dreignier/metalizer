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
 * Handle the cache. Can be access by the cache() function. See cache.php for configuration values.
 * In development mode, the cache do nothing.
 *
 * Value names should be like 'foo.bar.some_stuff.my_value'.
 *
 * @author David Reignier
 *
 * @todo Handle a local cache
 * @todo Clean the entire cache when it's needed
 */
class CacheUtil extends Util {

	/**
	 * Get the path for a file.
	 * @param $name string
	 * 	The name of the file.
	 * @return string
	 * 	The path to the file.
	 */
	private function getFilePath($name) {
		return str_replace('.', '/', PATH_CACHE . $name);
	}

	/**
	 * Put a value in the cache.
	 * @param $name string
	 * 	The name of the value.
	 * @param $value mixed
	 * 	The value. Must be serializable.
	 */
	public function put($name, $value) {
		$file = $this->getFilePath($name);

		if (isDevMode()) {
			return;
		}

		file_put_contents($file, serialize($value));
	}

	/**
	 * @param $name string
	 * 	The name of a value
	 * @return bool
	 * 	true if the value is in the cache, false otherwise.
	 */
	public function exists($name) {
		$file = $this->getFilePath($name);

		if (isDevMode()) {
			return false;
		}

		return file_exists($file);
	}

	/**
	 * Retrieve a value.
	 * @param $name string
	 * 	The name of a value
	 * @return mixed
	 * 	The value, or null if the value is not in the cache.
	 */
	public function get($name) {
		$file = $this->getFilePath($name);

		if (isDevMode() || !$this->exists($name)) {
			return null;
		}

		return unserialize(file_get_contents($file));
	}

	/**
	 * Clean a value from the cache.
	 * @param $name string
	 * 	The name of a value. It can be a subname of a value.
	 */
	public function clean($name) {
		$file = $this->getFilePath($name);

		if ($this->exists($name)) {
			unlink($file);
			return;
		}

		// Maybe it's a folder
		if (is_dir($file)) {
			rmdir($file);
		}
	}

}

/**
 * Access to the CacheUtil.
 * @return CacheUtil
 * 	The CacheUtil.
 */
function cache() {
	return Util('Cache');
}