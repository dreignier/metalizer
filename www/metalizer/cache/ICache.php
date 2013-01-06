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
interface ICache {
   
   /**
    * Store an item in the cache
    * @param $key string
    *    A key
    * @param $item mixed
    *    The item to store.
    * @return ICache
    *    $this
    */
   public function store($key, $item);
   
   /**
    * Store all given in the cache
    * @param $key string
    *    A key.
    * @param $items array
    *    The items to store.
    * @return ICache
    *    $this
    */
   public function storeAll($key, $items);
   
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got an item with the given key, <code>false</code> otherwise.
    */
   public function has($key);
   
   /**
    * @param $key string
    *    A key
    * @return boolean
    *    <code>true</code> if the current cache got a directory with the given key, <code>false</code> otherwise.
    */
   public function hasDirectory($key);
   
   /**
    * @param $key string
    *    A key
    * @return mixed
    *    The item in the cache corresponding to the given key. Note that if there's no item associated with the given key, it return <code>null</code>.
    *    But it also can return <code>null<code> if you associated a <code>null</code> value with a key. For instance :
    *    <code>$cache->store('test', null)->get('test');</code> returns <code>null</code>.
    *    <code>$cache->store('test', null)->has('test');</code> returns <code>true</code>.
    */
   public function load($key);
   
   /**
    * Load all items in a directory.
    * @param $key string
    *    A key to a directory
    * @return array[mixed]
    *    An array of all items in the given directory. If the current key is not a directory key, it returns null.
    */
   public function loadAll($key);
   
   /**
    * Remove an item from the cache.
    * @param $key string
    *    A key
    * @return ICache
    *    $this
    */
   public function trash($key);
   
   /**
    * Remove a directory from the cache.
    * @param $key string
    *    A key to a directory
    * @return ICache
    *    $this
    */
   public function trashAll($key);
      
   /**
    * @param $key string
    *    A key
    * @return array[string]
    *    If the current key is a directory key, the result if an array of all keys in the directory. If the current key is not a directory key, it returns null.
    */
   public function keys($key);
   
   /**
    * Flush the cache.
    * @param $ratio float
    *    The ratio of the flush. Optional. <code>1.0</code> by default (means 100%). 
    * @return ICache
    *    $this
    */
   public function flush($ratio = 1.0);
   
}
