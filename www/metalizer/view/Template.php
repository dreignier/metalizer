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
 * The Template is a view wich can define 'regions' for other views. It act as a layout.
 * The name of the template will specify its view file. The Template will search for a file named '$name.php' in the template application folder.
 * @author David Reignier
 *
 */
class Template extends View {

   /**
    * Components of the Template.
    * @var array[string]
    */
   private $components = array();
   
   /**
    * Parameters for the components of the template
    * @var array[mixed]
    */
   private $parameters = array();

   /**
    * Construct a new template.
    * @param $name string
    * 	The name of the template.
    * @param $data array[mixed]
    * 	The data for the template (Same of for the view). Optional.
    * @return Template
    */
   public function __construct($name, $data = array()) {
      parent::__construct(getTemplatePath($name), $data);
   }

   /**
    * Put a component in the region of the template.
    * @param $name string
    * 	The name of the region.
    * @param $webscriptName string
    * 	The webscript name. When the Template will display the region, it will search for a webscript class with this name.
    * @param $parameters array[mixed]
    *    The parameters for the execute method of the given webscript.
    */
   public function component($name, $webscriptName, $parameters = array()) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Register webscript $webscriptName in region $name");
      }
      
      $this->components[$name] = $webscriptName;
      $this->parameters[$name] = $parameters;
   }

   /**
    * Display a region.
    * @param $name string
    * 	The region name.
    * @param $chrome string
    * 	Optional. The chrome name of the region.
    */
   protected function region($name, $chrome = null) {
      if (isset($this->components[$name])) {
         if ($this->log()->isDebugEnabled()) {
            $this->log()->debug("Displaying $name");
            if ($chrome) {
               $this->log()->debug("Chrome : $chrome");   
            }
         }
         
         $class = $this->components[$name];

         $webscript = new $class($this->data);
         $parameters = $this->parameters[$name];
         try {
            if (call_user_func_array(array($webscript, 'execute'), $parameters) !== false) {
               $webscript->display($chrome);
            }
         } catch (Exception $exception) {
            $webscript->error($exception);
         }
      } else {
         if ($this->log()->isInfoEnabled()) {
            $this->log()->info("Region $name has no component");
         }
      }
   }

   /**
    * Display a template in the template. The template give its component and data to the new template and display it.
    * @param $name string
    * 	The name of the template.
    */
   protected function template($name) {
      if ($this->log()->isDebugEnabled()) {
            $this->log()->debug("Use template $name");
      }
      
      $template = new Template($name, $this->data);
      $template->components = $this->components;
      $template->display();
   }

}
