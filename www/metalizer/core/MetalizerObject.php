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
	 * @return Manager The manager of the object, or null.
	 */
	public function getManager() {
		return $this->manager;
	}

	/**
	 * Set the manager of the object.
	 * @param $manager A manager
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
	 * 
	 * @param $manager
	 * @return unknown_type
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
	
	public function onWakeUp() {

	}

	/**
	 * @return string A string representation of the object.
	 */
	public function toString() {
		return $this->getClass();
	}

	/**
	 *
	 * @return string Same as <code>get_class($object)</code>
	 */
	public function getClass() {
		return get_class($this);
	}

	public function getLogName() {
		return "";
	}
	
	private function makeLogMessage($message) {
		return $this->getLogName() . " $message";
	}

	public function logTrace($message) {
		logTrace($this, makeLogMessage($message));
	}

	public function logDebug($message) {
		logDebug($this, makeLogMessage($message));
	}

	public function logInfo($message) {
		logInfo($this, makeLogMessage($message));
	}

	public function logWarning($message) {
		logWarning($this, makeLogMessage($message));
	}

	public function logError($message) {
		logError($this, makeLogMessage($message));
	}

}