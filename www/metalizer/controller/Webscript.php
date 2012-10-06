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
 * Webscript are component of a page.
 * @author David Reignier
 *
 */
class Webscript extends Controller {

   /**
    * The folder of the webscript.
    * @var string
    */
   private $folder;

   /**
    * Construct a new webscript
    * @param $data array[mixed]
    * 	Optional. The data for the webscript.
    * @return Webscript
    */
   public function __construct($data = array()) {
      $this->data = $data;
      $file = classLoader()->getFile($this->getClass());
      $this->folder = substr($file, 0, -(strlen($this->getClass()) + 4));
   }

   /**
    * Display the webscript. Each webscript use a specific view file with this syntax (webscript class to lower case) . 'view.php'.
    * The file is searched is the webscript folder.
    */
   public function display() {
      $view = new WebscriptView($this, $this->data);
      $view->display();
   }

   /**
    * This method is called when the webscript must display itself. By default it does nothing.
    * Subclass should override this method.
    */
   public function execute() {

   }

   /**
    * Get the folder of the webscript.
    * @return string
    */
   public function getFolder() {
      return $this->folder;
   }

   /**
    * Construct the path to a file for this webscript.
    * @param $file string
    * 	The file.
    * @return string
    * 	The path to the given file for this webscript.
    */
   public function getFile($file) {
      return $this->folder . $file;
   }

}
