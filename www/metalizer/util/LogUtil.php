<?php
/*
 Metalizer, a MVC php Framework.
 Copyright (C) 2012 David Reignier

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

define('METALIZER_LOG_TRACE', 0);
define('METALIZER_LOG_DEBUG', 1);
define('METALIZER_LOG_INFO', 2);
define('METALIZER_LOG_WARNING', 3);
define('METALIZER_LOG_ERROR', 4);

/**
 * A log util for all the application. Its write in the "log" folder.
 * See the log.php file for configuration values.
 * @author David Reignier
 *
 */
class LogUtil extends Util {

	/**
	 * Get the log file name
	 * @return string
	 * 	A string like "log/metalizer-(today)"
	 */
	private function getLogFile() {
		return PATH_LOG . 'metalizer-' . date('Y-m-d');
	}

	/**
	 * Labels of all log levels.
	 * @var array[string]
	 */
	private static $logLabels = array('TRACE', 'DEBUG', 'INFO', 'WARNING', 'ERROR');

	/**
	 * Get the log level.
	 * @param $caller MetaliezObject
	 * 	The caller of the log function.
	 * @return int
	 * 	The log level for the caller.
	 */
	private function getLevel($caller) {
		if ($caller) {
			
			$level = config('log.level.' . $caller->getClass());	
			

			if ($level !== null) {
				return $level;
			}
		}

		return config('log.level');
	}

	/**
	 * Log a message. The level is lower than the log level (for the called), nothing is done.
	 * @param $caller MetalizerObject
	 * 	The caller.
	 * @param $message string
	 * 	The message.
	 * @param $level int
	 * 	The level of the message.
	 */
	public function log($caller, $message, $level) {
		if ($level < $this->getLevel($caller)) {
			return;
		}

		$file = $this->getLogFile();
		$handle = fopen($file, 'a');
		$time = date('H:i:s');
 		$class = ($caller !== null) ? $caller->getClass() : '';
		$level = LogUtil::$logLabels[$level];

		fwrite($handle, "[$time][$level][$class] $message \n");
		fclose($handle);
	}
}

/**
 * @see LogUtil#log
 */
function _log($caller, $message, $level) {
	Util('Log')->log($caller, $message, $level);
}

/**
 * Log a message with no caller and the trace level.
 * @param $message string
 * 	The message.
 */
function logTrace($message) {
	_log(null, $message, METALIZER_LOG_TRACE);
}

/**
 * Log a message with no caller and the debug level.
 * @param $message string
 * 	The message.
 */
function logDebug($message) {
	_log(null, $message, METALIZER_LOG_DEBUG);
}

/**
 * Log a message with no caller and the info level.
 * @param $message string
 * 	The message.
 */
function logInfo($message) {
	_log(null, $message, METALIZER_LOG_INFO);
}

/**
 * Log a message with no caller and the warning level.
 * @param $message string
 * 	The message.
 */
function logWarning($message) {
	_log(null, $message, METALIZER_LOG_WARNING);
}

/**
 * Log a message with no caller and the error level.
 * @param $message string
 * 	The message.
 */
function logError($message) {
	_log(null, $message, METALIZER_LOG_ERROR);
}

