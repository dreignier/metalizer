<?php
/**
 * Mother class of all classes in Metalizer.
 * @author Magus
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
	public function setManager(Manager $manager) {
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
	public function wakeUp(Manager $manager = null) {
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
		logTrace($this, makeLogMessage($message));
	}

	/**
	 * Log a debug level message.
	 * @param $message string
	 * 	The message
	 */
	public function logDebug($message) {
		logDebug($this, makeLogMessage($message));
	}

	/**
	 * Log a info level message.
	 * @param $message string
	 * 	The message
	 */
	public function logInfo($message) {
		logInfo($this, makeLogMessage($message));
	}

	/**
	 * Log a warning level message.
	 * @param $message string
	 * 	The message
	 */
	public function logWarning($message) {
		logWarning($this, makeLogMessage($message));
	}

	/**
	 * Log a error level message.
	 * @param $message string
	 * 	The message
	 */
	public function logError($message) {
		logError($this, makeLogMessage($message));
	}

}