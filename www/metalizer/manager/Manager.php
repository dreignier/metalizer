<?php
/**
 * 
 * @author Magus
 *
 */
abstract class Manager extends MetalizerObject {
	
	protected $class;
	protected $items;
	
	public function __construct($class) {
		$this->class = $class;
		$this->items = array();
	}
	
	public function onSleep() {
		foreach ($this->items as $item) {
			$item->sleep();
		}
	}
	
	public function get($name) {
		if (!isset($this->items[$name])) {
			$this->load($name);
		}
		
		$result = $this->items[$name];
		
		if ($result) {
			$result->wakeUp($this);
		}
		
		return $result;
	}
	
	protected function load($name) {
		$class = $name . $this->class;
		$item = new $class();
		$this->items[$name] = $item;
	}
	
}