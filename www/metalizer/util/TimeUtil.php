<?php
define('IN_MILLISECONDS', 1000);
define('MINUTE', 60);
define('HOUR', MINUTE * 60);
define('DAY', HOUR * 24);
define('WEEK', DAY * 7);

class TimeUtil extends Util {

	public function now() {
		return time(); 
	}
	
}

function now() {
	return Util('Time')->now();
}