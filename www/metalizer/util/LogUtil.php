<?php
define('METALIZER_LOG_TRACE', 0);
define('METALIZER_LOG_DEBUG', 1);
define('METALIZER_LOG_INFO', 2);
define('METALIZER_LOG_WARNING', 3);
define('METALIZER_LOG_ERROR', 4);

class LogUtil extends Util {

	private function getLogFile() {
		return PATH_LOG . 'metalizer-' . date('Y-m-d');
	}

	private static $logLabels = array('TRACE', 'DEBUG', 'INFO', 'WARNING', 'ERROR');

	private function getLevel($caller) {
		if ($caller) {
			$level = config('log.level.' . $caller->getClass());

			if ($level !== null) {
				return $level;
			}
		}

		return config('log.level');
	}

	public function log($caller, $message, $level) {
		if ($level < $this->getLevel($caller)) {
			return;
		}

		$file = $this->getLogFile();
		$handle = fopen($file, 'a');
		$time = date('H:i:s');
		$class = $caller->getClass();
		$level = LogUtil::$logLabels[$level];

		fwrite($handle, "[$time][$level][$class] $message \n");
		fclose($handle);
	}
}

function _log($caller, $message, $level) {
	Util('Log')->log($caller, $message, $level);
}

function logTrace($message) {
	_log(null, $message, METALIZER_LOG_TRACE);
}

function logDebug($message) {
	_log(null, $message, METALIZER_LOG_DEBUG);
}

function logInfo($message) {
	_log(null, $message, METALIZER_LOG_INFO);
}

function logWarning($message) {
	_log(null, $message, METALIZER_LOG_WARNING);
}

function logError($message) {
	_log(null, $message, METALIZER_LOG_ERROR);
}

