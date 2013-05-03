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
 * Provide an easy way to store data in files (in the data folder)
 * @author David Reignier
 *
 */
class StoreUtil extends Util {

   /**
    * The StoreUtil use a local cache.
    * @var array[RedBean_OODBBean]
    */
   private $cache = array();

   /**
    * Get a bean for a key
    * @param $name string
    *    A key
    * @param $create boolean 
    *    If true, a new bean is created is the bean can't be found
    * @return RedBean_OODBBean
    *    The bean for the given key.
    */
   private function getBean($name, $create = true) {
      // Try the cache
      if (isset($this->cache[$name])) {
         return $this->cache[$name];
      }
      
      // Try the database
      $bean = R()->findOne('store', 'name = ?', array($name));
      if ($bean && $bean->id != 0) {
         $this->cache[$name] = $bean;
         return $bean;
      }
      
      if (!$create) {
         return null;
      }
      
      // Create a new bean
      $bean = R()->dispense('store');
      $bean->name = $name;
      $this->cache[$name] = $bean;
      return $bean;
   }

   /**
    * Store a value.
    * @param $name string
    * 	The name of the value.
    * @param $value mixed
    *    The name.
    * @param $serialize boolean
    *    If true, the value will be serialized. Optional. true by default.
    */
   public function store($name, $value, $serialize = true) {
      $bean = $this->getBean($name);
      $bean->value = $serialize ? serialize($value) : $value;
   }

   /**
    * Load a stored value.
    * @param $name string
    * 	The name of the value.
    * @return mixed
    * 	The value. Or null if no value with $name is found.
    * @param $unserialize boolean
    *    If true, the value will be unserialized. Optional. true by default.
    */
   public function load($name, $unserialize = true) {
      if (!$bean = $this->getBean($name, false)) {
         return null;
      }
      
      return $unserialize ? unserialize($bean->value) : $bean->value;
   }

   /**
    * Delete a value or a folder of values.
    * @param $name string
    * 	The name of a value or a folder of values.
    */
   public function delete($name) {
      if ($bean = $this->getBean($name, false)) {
         $bean->trash();
         unset($this->cache[$name]);
      }
   }

   /**
    * @param $name string
    * 	The name of a value
    * @return bool
    * 	true if the value is in the store, false otherwise.
    */
   public function exists($name) {
      return $this->getBean($name, false) != null;
   }

   /**
    * Finalize the store
    */
   public function finalize() {
      foreach ($this->cache as $bean) {
         if ($bean->id == 0 || $bean->getMeta('tainted')) {
            R()->store($bean);
         }
      }
   }
}

/**
 * @return StoreUtil
 * 	The StoreUtil
 */
function store() {
   return util('Store');
}
