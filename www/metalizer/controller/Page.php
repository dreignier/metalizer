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

   /**
    * The content type of the page. <code>text/html</code> by default.
    */
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
    * The parameters for webscripts. Indexed by region.
    * @var array[mixed]
    */
   private $parameters = array();

   /**
    * If cleanOutput is false, there's no output clean.  You should set it to false if you render anything else than xhtml.
    * @var bool
    */
   private $cleanOutput = true;

   /**
    * Add a webscript to the future view.
    * @param $region string
    * 	The targeted region.
    * @param $webscript string
    * 	The webscript class name
    * @param $parameters array[mixed]
    *    The parameters for the execute method of the given webscript.
    */
   public function component($region, $webscript, $parameters = array()) {
      if (!is_array($parameters)) {
         $arguments = func_get_args();
         $parameters = array_slice($arguments, 2);
      }
      
      $this->components[$region] = $webscript;
      $this->parameters[$region] = $parameters;
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
         if ($this->log()->isInfoEnabled()) {
            $this->log()->info("Displaying using the template $this->template");
         }
         
         $template = new Template($this->template, $this->data);

         foreach ($this->components as $region => $webscript) {
            $template->component($region, $webscript, $this->parameters[$region]);
         }

         $template->display();
      }
   }

   /**
    * Set the content type of the page.
    * This function must be called before any output !
    * @param $contentType string
    *    The new content type for the page.
    */
   protected function setContentType($contentType) {
      if ($this->log()->isInfoEnabled()) {
         $this->log()->info("New content type : $contentType");
      }
      
      $this->contentType = $contentType;
      if ($contentType != 'text/html') {
         $this->noOutputClean();
      }
      _header()->set('Content-Type', $this->contentType);
   }

   /**
    * Set the http response code.
    * @param $code int
    *    A http response code.
    */
   protected function setCode($code) {
      if ($this->log()->isInfoEnabled()) {
         $this->log()->info("New response code : $code");
      }
      _header()->setHttpResponseCode($code);
   }

   /**
    * @return string
    *    The content type of the page.
    */
   public function getContentType() {
      return $this->contentType;
   }

   /**
    * Disable the output clean.
    */
   public function noOutputClean() {
      $this->cleanOutput = false;
   }

   /**
    * @return bool
    *    True if the output must be cleaned. False otherwise.
    */
   public function cleanOutput() {
      return $this->cleanOutput;
   }

}
