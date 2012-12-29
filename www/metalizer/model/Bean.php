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
 * Redbean beans are converted to this class in Metalizer.
 * @author David Reignier
 *
 */
class Bean extends RedBean_SimpleModel {

   /**
    * The model of the current bean
    * @var Model
    */
   private $model;
   
   /**
    * @param $model Model
    *    The new model of the bean.
    */ 
   public function setModel($model) {
      $this->model = $model;
   }
   
   /**
    * Delegate the fetchAs method to the internal bean.
    * @see RedBean_OODBBean#fetchAs
    */
   public function fetchAs($type) {
      return $this->unbox()->fetchAs($type);
   }
   
   /**
    * @return Model
    *    The Model of the current bean
    */
   public function getModel() {
      if (!$this->model) {
         $this->model = model($this->metalizerClass)->wrap($this);
      }

      return $this->model;
   }

   /**
    * Called after a "load"
    */
   public function open() {
      $this->getModel()->afterLoad();
   }

   /**
    * Called before a "dispense"
    */
   public function dispense() {
      // We got nothing to do here, the model is not initialized.
   }

   /**
    * Called before a "store"
    */
   public function update() {
      $this->getModel()->beforeUpdate();
   }

   /**
    * Called after a "store"
    */
   public function after_update() {
      $this->getModel()->afterUpdate();
   }


   /**
    * Called before a "trash"
    */
   public function delete() {
      $this->getModel()->beforeDelete();
      // We must delete the current model in its factory.
      $this->getModel()->getFactory()->beforeTrash();
   }

   /**
    * Called after a "trash"
    */
   public function after_delete() {
      $this->getModel()->afterDelete();
   }

}
