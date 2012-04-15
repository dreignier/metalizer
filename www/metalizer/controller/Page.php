<?php
class Page extends Controller {
	
	private $components = array();
	
	public function component($region, $webscript) {
		$this->components[$region] = $webscript;
	}
	
	public function template($template) {
		$template = new Template($template, $this->data);
		
		foreach ($this->components as $region => $webscript) {
			$template->component($region, $webscript);
		}
		
		$template->display();
	}
}