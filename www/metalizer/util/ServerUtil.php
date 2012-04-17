<?php
/**
 * Helpers for the $_SERVER supervar.
 * @author David Reignier
 *
 */
class ServerUtil extends Util {
	
	/**
	 * Get a value in $_SERVER.
	 * @param $key string
	 * 	The key of the value/
	 * @return mixed 
	 * 	$_SERVER[$key]
	 */
	public function get($key) {
		return $_SERVER[$key];
	}
	
}