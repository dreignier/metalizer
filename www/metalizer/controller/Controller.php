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
	 * @param name The name of the data.
	 * @param value The value of the data. Can be anything.
	 */
	public function data(string $name, mixed $value) {
		$this->data[$name] = $value;
	}
}