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
 * Super class of all controllers of metalizer.
 *
 * @author David Reignier
 *
 */
class Controller extends MetalizerObject {

	/**
	 * Data of the controller. The entire array is given to the views.
	 * @var array[mixed]
	 */
	protected $data = array();

	/**
	 * Add a data.
	 * @param name string
	 * 	The name of the data.
	 * @param value mixed
	 * 	The value of the data. Can be anything.
	 */
	public function data($name, $value) {
		$this->data[$name] = $value;
	}
}