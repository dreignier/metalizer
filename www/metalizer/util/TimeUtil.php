<?php
define('IN_MILLISECONDS', 1000);
define('MINUTE', 60);
define('HOUR', MINUTE * 60);
define('DAY', HOUR * 24);
define('WEEK', DAY * 7);

/**
 * Provide some constants and helper for time and date.
 * @author David Reignier
 *
 */
class TimeUtil extends Util {

	/**
	 * Return the current date in a Unix timestamp.
	 * @return long 
	 * 	The current date.
	 */
	public function now() {
		return time(); 
	}
	
}

/**
 * @see TimeUtil#now 
 */
function now() {
	return Util('Time')->now();
}