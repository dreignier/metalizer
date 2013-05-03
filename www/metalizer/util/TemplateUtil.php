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
 * Scan all the directories to find templates and provide helper to use templates.
 * @author David Reignier
 *
 */
class TemplateUtil extends Util {

   /**
    * All the templates of the project with their real path.
    * @var array[string]
    */
   private $templates = array();

   public function __construct() {
      $paths = array(
         PATH_METALIZER . 'template/*.php', 
         PATH_METALIZER_LIBRARY . '*/template/*.php', 
         PATH_APPLICATION_LIBRARY . '*/template/*.php', 
         PATH_APPLICATION . 'template/*.php'
      );
      
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug('Loading all templates');
      }
   
      $trace = $this->log()->isTraceEnabled();
      foreach ($paths as $path) {
         foreach (_file()->glob($path) as $file) {
            if ($trace) {
               $this->log()->trace("Found $file");
            }
            
            $this->templates[substr($file, strpos($file, '/template/') + 10, -4)] = $file;
         }
      }
   }

   /**
    * Get the real path for a template.
    * @param $template string
    *    A template name.
    * @return string
    *    The path to the given template or null.
    */
   public function getPath($template) {
      return isset($this->templates[$template]) ? $this->templates[$template] : null;
   }

}

/**
 * @return TemplateUtil
 */
function template() {
   return util('Template');
}

/**
 * @see TemplateUtil#getPath
 */
function getTemplatePath($template) {
   return util('Template')->getPath($template);
}
