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
 * @author David Reignier
 */
class LessCssPage extends Page {

   /**
    * Compile "on the fly" a less file.
    * Don't use this in production mode. But it's fine in development mode.
    */
   public function compile($file) {
      $this->setContentType('text/css');
      if (file_exists($file)) {
         echo util('LessCss')->compile($file);
      } else {
         echo "/* Resource not found : $file */";
      }
   }

}   