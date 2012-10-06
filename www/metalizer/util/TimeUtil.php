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

define('IN_MILLISECONDS', 1000);
define('MINUTE', 60);
define('HOUR', MINUTE * 60);
define('DAY', HOUR * 24);
define('WEEK', DAY * 7);

/**
 * Provide some constants and helper for time and date.
 * @author David Reignier
 *
 */
class TimeUtil extends Util {

	/**
	 * Return the current time in a Unix timestamp.
	 * @return long
	 * 	The current time.
	 */
	public function now() {
		return time();
	}

}

/**
 * @see TimeUtil#now
 */
function now() {
	return Util('Time')->now();
}