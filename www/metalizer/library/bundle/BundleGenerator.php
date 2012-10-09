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
    * Convert a file path to an url to the file.
    * @param $path string
    *    A path to a file.
    * @return string
    *    The url to the given file.
    */
   abstract public function filePathToUrl($path);
   
   /**
    * Read the content of a file.
    * @param $file string
    *    The complete path to a file.
    */
   abstract public function readFile($file);

   /**
    * We keep files to avoid adding them more than one.
    */   
   private $files = array();
   
   /**
    * Minify the given file. Do nothing by default.
    */
   public function minify($file) {
      
   }
   
   /**
    * Generate the bundle. It call devMode or prodMode according to the isDevMode() value.
    * @param $bundle string
    *    The name of the bundle.
    * @param $patterns array[string]
    *    Files in the bundle. Can be a glob pattern.
    */
   public function generate($bundle, $patterns) {
      if (isDevMode()) {
         $this->devMode($patterns);
      } else {
         $this->prodMode($bundle, $patterns);
      }
   }
   
   /**
    * Generate a bundle in development mode.
    * @param $files array[string]
    *    Files in the bundle.
    */
   public function devMode($patterns) {
      foreach($patterns as $pattern) {
         foreach(glob($this->resolveFilePath($pattern)) as $file) {
            if (!in_array($file, $this->files)) {
               $this->html($this->filePathToUrl($file));
               $this->files[] = $file;   
            }
         }
      }
   }
   
   /**
    * Generate a bundle in production mode.
    * @param $bundle string
    *    The name of the bundle.
    * @param $patterns array[string]
    *    Files in the bundle. Can be a glob pattern.
    */
   public function prodMode($bundle, $patterns) {
      $path = $this->resolveBundlePath($bundle);
      if (!file_exists($path)) {
         $handle = fopen($path, 'w');
         
         foreach($patterns as $pattern) {
            foreach(glob($this->resolveFilePath($pattern)) as $file) {
               if (!in_array($file, $this->files)) {
                  fwrite($handle, $this->readFile($file));
                  $this->files[] = $file;   
               }
            }
         }
         
         fclose($handle);
         
         $this->minify($path);
      }
      
      $this->html($this->resolveBundleUrl($bundle));
   }

}      
   