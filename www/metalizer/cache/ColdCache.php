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
class ColdCache extends Cache {
      
   /**
    * The index of the cache
    * @var ColdCache_Index
    */
   protected $index;
   
   public function __construct($name) {
      parent::__construct($name);
      
      if (isDevMode()) {
         $this->enabled = false;
      }
      
      if (file_exists($this->getIndexFilePath())) {
         if ($this->log()->isInfoEnabled()) {
            $this->log()->info('Load index');
         }
         $this->index = unserialize(file_get_contents($this->getIndexFilePath()));
      } else {
         if ($this->log()->isInfoEnabled()) {
            $this->log()->info('Create a new index');
         }
         $this->index = new ColdCache_Index();
      }
   }
   
   /**
    * @return string
    *    The path to the index file.
    */
   protected function getIndexFilePath() {
      return PATH_CACHE . 'index/' . str_replace('_cold', '', $this->name);
   }
   
   public function finalize() {
      parent::finalize();
      
      if (!$this->enabled) {
         return;
      }
      
      $dir = PATH_CACHE . 'index';
      if (!is_dir($dir)) {
         mkdir($dir);
      }
      
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug('Store the index in ' . $this->getIndexFilePath());
      }
      file_put_contents($this->getIndexFilePath(), serialize($this->index));
   }
   
   /**
    * @return string
    *    The root directory of the current cache
    */
   protected function getRoot() {
      return PATH_CACHE . str_replace('_cold', '', $this->name) . '/';
   }
   
   /**
    * @param $key string
    *    A key 
    * @return string
    *    The path to the file correspond to the given key
    */
   protected function getPath($key) {
      return $this->getRoot() . str_replace('.', '/', $key);
   }
      
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got an item associated to the given key, <code>false</code> otherwise.
    */
   protected function test($key) {
      $key = $this->getPath($key);
      
      return file_exists($key) && !is_dir($key);
   }
   
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got a directory associated to the given key, <code>false</code> otherwise.
    */
   protected function testDirectory($key) {
      return is_dir($this->getPath($key));
   }
   
   /**
    * @param $key string
    *    A key to a directory
    * @return array[string]
    *    All the key in the given directory
    */
   protected function browse($key) {
      $result = array();
      
      $directory = $this->getPath($key);
      $handle = opendir($directory);
      while ($file = readdir($handle)) {
         if ($file != '.' && $file != '..') {
            if (is_dir("$directory/$file")) {
               $result = array_merge($result, $this->browse("$key.$file"));
            } else {
               $result[] = "$key.$file";
            }
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
      $this->index->refresh($key);
      
      return unserialize(file_get_contents($this->getPath($key)));
   }
   
   /**
    * Save a value in the cache
    * @param $key string
    *    A key
    * @param $value mixed
    *    The value to save.
    */
   protected function save($key, $value) {
      $serialized = serialize($value);
      
      if ($this->test($key)) {
         $this->index->refresh($key, strlen($serialized));
      } else {
         $this->index->store($key, strlen($serialized));
      }
      
      $path = $this->getPath($key);
      _file()->checkDirectory($path);
      file_put_contents($path, $serialized);
   }
   
   /**
    * Delete a value or a directory from the cache
    * @param $key string
    *    A key
    */
   protected function delete($key) {
      $this->index->remove($key);
      
      unlink($this->getPath($key));
   }
   
   /**
    * Nuke a part of the cache.
    * @param $ratio float
    *    The ratio of the cache to nuke.
    */
   protected function nuke($ratio) {
      if ($ratio == 1.0) {
         // A 100% nuke is easier
         $this->index = new ColdCache_Index();
         
         _file()->rmdir($this->getRoot());
         mkdir($this->getRoot());
      } else {
         $size = $this->index->size();
         
         $hits = $this->index->hits();
         $index = sizeof($hits) - 1;
         while ($this->index->size() > $size * $ratio) {
            $this->trash($hits[$index--]);
         }
      }
   }
   
   /**
    * @return int
    *    The size of the current cache in byte.
    */
   protected function size() {
      return $this->index->size();
   }
      
}

class ColdCache_Index extends MetalizerObject {
   
   /**
    * We keep all entries of the cache ordered by last hit
    * @var array
    */
   protected $hits = array();
   
   /**
    * We keep all entries size.
    * @var array
    */
   protected $sizes = array();
      
   /**
    * The current size of the cache
    * @var int 
    */
   protected $size = 0;
   
   /**
    * @return int
    *    The size of the index
    */
   public function size() {
      return $this->size;
   }
   
   /**
    * @return array
    *    The hits of the index
    */
   public function hits() {
      return $this->hits;
   }
   
   /**
    * Store a new entry
    * @param $key string
    *    The key of the entry
    * @param $size int
    *    The size of the entry
    */
   public function store($key, $size) {
      $this->sizes[$key] = $size;
      $this->size += $size;
      
      // Insert the new hit in the first place
      for ($index = sizeof($this->hits); $index > 0; --$index) {
         $this->hits[$index] = $this->hits[$index - 1];
      }
      
      $this->hits[0] = $key;
   }
   
   /**
    * @param $key string
    *    A key
    * @return int
    *    Find the position of a key in hits
    */
   protected function findHit($key) {
      foreach ($this->hits as $position => $hit) {
         if ($hit == $key) {
            return $position;
         }
      }
      
      return -1; // Not found.
   }
   
   /**
    * Refresh an entry
    * @param $key string
    *    The key of the entry
    * @param $size int
    *    The size of the entry
    */
   public function refresh($key, $size = null) {
      if ($size !== null) {
         $this->size = $this->size - $this->sizes[$key] + $size;
         $this->sizes[$key] = $size;
      }
      
      // Move the hit to the first place
      for ($index = $this->findHit($key); $index > 0; --$index) {
         $this->hits[$index] = $this->hits[$index - 1];
      }
      
      $this->hits[0] = $key;
   }
   
   /**
    * Remove an entry from the index
    * @param $key string
    *    The key of the entry
    */
   public function remove($key) {
      $this->size -= $this->sizes[$key];
      unset($this->sizes[$key]);
      
      // Remove the hit and repair the array
      $length = sizeof($this->hits);
      for ($index = $this->findHit($key); $index < $length - 1; ++$index) {
         $this->hits[$index] = $this->hits[$index + 1];
      }
      
      unset($this->hits[$length - 1]);
   }
   
 }
