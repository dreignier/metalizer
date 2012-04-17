<?php
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

		if (is_a($value, 'MetalizerObject')) {
			$value->sleep();
		}

		file_put_contents($file, serialize($data));
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

		$result = unserialize(file_get_contents($file));

		if (is_a($result, 'MetalizerObject')) {
			$result->sleep();
		}

		return $result;
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