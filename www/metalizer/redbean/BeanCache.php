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

   /**
    * Save the hot cache in the cold cache
    */
   public function onSleep() {
      foreach ($this->cache as $type => $beans) {
         foreach ($beans as $id => $bean) {
            // Don't cache a tainted bean.
            if (!$bean->getMeta('tainted')) {
               $bean->sleep();
               cache()->put("metalizer.model.bean.$type.$id", $bean);
            }
         }
      }
   }   

   /**
    * Put or update a bean in the cache. The bean must be registered and valid.
    * @param $bean RedBean_OODBBean
    *    A bean.
    */
   public function put($bean) {
      if (!$bean->id || !$bean->getMeta('type')) {
         return;
      }

      $type = $bean->getMeta('type');
      $id = $bean->id;

      if (!isset($this->cache[$type])) {
         $this->cache[$type] = array();
      }

      $this->cache[$type][$id] = $bean;
   }

   /**
    * Try to get a bean
    * @param $type string
    *    A bean type
    * @param $id integer
    *    A bean id
    * @return RedBean_OODBBean
    *    The corresponding bean. Or null if the cache can't found the bean.
    */
   public function get($type, $id) {
      // Try the hot cache
      if (isset($this->cache[$type][$id])) {
         return $this->cache[$type][$id];
      }

      // Try the cold cache
      if ($bean = cache()->get("metalizer.model.bean.$type.$id")) {
         $this->put($bean);
         return $bean;
      }

      // Not found
      return null;
   }

   /**
    * Clean a part of the cache.
    * @param $type string
    *    A bean type.
    * @param $id integer
    *    A bean id. Optional. If id is missing, we clean all the beans for the given type.
    */
   public function clean($type, $id = 0) {
      if ($id) {
         if (isset($this->cache[$type]) && isset($this->cache[$type][$id])) {
            unset($this->cache[$type][$id]);
         }

         cache()->clean("metalizer.model.bean.$type.$id");
      } else {
         if (isset($this->cache[$type])) {
            unset($this->cache[$type]);
         }

         cache()->clean("metalizer.model.bean.$type");
      }
   }

   /**
    * Remove the bean from the cache. The bean must be registered and valid.
    * @param $bean RedBean_OODBBean
    *    A bean.
    */
   public function remove($bean) {
      if (!$bean->id || !$bean->getMeta('type')) {
         return;
      }

      $this->clean($bean->getMeta('type'), $bean->id);
   }

}