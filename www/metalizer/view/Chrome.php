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
 * The Chrome is a view wich can display another View in itself.
 * The Chrome view file is seached in the 'chrome' application folder with its name.
 * @author David Reignier
 *
 */
class Chrome extends View {

   /**
    * The View to display in the Chrome.
    * @var View
    */
   private $content;

   /**
    * The name of the Chrome.
    * @var string
    */
   private $name;

   /**
    * Construct a new Chrome.
    * @param $content View
    * 	The view to display inside the Chrome.
    * @param $name string
    * 	The name of the Chrome. When the Chrome must display itself, it will searched for a file named '$name.php' in the application 'chrome' folder.
    * @param $data array[mixed]
    * 	The data for the Chrome (Same as for the view). Optional.
    * @return Chrome
    */
   public function __construct($content, $name, $data = array()) {
      parent::__construct(PATH_APPLICATION_CHROME . $name, $data);
      $this->content = $content;
   }

   /**
    * Display the content of the Chrome.
    * @see View#display
    */
   protected function content() {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug('Displaying content');
      }
      
      $this->content->display();
   }

}
