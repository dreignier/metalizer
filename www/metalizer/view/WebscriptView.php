<?php
class WebscriptView extends View {
	
	private $webscript;
	
	public function __construct($webscript, $data) {
		parent::__construct($webscript->getFolder() . strtolower($webscript->getClass()) . '.view', $data);		
	}
	
}