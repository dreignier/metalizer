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
 * A result of a Database query.
 * @author David Reignier
 *
 */
class QueryResult extends MetalizerObject {

	/**
	 * Create a new query.
	 * @param $query mysqli_stmt 
	 * 	A mysqli query result.
	 * @return QueryResult
	 */
	public function __construct(mysqli_stmt $query) {

	}

	/**
	 * Get the next row, or null if the result is at the end.
	 * @return array[mixed] 
	 * 	An array representation of the next row
	 */
	public function next() {

	}

	/**
	 * Return the results count.
	 * @return int 
	 * 	The results count.
	 */
	public function count() {

	}

	/**
	 *
	 * @return bool 
	 * 	true is the QueryResult if empty (eg. count == 0), false otherwise.
	 */
	public function isEmpty() {

	}
}