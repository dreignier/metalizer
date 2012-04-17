<?php
/**
 * Super class of all controllers of metalizer.
 *
 * @author David Reignier
 *
 */
class Controller extends MetalizerObject {

	/**
	 * Data of the controller. The entire array is given to the views.
	 * @var array[mixed]
	 */
	protected $data = array();

	/**
	 * Add a data.
	 * @param name string 
	 * 	The name of the data.
	 * @param value mixed
	 * 	The value of the data. Can be anything.
	 */
	public function data($name, $value) {
		$this->data[$name] = $value;
	}
}