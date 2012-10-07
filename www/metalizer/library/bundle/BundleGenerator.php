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
 * A BundleGenerator car generate a resource bundle.
 * @author David Reignier
 *
 */
abstract class BundleGenerator extends MetalizerObject {

   /**
    * Generate the html code for a file specified by its url.
    * @param $url string
    *    A valid url.
    */
   abstract public function html($url);
   
   /**
    * Resolve the url for a file in a bundle.
    * @param $file string
    *    The file name in the bundle.
    */
   abstract public function resolveFileUrl($file);
   
   /**
    * Resolve the path for a file in a bundle.
    * @param $file string
    *    The file name in the bundle.
    */
   abstract public function resolveFilePath($file);
   
   /**
    * Resolve the path to the final file for a bundle.
    * @param $bundle string
    *    The name of the bundle.
    */
   abstract public function resolveBundlePath($bundle);
   
   /**
    * Resolve the url to the final file for a bundle.
    * @param $bundle string
    *    The name of the bundle.
    */
   abstract public function resolveBundleUrl($bundle);
   
   /**
    * Read the content of a file.
    * @param $file string
    *    The complete path to a file.
    */
   abstract public function readFile($file);
   
   /**
    * Generate the bundle. It call devMode or prodMode according to the isDevMode() value.
    * @param $bundle string
    *    The name of the bundle.
    * @param $files array[string]
    *    Files in the bundle.
    */
   public function generate($bundle, $files) {
      if (isDevMode()) {
         $this->devMode($files);
      } else {
         $this->prodMode($bundle, $files);
      }
   }
   
   /**
    * Generate a bundle in development mode.
    * @param $files array[string]
    *    Files in the bundle.
    */
   public function devMode($files) {
      foreach($files as $file) {
         $this->html($this->resolveFileUrl($file));
      }
   }
   
   /**
    * Generate a bundle in produciton mode.
    * @param $bundle string
    *    The name of the bundle.
    * @param $files array[string]
    *    Files in the bundle.
    */
   public function prodMode($bundle, $files) {
      $path = $this->resolveBundlePath($bundle);
      if (!file_exists($path)) {
         $handle = fopen($path, 'w');
         
         foreach($files as $file) {
            $file = $this->resolveFilePath($file);
            fwrite($handle, $this->readFile($file));
         }
         
         fclose($handle);
      }
      
      $this->html($this->resolveBundleUrl($bundle));
   }

}      
   