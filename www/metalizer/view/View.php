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
 * A view can include a php file using data. If the view have a data named 'foo', in the view you can use the $foo variable.
 * @author David Reignier
 *
 */
class View extends MetalizerObject {

   /**
    * The file of the view.
    * @var string
    */
   private $file;

   /**
    * Data for the view.
    * @var array[mixed]
    */
   protected $data;

   /**
    * Construct a new View.
    * @param $file string
    * 	The file for the view. Must be a existing php file without the .php extension.
    * @param $data array[mixed]
    * 	Data for the view.
    * @return View
    */
   public function __construct($file, $data) {
      $this->file = $file;
      $this->data = $data;
   }

   /**
    * The view display itself.
    */
   public function display() {
      // Variables names are weird to avoid overriding by data keys.
      foreach ($this->data as $__key__ => $__value__) {
         $$__key__ = $__value__;
      }

      $file = $this->file;

      if (substr($file, -4) != '.php') {
         $file = "$file.php";
      }

      include $file;
   }

}
