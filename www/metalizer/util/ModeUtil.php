<?php
define('MODE_DEVELOPMENT', 'development');
define('MODE_PRODUCTION', 'production');

/**
 * Provide an easy way to check the current mode of the applicatioN.
 * @author David Reignier
 *
 */
class ModeUtil extends Util {

	/**
	 * Get the application mode. It's the 'metalizer.mode' configuration value.
	 * @return string 
	 * 	MODE_DEVELOPMENT or MODE_PRODUCTION
	 */
	public function getMode() {
		return config('metalizer.mode');
	}
	
}

/**
 * @return bool 
 * 	true if the current application mode is MODE_DEVELOPMENT. False otherwise.
 */
function isDevMode() {
	return Util('Mode')->getMode() == MODE_DEVELOPMENT;
}

