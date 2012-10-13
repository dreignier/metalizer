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
 * Handle the generated resources directory.
 * @author David Reignier
 *
 */
class GeneratedResourceUtil extends Util {
   
   /**
    * Clean the generated resources directory.
    */
   public function __construct() {
      util('File')->rmdir(PATH_RESOURCE_GEN);
      @mkdir(PATH_RESOURCE_GEN);
   }
   
}

