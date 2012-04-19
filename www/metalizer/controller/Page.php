<?php
/*
 Metalizer, a MVC php Framework.
 Copyright (C) 2012 David Reignier

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

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
	 * @param webscript string
	 * 	The webscript class name
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