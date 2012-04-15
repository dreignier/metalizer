<?php
class Template extends View {
	
	private $components;
	
	public function __construct($name, $data = array()) {
		parent::__construct(PATH_APPLICATION_TEMPLATE . $name, $data);
		$this->components = array();
	}
	
	public function component($name, $webscriptName) {
		$this->components[$name] = $webscriptName;
	}
	
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
	
	protected function template($name) {
		$template = new Template($name, $this->data);
		$template->components = $this->components;
		$template->display();
	} 
	
}