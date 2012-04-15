<?php 

class CacheUtil extends Util {
	
	/// @TODO handle local cache
	private $localCache = array();
	
	private function getFilePath($name) {
		return str_replace('.', '/', PATH_CACHE . $name);
	}

	public function put($name, $value) {
		$file = $this->getFilePath($name);
		
		if (isDevMode()) {
			return;
		}
		
		if (is_a($value, 'MetalizerObject')) {
			$value->sleep();
		}
		
		file_put_contents($file, serialize($data));
	}
	
	public function exists($name) {
		$file = $this->getFilePath($name);
		
		if (isDevMode()) {
			return false;
		}
		
		return file_exists($file);
	}
	
	public function get($name) {
		$file = $this->getFilePath($name);
		
		if (isDevMode() || !$this->exists($name)) {
			return null;
		}
		
		$result = unserialize(file_get_contents($file));
		
		if (is_a($result, 'MetalizerObject')) {
			$result->sleep();
		}
		
		return $result;
	}
	
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

class LocalCacheElement {
	private $lastUse;
	private $value = null;
	
	public function __construct($value) {
		$this->value = $value;
		$this->lastUse = time();
	}
}

function cache() {
	return Util('Cache');
} 