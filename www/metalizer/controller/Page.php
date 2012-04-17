<?php
/**
 * Represent a page of the application.
 * Pages can populate a template with webscripts.
 * @author David Reignier
 *
 */
class Page extends Controller {

	/**
	 * The webscripts indexed by region.
	 * @var array[Webscript]
	 */
	private $components = array();

	/**
	 * Add a webscript to the future view.
	 * @param region string 
	 * 	The targeted region.
	 * @param webscript Webscript
	 * 	The webscript
	 */
	public function component($region, $webscript) {
		$this->components[$region] = $webscript;
	}

	/**
	 * Display a template, using the page webscripts.
	 * @param $template string
	 * 	 The template name without the '.php' extension. The template file must exists in the application template folder.
	 */
	public function template($template) {
		$template = new Template($template, $this->data);

		foreach ($this->components as $region => $webscript) {
			$template->component($region, $webscript);
		}

		$template->display();
	}
}