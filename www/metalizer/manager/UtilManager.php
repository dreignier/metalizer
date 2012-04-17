<?php
/**
 * Manager of all Utils.
 * @author David Reignier
 *
 */
class UtilManager extends Manager {
	
	/**
	 * Construct a new UtilManager
	 * @return UtilManager
	 */
	public function __construct() {
		parent::__construct('Util');
		
		// Require all files in util for non-object functions.
		foreach (glob(PATH_METALIZER_UTIL . '*.php') as $file) {
			require_once $file; 
		}
		
		// Don't forget the application util folder
		foreach (glob(PATH_APPLICATION_UTIL . '*.php') as $file) {
			require_once $file; 
		}
	}
	
}

/**
 * Get an Util by its name.
 * @param $name string
 * 	The name of an Util.
 * @return Util 
 * 	The Util corresponding to the given name.
 */
function util($name) {
	return manager('Util')->get($name);
}

