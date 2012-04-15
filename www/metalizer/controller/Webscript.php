<?php
class Webscript extends Controller {
	
	private $folder;
	
	public function __construct($data = array()) {
		$this->data = $data;
		$file = classLoader()->getFile($this->getClass());
		$this->folder = substr($file, 0, -(strlen($this->getClass()) + 4));
	}
	
	public function display() {
		$this->execute();
		
		$view = new WebscriptView($this, $this->data);
		$view->display();
	}
	
	public function execute() {
		
	}
	
	public function getFolder() {
		return $this->folder;
	}
	
	public function getFile($file) {
		return $this->folder . $file;
	}
}