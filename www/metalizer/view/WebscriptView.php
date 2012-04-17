<?php
/**
 * A simple view Class for Webscript. It search a view file in the Webscript folder as '(name of Webscript in lower case).view.php'
 * @author David Reignier
 *
 */
class WebscriptView extends View {
		
	/**
	 * Construct a new WebscriptView
	 * @param $webscript Webscript
	 * 	The Webscript of the view.
	 * @param $data array[mixed]
	 * 	Data for the view.
	 * @return WebscriptView
	 */
	public function __construct($webscript, $data) {
		parent::__construct($webscript->getFolder() . strtolower($webscript->getClass()) . '.view', $data);		
	}
	
}