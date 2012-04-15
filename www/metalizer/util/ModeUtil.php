<?php
define('MODE_DEVELOPMENT', 'development');
define('MODE_PRODUCTION', 'production');

class ModeUtil extends Util {

	public function getMode() {
		return config('metalizer.mode');
	}
	
}

function isDevMode() {
	return Util('Mode')->getMode() == MODE_DEVELOPMENT;
}

