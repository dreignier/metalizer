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
 * @author David Reignier
 */
class HotCache extends Cache {
   
   /**
    * The HotCache use an array to store items.
    * @var HotCache_Directory
    */
   protected $cache;
   
   public function __construct($name) {
      parent::__construct($name);
      $this->cache = new HotCache_Directory();
      $this->maxSize = 0;
   }
   
   /**
    * @param $key string
    *    A key
    * @return array[string]
    *    The given key, splitted
    */
   protected function split($key) {
      return explode('.', $key);
   }
   
   /**
    * @param $value mixed
    *    A value
    * @return boolean
    *    <code>true</code> if the given value is a directory, <code>false</code> otherwise
    */
   protected function isDirectory($value) {
      return is_object($value) && is_a($value, 'HotCache_Directory'); 
   }
   
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got an item associated to the given key, <code>false</code> otherwise.
    */
   protected function test($key) {
      $cache = $this->cache;
      
      foreach ($this->split($key) as $key) {
         if (!$this->isDirectory($cache) || !isset($cache[$key])) {
            return false;
         }
         
         $cache = $cache[$key];           
      }
      
      return !$this->isDirectory($cache);
   }
   
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got a directory associated to the given key, <code>false</code> otherwise.
    */
   protected function testDirectory($key) {
      $cache = $this->cache;
      
      foreach ($this->split($key) as $key) {
         if (!$this->isDirectory($cache) || !isset($cache[$key])) {
            return false;
         }
         
         $cache = $cache[$key];           
      }
      
      return $this->isDirectory($key);
   } 
   
   /**
    * @param $key string
    *    A key
    * @return mixed
    *    The value or directory associated with the key
    */
   protected function get($key) {
      $result = $this->cache;
      
      foreach ($this->split($key) as $key) {
         $result = $result[$key];
      }
      
      return $result;
   }
   
   /**
    * @param $key string
    *    A key to a directory
    * @return array[string]
    *    All the key in the given directory
    */
   protected function browse($key) {
      $result = array();
      
      foreach ($this->get($key) as $itemKey => $item) {
         if ($this->isDirectory($item)) {
            $result = array_merge($this->browse("$key.$itemKey"));
         } else {
            $result[] = "$key.$itemKey";
         }
      }
      
      return $result;
   }
   
   /**
    * Open a value
    * @param $key string
    *    The key of the value to open
    * @return mixed
    *    The value associated to the given key.
    */
   protected function open($key) {
      return $this->get($key);
   }
   
   /**
    * Save a value in the cache
    * @param $key string
    *    A key
    * @param $value mixed
    *    The value to save.
    */
   protected function save($key, $value) {
      $keys = $this->split($key);
      $last = array_pop($keys);
      $cache = $this->cache;
      
      foreach($keys as $key) {
         if (!isset($cache[$key])) {
            $cache[$key] = new HotCache_Directory();
         }
         
         $cache = $cache[$key];
      }
      
      $cache[$last] = $value;
   }
   
   /**
    * Delete a value or a directory from the cache
    * @param $key string
    *    A key
    */
   protected function delete($key) {
      $keys = $this->split($key);
      $last = array_pop($keys);
      $cache = $this->cache;
      
      foreach($keys as $key) {
         $cache = $cache[$key];
      }
      
      unset($cache[$last]);
   }
   
   /**
    * Nuke a part of the cache.
    * @param $ratio float
    *    The ratio of the cache to nuke.
    */
   protected function nuke($ratio) {
      // A HotCache never nuke itself
   }
   
   /**
    * @return int
    *    The size of the current cache in byte.
    */
   protected function size() {
      // A HotCache never nuke itself
      return 0;
   }
}

/**
 * Private class
 * A HotCache directory
 */
class HotCache_Directory extends MetalizerObject implements ArrayAccess {
   
   /**
    * The directory content
    * @var array
    */
   private $directory = array();
   
   /**
    * @see ArrayAccess#offsetExists
    */
   public function offsetExists($offset) {
      return isset($this->directory[$offset]);
   }
   
   /**
    * @see ArrayAccess#offsetGet
    */
   public function offsetGet($offset) {
      return isset($this->directory[$offset]) ? $this->directory[$offset] : null;
   } 
   
   /**
    * @see ArrayAccess#offsetSet
    */
   public function offsetSet($offset, $value) {
      $this->directory[$offset] = $value;
   }
   
   /**
    * @see ArrayAccess#offsetUnset
    */
   public function offsetUnset($offset) {
      unset($this->directory[$offset]);
   }
} 
