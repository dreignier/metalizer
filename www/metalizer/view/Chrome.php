<?php 
class Chrome extends View {
	
	private $content;
	private $name;
	
	public function __construct($content, $name, $data = array()) {
		parent::__construct(PATH_APPLICATION_CHROME . $name, $data);
		$this->content = $content;
	}
	
	protected function content() {
		$this->content->display();
	}
}