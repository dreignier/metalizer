<?php
/**
 * The Template is a view wich can define 'regions' for other views. It act as a layout.
 * The name of the template will specify its view file. The Template will search for a file named '$name.php' in the template application folder.
 * @author David Reignier
 *
 */
class Template extends View {
	
	/**
	 * Components of the Template.
	 * @var array[View]
	 */
	private $components;
	
	/**
	 * Construct a new template.
	 * @param $name string
	 * 	The name of the template.
	 * @param $data array[mixed]
	 * 	The data for the template (Same of for the view). Optional.
	 * @return Template
	 */
	public function __construct($name, $data = array()) {
		parent::__construct(PATH_APPLICATION_TEMPLATE . $name, $data);
		$this->components = array();
	}
	
	/**
	 * Put a component in the region of the template.
	 * @param $name string
	 * 	The name of the region.
	 * @param $webscriptName string
	 * 	The webscript name. When the Template will display the region, it will search for a webscript class with this name.
	 */
	public function component($name, $webscriptName) {
		$this->components[$name] = $webscriptName;
	}
	
	/**
	 * Display a region.
	 * @param $name string
	 * 	The region name.
	 * @param $chrome string
	 * 	Optional. The chrome name of the region.
	 */
	protected function region($name, $chrome = null) {
		$class = $this->components[$name];
		$webscript = new $class($this->data);
		
		if ($chrome) {
			$chrome = new Chrome($webscript, $chrome, $this->data);
			$chrome->display();
		} else {
			$webscript->display();
		}
	}
	
	/**
	 * Display a template in the template. The template give its component and data to the new template and display it.
	 * @param $name string
	 * 	The name of the template.
	 */
	protected function template($name) {
		$template = new Template($name, $this->data);
		$template->components = $this->components;
		$template->display();
	} 
	
}