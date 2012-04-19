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

// The class loader can't handle it's own dependencies.
require_once PATH_METALIZER_CORE . 'MetalizerObject.php';

define('CLASS_LOADER_CACHE_FILE', PATH_CACHE . 'classes');

/**
 * Handle the autoload of all classes in the project. This class use a cache file. The cache file is <code>cache/classes</code>.
 * If a class can't be found in the cache, the ClassLoader just refresh the cache and try again.
 * @author David Reignier
 */
class ClassLoader extends MetalizerObject {

	/**
	 * Files indexed by classes.
	 * @var array[string]
	 */
	private $files;

	/**
	 * ClassLoader is a Singleton.
	 * @var ClassLoader
	 */
	static private $instance = null;

	/**
	 * Load a class.
	 * @param $class string
	 * 	The class name.
	 * @return mixed Nothing if the class is loaded correctly, false otherwise.
	 */
	public function load($class) {
		if (isset($this->files[$class])) {
			require_once $this->files[$class];
			return;
		}

		$this->loadFiles();

		if (isset($this->files[$class])) {
			require_once $this->files[$class];
			return;
		}

		return false;
	}

	/**
	 * Initialize the class loader. The class loader should use his cache or create it.
	 */
	public function initialize() {
		if (file_exists(CLASS_LOADER_CACHE_FILE)) {
			$files = unserialize(file_get_contents(CLASS_LOADER_CACHE_FILE));
		} else {
			$this->loadFiles();
		}

		spl_autoload_register(array($this, 'load'));
	}

	/**
	 * Get the ClassLoader.
	 * @return ClassLoader
	 */
	static public function instance() {
		if (ClassLoader::$instance == null) {
			ClassLoader::$instance = new ClassLoader();
		}

		return ClassLoader::$instance;
	}

	/**
	 * Construct a new ClassLoader.
	 */
	private function __construct() {
		$this->files = array();
	}

	/**
	 * Load all files and create the ClassLoader cache.
	 */
	private function loadFiles() {
		// Find all php files with the first letter in uppercase
		$this->browseFiles(PATH_METALIZER);
		$this->browseFiles(PATH_APPLICATION);
		file_put_contents(CLASS_LOADER_CACHE_FILE, serialize($this->files));
	}

	/**
	 * Browse a directory recursively and get all the classes files (A class file begin with an upper case).
	 * @param $directory string
	 * 	The directory to browse.
	 */
	private function browseFiles($directory) {
		static $upperCases = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

		$dirHandle = opendir($directory);
		while (($file = readdir($dirHandle))) {
			if ($file != '.' && $file != '..') {
				$fullFile = "$directory/$file";
				if (substr($file, -4) == '.php' && strpos($upperCases, substr($file, 0, 1)) !== false) {
					$this->files[substr($file, 0, -4)] = $fullFile;
				} else if (is_dir($fullFile)) {
					$this->browseFiles($fullFile);
				}
			}
		}
	}

	/**
	 * Get the file of a class.
	 * @param $class string
	 * 	A class.
	 * @return string
	 * 	The class of the given file.
	 */
	public function getFile($class) {
		if (isset($this->files[$class])) {
			return $this->files[$class];
		}

		return null;
	}

}

/**
 * @return ClassLoader The ClassLoader instance.
 */
function classLoader() {
	return ClassLoader::instance();
}