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
 *
 */
class FilterChainer extends MetalizerObject {
   
   private $path;
   
   public function __construct($path) {
      $this->path = $path;
   }
   
   public function run() {
      foreach (config('filter.patterns') as $pattern => $filter) {
         $filter = new $filter();
         if ($temp = $filter->execute($this->path)) {
            $this->path = $temp;
         }
      }

      return $this->path;
   }
}