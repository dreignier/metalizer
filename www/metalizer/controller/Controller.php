<?php
class Controller extends MetalizerObject {

	protected $data = array();
	
	public function data($name, $value) {
		$this->data[$name] = $value;
	}
}