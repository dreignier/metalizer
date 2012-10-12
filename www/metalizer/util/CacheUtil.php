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
 * Handle the cache. Can be access by the cache() function. See cache.php for configuration values.
 * In development mode, the cache do nothing.
 *
 * Value names should be like 'foo.bar.some_stuff.my_value'.
 *
 * @author David Reignier
 *
 * @todo Clean the entire cache when it's needed
 */
class CacheUtil extends Util {

   public function __construct() {
      // If cache is disabled, we clear the cache folder.
      if (!$this->isEnabled()) {
         util('File')->rmdir(PATH_CACHE);
         mkdir(PATH_CACHE);
      }
   }

   /**
    * Get the path for a file.
    * @param $name string
    * 	The name of the file.
    * @return string
    * 	The path to the file.
    */
   private function getFilePath($name) {
      return PATH_CACHE . str_replace('.', '/', $name);
   }
   
   /**
    * @return bool
    *    true is cache is enabled, false otherwise.
    */
   public function isEnabled() {
      return (config('cache.enabled') && !isDevMode());
   }

   /**
    * Put a value in the cache.
    * @param $name string
    * 	The name of the value.
    * @param $value mixed
    * 	The value. Must be serializable.
    */
   public function put($name, $value) {
      if (!$this->isEnabled()) {
         return;
      }

      $file = $this->getFilePath($name);

      util('File')->checkDirectory($file);

      $value = serialize($value);
      file_put_contents($file, $value);
   }

   /**
    * @param $name string
    * 	The name of a value
    * @return bool
    * 	true if the value is in the cache, false otherwise.
    */
   public function exists($name) {
      if (!$this->isEnabled()) {
         return false;
      }
      
      $file = $this->getFilePath($name);

      return file_exists($file);
   }

   /**
    * Retrieve a value.
    * @param $name string
    * 	The name of a value
    * @return mixed
    * 	The value, or null if the value is not in the cache.
    */
   public function get($name) {
      if (!$this->isEnabled()) {
         return null;
      }

      if (!$this->exists($name)) {
         $this->log()->warning("There's no value with the name '$name' in the cache");
         return null;
      }

      $file = $this->getFilePath($name);
      $result = file_get_contents($file);
      $result = unserialize($result);

      return $result;
   }

   /**
    * Clean a value from the cache.
    * @param $name string
    * 	The name of a value. It can be a subname of a value.
    */
   public function clean($name) {
      if (!$this->isEnabled()) {
         return;
      }
      
      $file = $this->getFilePath($name);
      
      if ($this->exists($name)) {
         unlink($file);
         return;
      }

      // Maybe it's a folder
      if (is_dir($file)) {
         rmdir($file);
         return;
      }

      $this->log()->warning("A clean for '$name' is called, but there's nothing to clean here");
   }

}

/**
 * Access to the CacheUtil.
 * @return CacheUtil
 * 	The CacheUtil.
 */
function cache() {
   return util('Cache');
}
