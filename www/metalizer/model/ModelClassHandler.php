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

class ModelClassHandler extends MetalizerObject
{
	// is defined in each subclas
	protected $_table_name;
	public function find($where)
	{
		$sql = "SELECT * FROM {$this->_table_name} WHERE ({$where});";
		$lines = database()->query($sql);
		$line = $lines->next();
		$result = array();
		$class_name = str_replace('ClassHandler', '', $this->getClass());
		while($line) {
			$result[] = new $class_name($line);
			$line = $lines->next();
		}
		return $result;
	}
}
