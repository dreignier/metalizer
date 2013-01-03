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
 * We override the Redbean facade with this class in Metalizer.
 * This facade can use the Metalizer cache for beans.
 */
class Facade extends RedBean_OODB {

   /**
    * The RedbeanUtil
    * @var RedbeanUtil
    */
   private $util;

   /**
    * The bean cache
    * @var RedbeanUtil_BeanCache
    */
   private $cache;

   /**
    * @see RedBean_OODB#__construct
    */
   public function __construct($writer) {
      parent::__construct($writer);
   }

   /**
    * Set the cache
    * @param $cache RedbeanUtil_BeanCache
    *    The cache to use.
    */
   public function setCache($cache) {
      $this->cache = $cache;
   }

   /**
    * Set the util
    * @param $util RedbeanUtil
    *    The util to use.
    */
   public function setUtil($util) {
      $this->util = $util;
   }

   /**
    * We are not in a MetalizerObject. So we use the logger of RedbeanUtil.
    */
   protected function log() {
      return $this->util->log();
   }

   /**
    * @see RedBean_OODB#load
    */
   public function load($type, $id) {
      if ($bean = $this->cache->get($type, $id)) {
         return $bean;
      }

      $bean = parent::load($type, $id);

      $this->cache->put($bean);

      return $bean;
   }

   /**
    * @see RedBean_OODB#store
    */
   public function store($bean) {
      $result = parent::store($bean);
      
      $this->cache->put($bean);

      return $result;
   }

   /**
    * @see RedBean_OODB#trash
    */
   public function trash($bean) {
      $this->cache->remove($bean);

      parent::trash($bean);
   }

   /**
    * @see RedBean_OODB#batch
    */
   public function batch($type, $ids) {
      return parent::batch($type, $ids);
   }

   /**
    * @see RedBean_OODB#wipe
    */
   public function wipe($type) {
      $this->cache->clean($type);

      return parent::wipe($type);
   }

}