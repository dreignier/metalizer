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

define('PATH_RESSOURCE_BUNDLE', PATH_RESSOURCE . 'bundle/');
define('PATH_RESSOURCE_BUNDLE_JS', PATH_RESSOURCE_BUNDLE . 'js/');
define('PATH_RESSOURCE_BUNDLE_CSS', PATH_RESSOURCE_BUNDLE . 'css/');
 
/**
 * Provide some helper for file and directory manipulation.
 * @author David Reignier
 *
 */
class ResourceBundleUtil extends Util {
   
   public function __construct() {
      // Clean the bundle folders
      util('File')->rmdir(PATH_RESSOURCE_BUNDLE);
      mkdir(PATH_RESSOURCE_BUNDLE);
      mkdir(PATH_RESSOURCE_BUNDLE_JS);
      mkdir(PATH_RESSOURCE_BUNDLE_CSS);
   }
   
   public function css($name, $files) {
      if (isDevMode()) {
         foreach($files as $file) {
            $url;
            if (isLibraryExists('less_css') && substr($file, -5) == '.less') {
               $url = lessCssUrl($file);
            } else {
               $url = cssUrl($file);
            }
            
            echo  '<link type="text/css" rel="stylesheet" href="' . $url . '" />';
         }
      } else {
         $path = PATH_RESSOURCE_BUNDLE_CSS . str_replace('.', '/', $name) . '.css';
         if (!file_exists($path)) {
            // Create the bundle
            echo "Create bundle $name <br/>";
            $handle = fopen($path, 'w');
            foreach($files as $file) {
               $file = PATH_RESSOURCE_CSS . $file;
               if (isLibraryExists('less_css') && substr($file, -5) == '.less') {
                  $content = util('LessCss')->compile($file);
               } else {
                  $content = file_get_contents($file);
               }
               
               fwrite($handle, $content);
            }
            fclose($handle);
         }
         
         $url = resUrl('bundle/css/' . str_replace('.', '/', $name) . '.css');
         echo '<link type="text/css" rel="stylesheet" href="' . $url . '" />';
      }
   }
   
   public function js($name, $files) {
      if (isDevMode()) { 
         foreach($files as $file) {
            echo '<script type="text/javascript" src="' . jsUrl($file) . '" ></script>';
         }
      } else {
         $path = PATH_RESSOURCE_BUNDLE_JS . str_replace('.', '/', $name) . '.js';
         if (!file_exists($path)) {
            // Create the bundle
            echo "Create bundle $name <br/>";
            $handle = fopen($path, 'w');
            foreach($files as $file) {
               $file = PATH_RESSOURCE_JS . $file;
               $content = file_get_contents($file);
               
               fwrite($handle, $content);
            }
            fclose($handle);
         }
         
         $url = resUrl('bundle/js/' . str_replace('.', '/', $name) . '.js');
         echo '<script type="text/javascript" src="' . $url . '" /></script>';
      }
   }
   
}

function cssBundle($name, $files) {
   util('ResourceBundle')->css($name, $files);
}

function jsBundle($name, $files) {
   util('ResourceBundle')->js($name, $files); 
}
