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
    * Called only in production. The bundle generator can process the bundle file a last time.
    * @param $bundlePath string
    *    The path to a bundle file.
    */
   abstract public function afterGenerate($bundlePath);

   /**
    * Must generate the good html code (with echo) for an url.
    * @param $url string
    *    A valid url.
    */
   abstract public function html($url);

   /**
    * We must keep the handled file to avoid double handling on a file.
    * @var array[string]
    */
   private $files = array();

   /**
    * The default processor for the bundle.
    * It must be a valid processor, eg. the configuration value "bundle.processor.<processor>" must be a valid BundleFileProcessor subclass.
    * @var string
    */
   private $processor;

   /**
    * The file extension for the bundle file.
    * @var string
    */
   private $extension;

   /**
    * Construct a new BundleGenerator
    * @param $extension string
    *    The file extension for the bundle file. Without the dot.
    * @param $processor string
    *    The default processor for the bundle. Optional. 'default' by default.
    */
   public function __construct($extension, $processor = 'default') {
      $this->extension = $extension;
      $this->processor = $processor;
   }

   /**
    * Find the processor in a pattern. If a pattern is like '<processor>:<pattern>', then the processor will be <processor>.
    * @param $pattern string
    *    A pattern.
    * @return BundleFileProcessor
    *    The processor for the given pattern.
    */
   protected function findProcessor($pattern) {
      $colonPos = strpos($pattern, ':');
      if ($colonPos !== false) {
         $processor = substr($pattern, 0, $colonPos);
      } else {
         $processor = $this->processor;
      }

      $class = config("bundle.processor.$processor");

      if (!$class || !is_subclass_of($class, "BundleFileProcessor")) {
         throw new BundleException("$processor is not a valid file processor");
      }

      return new $class($processor);
   }

   /**
    * @param $bundle string
    *    A bundle name.
    * @return string
    *    The path to the final file for the given bundle.
    */
   protected function path($bundle) {
      return PATH_RESOURCE_GEN . "$bundle.$this->extension";
   }

   /**
    * Generate a bundle.
    * @param $bundle string
    *    The bundle name. Used for the final bundle file in production.
    * @param $patterns array[string]
    *    All the patterns for the bundle.
    */
   public function generate($bundle, $patterns) {
      if (isDevMode()) {
         $this->devGenerate($bundle, $patterns);
      } else {
         $this->prodGenerate($bundle, $patterns);
      }
   }

   /**
    * Generate in development mode.
    * @param $bundle string
    *    The bundle name. Used for the final bundle file in production.
    * @param $patterns array[string]
    *    All the patterns for the bundle.
    */
   protected function devGenerate($bundle, $patterns) {
      foreach ($patterns as $pattern) {
         $processor = $this->findProcessor($pattern);
         $pattern = str_replace($processor->getName() . ':', '', $pattern);
         $processor->initialize($pattern);
         foreach (_file()->glob($processor->path($pattern)) as $path) {
            if (!$processor->isValid($path)) {
               continue;
            }
            
            if ($this->log()->isTraceEnabled()) {
               $this->log()->trace("Process $path with $processor");
            }

            if (!in_array($path, $this->files)) {
               $this->html($processor->url($path));
               $this->files[] = $path;
            }
         }
      }
   }

   /**
    * Generate in production mode.
    * @param $bundle string
    *    The bundle name. Used for the final bundle file in production.
    * @param $patterns array[string]
    *    All the patterns for the bundle.
    */
   protected function prodGenerate($bundle, $patterns) {
      $bundlePath = $this->path($bundle);

      if (!file_exists($bundlePath)) {
         if ($this->log()->isInfoEnabled()) {
            $this->log()->info("Generate $bundlePath");
         }
         
         _file()->checkDirectory($bundlePath);
         $handle = fopen($bundlePath, 'w');
         foreach ($patterns as $pattern) {
            $processor = $this->findProcessor($pattern);
            $pattern = str_replace($processor->getName() . ':', '', $pattern);
            $processor->initialize($pattern);
            foreach (_file()->glob($processor->path($pattern)) as $path) {
               if (!$processor->isValid($path)) {
                  continue;
               }
               
               if ($this->log()->isTraceEnabled()) {
                  $this->log()->trace("Process $path with $processor");
               }  

               if (!in_array($path, $this->files)) {
                  $content = $processor->read($path);
                  fwrite($handle, $content);
                  $this->files[] = $path;
               }
            }
         }
         fclose($handle);

         $this->afterGenerate($bundlePath);
      }
      $this->html(resUrl($this->path($bundle), false));
   }

}
