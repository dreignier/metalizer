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
 * Handle the cache of beans.
 */
class BeanCache extends MetalizerObject {

   /**
    * We use a hot local cache.
    * @var array
    */
   private $cache = array();

   public function __construct() {
      $this->cache = new SimpleCache('bean');
   }
   
   /**
    * @param $bean RedBean_OODB
    *    A bean
    * @return string
    *    The key corresponding to the given bean.
    */
   private function key($bean) {
      return $bean->getMeta('type') . ".$bean->id"; 
   }
   
   /**
    * Store a bean
    * @param $bean RedBean_OODB
    *    A bean
    */
   public function store($bean) {
      $this->cache->store($this->key($bean), $bean);
   }
   
   /**
    * Load a bean
    * @param $type string
    *    The type of the bean
    * @param $id int
    *    The id of the bean
    * @return RedBean_OODB
    *    The bean correspond to the given type and id. Or null, if there's no bean found.
    */
   public function load($type, $id) {
      return $this->cache->load("$type.$id");
   }
   
   /**
    * Remove a bean from the cache.
    * @param $bean RedBean_OODB
    *    The bean to remove
    */
   public function trash($bean) {
      $this->cache->trash($this->key($bean));
   }
   
   /**
    * Remove all bean of a type in the cache
    * @param $type string
    *    A bean type.
    */
   public function trashAll($type) {
      $this->cache->trashAll($type);
   }
   
   public function finalize() {
      $this->cache->finalize();
   }
}