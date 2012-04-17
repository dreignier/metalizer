<?php
/**
 * Webscript are component of a page.
 * @author David Reignier
 *
 */
class Webscript extends Controller {

	/**
	 * The folder of the webscript.
	 * @var string
	 */
	private $folder;

	/**
	 * Construct a new webscript
	 * @param $data array[mixed]
	 * 	Optional. The data for the webscript.
	 * @return Webscript
	 */
	public function __construct($data = array()) {
		$this->data = $data;
		$file = classLoader()->getFile($this->getClass());
		$this->folder = substr($file, 0, -(strlen($this->getClass()) + 4));
	}

	/**
	 * Display the webscript. Each webscript use a specific view file with this syntax (webscript class to lower case) . 'view.php'.
	 * The file is searched is the webscript folder.
	 */
	public function display() {
		$view = new WebscriptView($this, $this->data);
		$view->display();
	}

	/**
	 * This method is called when the webscript must display itself. By default it does nothing.
	 * Subclass should override this method.
	 */
	public function execute() {

	}

	/**
	 * Get the folder of the webscript.
	 * @return string
	 */
	public function getFolder() {
		return $this->folder;
	}

	/**
	 * Construct the path to a file for this webscript.
	 * @param $file string
	 * 	The file.
	 * @return string 
	 * 	The path to the given file for this webscript.
	 */
	public function getFile($file) {
		return $this->folder . $file;
	}
}