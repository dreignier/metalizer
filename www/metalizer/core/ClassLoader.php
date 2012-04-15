<?php
require_once PATH_METALIZER_CORE . 'MetalizerObject.php';

define('CLASS_LOADER_CACHE_FILE', PATH_CACHE . 'classes');

/**
 * Handle the autoload of all classes in the project. This class use a cache file. The cache file is <code>cache/classes</code>.
 * If a class can't be found in the cache, the ClassLoader just refresh the cache and try again.
 * @author Magus
 */
class ClassLoader extends MetalizerObject {
	
	/**
	 * Files indexed by classes.
	 * @var array
	 */
	private $files;

	/**
	 *
	 * @var ClassLoader
	 */
	static private $instance = null;
	
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
	
	public function initialize() {
		if (file_exists(CLASS_LOADER_CACHE_FILE)) {
			$files = unserialize(file_get_contents(CLASS_LOADER_CACHE_FILE));
		} else {
			$this->loadFiles();
		}
		
		spl_autoload_register(array($this, 'load'));
	}

	static public function instance() {
		if (ClassLoader::$instance == null) {
			ClassLoader::$instance = new ClassLoader();
		}

		return ClassLoader::$instance;
	}

	private function __construct() {
		$this->files = array();
	}

	private function loadFiles() {
		// Find all php files with the first letter in uppercase
		$this->browseFiles(PATH_METALIZER);
		$this->browseFiles(PATH_APPLICATION);
		file_put_contents(CLASS_LOADER_CACHE_FILE, serialize($this->files));
	}
	
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