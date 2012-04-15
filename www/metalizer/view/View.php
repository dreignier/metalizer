<?php
class View extends MetalizerObject {

	private $file;
	protected $data;
	
	public function __construct($file, $data) {
		$this->file = $file;
		$this->data = $data;
	}
	
	public function display() {
		// Variables names are weird to avoid overriding by data keys.
		foreach ($this->data as $__key__ => $__value__) {
			$$__key__ = $__value__;
		}
		
		include $this->file . '.php';
		
		foreach ($this->data as $__key__ => $__value__) {
			unset($$__key__);
		}
	}
	
}