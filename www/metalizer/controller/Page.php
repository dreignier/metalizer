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
 * Represent a page of the application.
 * Pages can populate a template with webscripts.
 * @author David Reignier
 *
 */
class Page extends Controller {
   
   private $contentType = 'text/html';

   /**
    * The template name to display at the end.
    * @var string
    */
   private $template = null;

   /**
    * The webscripts indexed by region.
    * @var array[Webscript]
    */
   private $components = array();

   /**
    * Add a webscript to the future view.
    * @param region string
    * 	The targeted region.
    * @param webscript string
    * 	The webscript class name
    */
   public function component($region, $webscript) {
      $this->components[$region] = $webscript;
   }

   /**
    * Set the template used by the page. 
    * @param $template string
    *     The template name without the '.php' extension. The template file must exists in the application template folder.
    */    
   public function template($template) {
      $this->template = $template;
   }

   /**
    * Display the template, using the page webscripts.
    */
   public function display() {
      if ($this->template) {
         $template = new Template($this->template, $this->data);
   
         foreach ($this->components as $region => $webscript) {
            $template->component($region, $webscript);
         }
   
         $template->display();
      }
   }
   
   /**
    * Set the content type of the page.
    * @param $contentType string
    *    The new content type for the page.
    */
   protected function setContentType($contentType) {
      $this->contentType = $contentType;
      util('Header')->set('Content-Type', $this->contentType);
   }
   
   /**
    * @return string
    *    The content type of the page.
    */
   public function getContentType() {
      return $this->contentType;
   }

}
