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
 * Provide an easy way to access all model classes.
 * @author David Reignier
 *
 */
class ModelClassHandlerUtil extends Util {

	/**
	 * Get a ModelClassHandler.
	 * @param $name string
	 * 	The name of the ModelClassHandler.
	 * @return ModelClassHandler
	 *  The ModelClassHandler with the class name '$nameClassHandler'
	 */
	public function get($name) {
		return manager('ModelClassHandler')->get($name);
	}
	
}

/**
 * Get a ModelClassHandler.
 * @see ModelClassHandlerUtil#get
 */
function model($name) {
	return Util('ModelClassHandler')->get($name);
}