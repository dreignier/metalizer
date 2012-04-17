<?php
/**
 * The Chrome is a view wich can display another View in itself.
 * The Chrome view file is seached in the 'chrome' application folder with its name.
 * @author David Reignier
 *
 */
class Chrome extends View {

	/**
	 * The View to display in the Chrome.
	 * @var View
	 */
	private $content;

	/**
	 * The name of the Chrome.
	 * @var string
	 */
	private $name;

	/**
	 * Construct a new Chrome.
	 * @param $content View
	 * 	The view to display inside the Chrome.
	 * @param $name string
	 * 	The name of the Chrome. When the Chrome must display itself, it will searched for a file named '$name.php' in the application 'chrome' folder.
	 * @param $data array[mixed]
	 * 	The data for the Chrome (Same as for the view). Optional.
	 * @return Chrome
	 */
	public function __construct($content, $name, $data = array()) {
		parent::__construct(PATH_APPLICATION_CHROME . $name, $data);
		$this->content = $content;
	}

	/**
	 * Display the content of the Chrome.
	 * @see View#display
	 */
	protected function content() {
		$this->content->display();
	}
}