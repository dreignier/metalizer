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
 * We override the Redbean OODB with this class in Metalizer.
 */
class OODB extends RedBean_OODB {

   /**
    * The RedbeanUtil
    * @var RedbeanUtil
    */
   private $util;

   /**
    * @see RedBean_OODB#__construct
    */
   public function __construct($writer) {
      parent::__construct($writer);
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
    * @return Logger
    */
   protected function log() {
      return $this->util->log();
   }

   /**
    * @see RedBean_OODB#load
    */
   public function load($type, $id) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Load $type : $id");
      }
      
      $bean = parent::load($type, $id);

      return $bean;
   }

   /**
    * @see RedBean_OODB#store
    */
   public function store($bean) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Store " . $bean->getMeta('type'));
      }
      
      $result = parent::store($bean);
      
      return $result;
   }

   /**
    * @see RedBean_OODB#trash
    */
   public function trash($bean) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Trash " . $bean->getMeta('type') . ' : ' . $bean->id);
      }
      
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
      return parent::wipe($type);
   }

}