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
 * Provide some helper for file and directory manipulation.
 * @author David Reignier
 *
 */
class FileUtil extends Util {
	
	/**
	 * Check if the directory for a file path exists. If not, the directory is created.
	 * @param $file string
	 *  A file path.
	 */
	public function checkDirecoty($file) {
		if (file_exists($file)) {
			return;
		}
		
		$file = explode('/', $file);
				
		if (sizeof($file) > 0) {
			// Remove the file name
			$file = array_slice($file, 0, sizeof($file) - 1);
			
			// Remove the last /
			$path = substr(PATH_ROOT, 0, -1);
			foreach($file as $directory) {
				$path =  "$path/$directory";
				
				if (!file_exists($path)) {
					mkdir($path);
				}
			}
		}
	}
	
}