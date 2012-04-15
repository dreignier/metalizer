<?php
class UtilManager extends Manager {
	
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

function util($name) {
	return manager('Util')->get($name);
}

