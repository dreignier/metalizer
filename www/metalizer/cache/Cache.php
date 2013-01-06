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
abstract class Cache extends MetalizerObject implements ICache {
   
   /**
    * The subcache.
    * @var ICache
    */
   protected $subcache = null;
   
   /**
    * The name of the cache
    * @var string
    */
   protected $name;
   
   /**
    * The maximum size of the cache in MB
    * @var int
    */
   protected $maxSize;
   
   /**
    * The flush ratio when the cache is full.
    * @var float
    */
   protected $flushRatio;
   
   /**
    * <code>true</code> if the current cache is enabled, <code>false</code> otherwise.
    * @var boolean
    */
   protected $enabled;

   /**
    * Construct a new cache
    * @param string $name
    *    The name of the cache. It is used to retrieve the cache configuration.
    */
   public function __construct($name) {
      $this->name = $name;
      
      $this->maxSize = config("cache.$name.size", config('cache.size')) * 1024 * 1024;
      $this->flushRatio = config("cache.$name.flush_ratio", config('cache.flush_ratio'));
      $this->enabled = config("cache.$name.enabled", config('cache.enabled'));
   }
   
   public function finalize() {
      if ($this->subcache) {
         $this->subcache->finalize();
      }
   }
   
   /**
    * Set the subcache of the current cache
    * @param $cache ICache
    *    The new subcache.
    * @return Cache
    *    $this
    */
   public function setSubcache(ICache $cache) {
      $this->subcache = $cache;
      return $this;
   }
   
   /**
    * @return ICache
    *    The subcache of the current cache. Or null, if the current cache has no subcache.
    */
   public function getSubcache() {
      return $this->subcache;
   }
      
   /**
    * @see ICache#store
    */
   public function store($key, $item, $subcache = true) {
      if ($subcache && $this->subcache) {
         $this->subcache->store($key, $item);   
      }
      
      if (!$this->enabled) {
         return $this;
      }
      
      $this->save($key, $item);
      
      if ($this->maxSize > 0 && $this->size() > $this->maxSize) {
         $this->flush($this->flushRatio);
      }
   }
   
   /**
    * @see ICache#storeAll
    */
   public function storeAll($key, $items) {
      foreach($items as $itemKey => $item) {
         $this->store("$key.$itemKey", $item);
      }
      
      return $this;
   }
   
   /**
    * @see ICache#has
    */
   public function has($key, $subcache = true) {
      if ($this->enabled && $result = $this->test($key)) {
         return true;
      }
      
      if ($subcache && $this->subcache) {
         return $this->subcache->has($key);
      }
      
      return false;
   }
   
   /**
    * @see ICache#hasDirectory
    */
   public function hasDirectory($key) {
      if ($this->enabled && $result = $this->testDirectory($key)) {
         return true;
      }
      
      if ($this->subcache) {
         return $this->subcache->hasDirectory($key);
      }
      
      return false;
   }
   
   /**
    * @see ICache#load
    */
   public function load($key) {
      if ($this->enabled && $this->has($key, false)) {
         return $this->open($key);
      }
      
      if ($this->subcache) {
         $result = $this->subcache->load($key);
         
         if ($result !== null || $this->subcache->has($key)) {
            $this->store($key, $result, false);
         }
         
         return $result;
      }
      
      return null;
   }
   
   /**
    * @see ICache#loadAll
    */
   public function loadAll($key) {
      if (!$this->hasDirectory($key)) {
         return null;
      }
      
      $result = array();
      
      foreach($this->keys($key) as $key) {
         $result[$key] = $this->load($key);
      }
      
      return $result;
   } 
   
   /**
    * @see ICache#keys
    */
   public function keys($key) {
      if (!$this->hasDirectory($key)) {
         return null;
      }
      
      $result = $this->enabled  ? $this->browse($key) : array();
      
      if ($this->subcache && $subkeys = $this->subcache->keys($key)) {
         $result = array_merge($result, $subkeys);
      } 
      
      return $result;
   }
   
   /**
    * @see ICache#trash
    */
   public function trash($key) {
      if ($this->enabled) {
         $this->delete($key);
      }
      
      if ($this->subcache) {
         $this->subcache->trash($key);
      }
            
      return $this;
   }
   
   /**
    * @see ICache#trashAll
    */
   public function trashAll($key) {
      if (!$this->hasDirectory($key)) {
         return $this;
      }
      
      foreach($this->keys($key) as $key) {
         $this->trash($key);
      }
      
      return $this;
   }
    
   
   /**
    * @see ICache#flush
    */
   public function flush($ratio = 1.0, $subcache = true) {
      if ($subcache && $this->subcache) {
         $this->subcache->flush($ratio);
      }
      
      if (!$this->enabled) {
         return $this;
      }
      
      $this->nuke($ratio);
   }
   
   /**
    * @return boolean
    *    <code>true</code> if the current cache is enabled, <code>false</code> otherwise. A disabled cache do nothing and don't call its subcache.
    */
   public function isEnabled() {
      return $this->enabled;
   }
   
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got an item associated to the given key, <code>false</code> otherwise.
    */
   abstract protected function test($key);
   
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got a directory associated to the given key, <code>false</code> otherwise.
    */
   abstract protected function testDirectory($key);
   
   /**
    * @param $key string
    *    A key to a directory
    * @return array[string]
    *    All the key in the given directory
    */
   abstract protected function browse($key);
   
   /**
    * Open a value
    * @param $key string
    *    The key of the value to open
    * @return mixed
    *    The value associated to the given key.
    */
   abstract protected function open($key);
   
   /**
    * Save a value in the cache
    * @param $key string
    *    A key
    * @param $value mixed
    *    The value to save.
    */
   abstract protected function save($key, $value);
   
   /**
    * Delete a value or a directory from the cache
    * @param $key string
    *    A key
    */
   abstract protected function delete($key);
   
   /**
    * Nuke a part of the cache.
    * @param $ratio float
    *    The ratio of the cache to nuke.
    */
   abstract protected function nuke($ratio);
   
   /**
    * @return int
    *    The size of the current cache in byte.
    */
   abstract protected function size();
}
