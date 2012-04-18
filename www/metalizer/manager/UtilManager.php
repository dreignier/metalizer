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
 * Manager of all Utils.
 * @author David Reignier
 *
 */
class UtilManager extends Manager {

	/**
	 * Construct a new UtilManager
	 * @return UtilManager
	 */
	public function __construct() {
		parent::__construct('Util');

		// Require all files in util for non-object functions.
		foreach (glob(PATH_METALIZER_UTIL . '*.php') as $file) {
			require_once $file;
		}

		// Don't forget the application util folder
		foreach (glob(PATH_APPLICATION_UTIL . '*.php') as $file) {
			require_once $file;
		}
	}

}

/**
 * Get an Util by its name.
 * @param $name string
 * 	The name of an Util.
 * @return Util
 * 	The Util corresponding to the given name.
 */
function util($name) {
	return manager('Util')->get($name);
}

