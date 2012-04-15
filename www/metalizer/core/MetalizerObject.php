<?php
/**
 * Mother class of all classes in Metalizer.
 * @author Magus
 *
 */
class MetalizerObject {

	private $sleeping = false;
	private $manager = null;

	public function getManager() {
		return $this->manager;
	}

	public function setManager($manager) {
		$this->manager = $manager;
	}

	public function isSleeping() {
		return $this->sleeping;
	}

	public function sleep() {
		if (!$this->sleeping) {
			$this->onSleep();
			$this->manager = null;
			$this->sleeping = true;
		}
	}

	public function onSleep() {

	}
	

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