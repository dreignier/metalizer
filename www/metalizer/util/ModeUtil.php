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

define('MODE_DEVELOPMENT', 'development');
define('MODE_PRODUCTION', 'production');

/**
 * Provide an easy way to check the current mode of the applicatioN.
 * @author David Reignier
 *
 */
class ModeUtil extends Util {

	/**
	 * Get the application mode. It's the 'metalizer.mode' configuration value.
	 * @return string 
	 * 	MODE_DEVELOPMENT or MODE_PRODUCTION
	 */
	public function getMode() {
		return config('metalizer.mode');
	}
	
}

/**
 * @return bool 
 * 	true if the current application mode is MODE_DEVELOPMENT. False otherwise.
 */
function isDevMode() {
	return Util('Mode')->getMode() == MODE_DEVELOPMENT;
}

