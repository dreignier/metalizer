<?php
/**
 * A Manager handle a specific class. 
 * @author David Reignier
 *
 */
abstract class Manager extends MetalizerObject {
	
	/**
	 * The handled class
	 * @var string
	 */
	protected $class;
	
	/**
	 * All known instances of the class.
	 * @var array[MetalizerObject]
	 */
	protected $items;
	
	/**
	 * Construct a new Manager
	 * @param $class string
	 * 	The handled class
	 * @return Manager
	 */
	public function __construct($class) {
		$this->class = $class;
		$this->items = array();
	}
	
	/**
	 * On sleep, the manager order to all items to sleep too.
	 * @see MetalizerObject#onSleep()
	 */
	public function onSleep() {
		foreach ($this->items as $item) {
			$item->sleep();
		}
	}
	
	/**
	 * Get a item. It return the object of the class '$name' . '$handledClass'. The result is awaken.
	 * @param $name string
	 * 	The name of the item.
	 * @return MetalizerObject
	 */
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
	
	/**
	 * If an objet can't be found by Manager#get, we try to load it with this function.
	 * It must add the new object in the $items member. Or  Manager#get will failed.
	 * @param $name string
	 * 	The name of the item.
	 */
	protected function load($name) {
		$class = $name . $this->class;
		$item = new $class();
		$this->items[$name] = $item;
	}
	
}