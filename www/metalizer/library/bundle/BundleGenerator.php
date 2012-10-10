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

   abstract public function finalize($bundlePath);
   
   abstract public function html($url);

   private $files = array();
   private $processor;
   private $extension;

   public function __construct($extension, $processor = 'default') {
      $this->extension = $extension;
      $this->processor = $processor;
   }
   
   protected function findProcessor($pattern) {
      $colonPos = strpos($pattern, ':');
      if ($colonPos !== false) {
         $processor = substr($pattern, 0, $colonPos);
      } else {
         $processor = $this->processor;
      }
      
      $class = config("bundle.processor.$processor");
      
      if (!is_subclass_of($class, "BundleFileProcessor")) {
         throw new BundleException("$processor is not a valid file processor");
      }
      
      return new $class($processor);
   }
   
   protected function path($bundle) {
      return PATH_RESOURCE_BUNDLE . "$bundle.$this->extension";
   }
   
   public function generate($bundle, $patterns) {
      if (isDevMode()) {
         $this->devGenerate($bundle, $patterns);
      } else {
         $this->prodGenerate($bundle, $patterns);
      }
   }
   
   public function devGenerate($bundle, $patterns) {
      foreach ($patterns as $pattern) {
         $processor = $this->findProcessor($pattern);
         $pattern = str_replace($processor->getName() . ':', '', $pattern);
         foreach (glob($processor->path($pattern)) as $path) {
            if (!in_array($path, $this->files)) {
               $this->html($processor->url($path));
               $this->files[] = $path;
            }
         }
      }
   }
   
   public function prodGenerate($bundle, $patterns) {
      $bundlePath = $this->path($bundle);
      
      if (!file_exists($bundlePath)) {
         util('File')->checkDirecoty($bundlePath);
         $handle = fopen($bundlePath, 'w');
         foreach ($patterns as $pattern) {
            $processor = $this->findProcessor($pattern);
            $pattern = str_replace($processor->getName() . ':', '', $pattern);
            foreach (glob($processor->path($pattern)) as $path) {
               if (!in_array($path, $this->files)) {
                  $content = $processor->read($path);
                  fwrite($handle, $content);
                  $this->files[] = $path;
               }
            }
         }
         fclose($handle);
         
         $this->finalize($bundlePath);
      }
      
      $this->html(resUrl($this->path($bundle), false));
   }

}      
   