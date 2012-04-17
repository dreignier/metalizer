<?php
/**
 * A view can include a php file using data. If the view have a data named 'foo', in the view you can use the $foo variable.
 * @author David Reignier
 *
 */
class View extends MetalizerObject {

	/**
	 * The file of the view.
	 * @var string
	 */
	private $file;
	
	/**
	 * Data for the view.
	 * @var array[mixed]
	 */
	protected $data;
	
	/**
	 * Construct a new View.
	 * @param $file string
	 * 	The file for the view. Must be a existing php file without the .php extension.
	 * @param $data array[mixed]
	 * 	Data for the view.
	 * @return View
	 */
	public function __construct($file, $data) {
		$this->file = $file;
		$this->data = $data;
	}
	
	/**
	 * The view display itself.
	 */
	public function display() {
		// Variables names are weird to avoid overriding by data keys.
		foreach ($this->data as $__key__ => $__value__) {
			$$__key__ = $__value__;
		}
		
		include $this->file . '.php';
	}
	
}