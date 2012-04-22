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

/**
 * Mother class of all classes in Metalizer.
 * @author David Reignier
 *
 */
class MetalizerObject {

	/**
	 * Say if the objet is sleeping.
	 * @var boolean
	 */
	private $sleeping = false;

	/**
	 * The manager of the object. Can be null.
	 * @var Manager
	 */
	private $manager = null;

	/**
	 * Get the manager of the object.
	 * @return Manager
	 * 	The manager of the object, or null.
	 */
	public function getManager() {
		return $this->manager;
	}

	/**
	 * Set the manager of the object.
	 * @param $manager Manager
	 * 	A manager
	 */
	public function setManager($manager) {
		$this->manager = $manager;
	}

	/**
	 * @return true if the object is sleeping, false otherwise.
	 */
	public function isSleeping() {
		return $this->sleeping;
	}

	/**
	 * Put the object in the sleep state. MetalizerObject#onSleep will be called.
	 */
	public function sleep() {
		if (!$this->sleeping) {
			$this->onSleep();
			$this->manager = null;
			$this->sleeping = true;
		}
	}

	/**
	 * Called when MetalizerObject#sleep is called. Do nothing by default. Subclasses should override this method.
	 */
	public function onSleep() {

	}

	/**
	 * Wake up the object.
	 * @param $manager Manager
	 * 	Optional. The new manager of the object.
	 */
	public function wakeUp($manager = null) {
		if ($this->sleeping) {
			if ($manager) {
				$this->manager = $manager;
			}
			$this->onWakeUp();
			$this->sleeping = false;
		}
	}

	/**
	 * Called when MetalizerObject#wakeUp is called. Do nothing by default. Subclasses should override this method.
	 */
	public function onWakeUp() {

	}

	/**
	 * @return string
	 * 	A string representation of the object.
	 */
	public function toString() {
		return $this->getClass();
	}

	/**
	 *
	 * @return string
	 * 	Same as <code>get_class($object)</code>
	 */
	public function getClass() {
		return get_class($this);
	}

	/**
	 * Get the log name (for log messages). Subclasses should override this method.
	 * @return string
	 * 	An empty string
	 */
	public function getLogName() {
		return "";
	}

	/**
	 * Make a log message.
	 * @param $message string
	 * 	The message
	 * @return string
	 * 	The object log name and the message.
	 */
	private function makeLogMessage($message) {
		return $this->getLogName() . " $message";
	}

	/**
	 * Log a trace level message.
	 * @param $message string
	 * 	The message
	 */
	public function logTrace($message) {
		_log($this, $this->makeLogMessage($message), METALIZER_LOG_TRACE);
	}

	/**
	 * Log a debug level message.
	 * @param $message string
	 * 	The message
	 */
	public function logDebug($message) {
		_log($this, $this->makeLogMessage($message), METALIZER_LOG_DEBUG);
	}

	/**
	 * Log a info level message.
	 * @param $message string
	 * 	The message
	 */
	public function logInfo($message) {
		_log($this, $this->makeLogMessage($message), METALIZER_LOG_INFO);
	}

	/**
	 * Log a warning level message.
	 * @param $message string
	 * 	The message
	 */
	public function logWarning($message) {
		logWarning($this, $this->makeLogMessage($message), METALIZER_LOG_WARNING);
	}

	/**
	 * Log a error level message.
	 * @param $message string
	 * 	The message
	 */
	public function logError($message) {
		logError($this, $this->makeLogMessage($message), METALIZER_LOG_ERROR);
	}
	
	/**
	 * Override the __sleep php method.
	 */
	public function __sleep() {
		$this->sleep();		
	}
	
	/**
	 * Override the __wakeup php method.
	 */
	public function __wakeup() {
		$this->wakeUp();
	}

}