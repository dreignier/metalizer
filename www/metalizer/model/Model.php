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
 * Represent a model object.
 * @author David Reignier
 *
 */
abstract class Model extends MetalizerObject {

   /**
    * The redbean bean used by the model.
    * @var RedBean_OODBBean
    */
   protected $bean;

   /**
    * A local cache of fetched properties.
    * @var array[Model]
    */
   protected $fetched = array();

   public function __construct() {
      list(, $caller) = debug_backtrace(false);

      if (!isset($caller['class']) || !($caller['class'] == 'ModelFactory' || is_subclass_of($caller['class'], 'ModelFactory'))) {
         $class = $this->getClass();
         throw new ModelException("You can't construct a new model, you must use <code>model('$class')->dispense();</code>");
      }
   }
   
   public function toString() {
      return $this->getClass() . ':' . $this->getId();
   }
   
   public function getLogName() {
      return $this->getClass() . ':' . $this->getId();
   }

   /**
    * Called after a "load"
    */
   public function afterLoad() {

   }

   /**
    * Called after a "dispense"
    */
   public function afterDispense() {

   }

   /**
    * Called on a "store"
    */
   public function beforeUpdate() {
   }

   /**
    * Called after a "store"
    */
   public function afterUpdate() {

   }

   /**
    * Called on a "trash"
    */
   public function beforeDelete() {

   }

   /**
    * Called after a "trash"
    */
   public function afterDelete() {

   }

   /**
    * @return bool
    *    true if the model is already stored in the database, false otherwise.
    *    Note that a stored object can still be out of date in the database.
    */
   public function isStored() {
      return $this->getId() != 0;
   }

   /**
    * Get the redbean bean of this model.
    * @return RedBean_OODBBean
    *	 The redbean of this model.
    */
   public function getBean() {
      return $this->bean;
   }

   /**
    * Set the redbean bean of this model.
    * This method should _NOT_ be used by an other class than ModelFactory.
    * @param $bean RedBean_OODBBean
    *		The new redbean bean for this model.
    */
   public function setBean($bean) {
      $this->bean = $bean;
   }

   /**
    * Get the current id of the model.
    * @return int
    * 	The current id of the model.
    */
   public function getId() {
      return $this->bean->id;
   }

   /**
    * Store or update the current model in the database.
    * @return Model
    *    $this
    */
   public function store() {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Store called");
      }
      
      return $this->getFactory()->store($this);
   }

   /**
    * Delete the current model in the database and caches.
    * @return Model
    *    $this
    */
   public function trash() {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Trash called");
      }
      
      return $this->getFactory()->trash($this);
   }

   /**
    * @return ModelFactory
    * 	The factory for this model.
    */
   public function getFactory() {
      return model($this->getClass());
   }

   /**
    * A generic setter for models classes.
    * If the current model got a method called 'validate$name', the method is called to validate the given value.
    * @param $name string
    *    The name of the field to set
    * @param $value mixed
    *    The new value of the field
    * @return Model
    *    $this
    */
   protected function set($name, $value) {
      $reflection = new ReflectionClass($this->getClass());
      $method = 'validate' . ucfirst($name);
      if ($reflection->hasMethod($method)) {
         if (!call_user_func(array($this, $method), $value)) {
            throw new ModelValidationException($name, $value);
         }
      }

      if (is_object($value) && $value instanceof Model) {
         $this->fetched[$name] = $value;
         $value = $value->getBean();
      }

      $this->bean->$name = $value;

      return $this;
   }

   /**
    * A generic getter for models classes.
    * @param $name string
    *    The name of the field to get
    * @return mixed
    *    The value of the wanted field.
    */
   protected function get($name) {
      return $this->bean->$name;
   }

   /**
    * Fetch a member in the bean as a class.
    * @param $class string
    *    A model class.
    * @return mixed
    *    The given property fetched as the given class.
    */
   protected function fetchAs($class, $name) {
      if (!isset($this->fetched[$name])) {
         // Get the table class
         $table = model($class)->getTable();
         $this->fetched[$name] = $this->wrap($this->bean->fetchAs($table)->$name);
      }

      return $this->fetched[$name];
   }

   /**
    * Wrap a redbean in a Model, using the current Model factory.
    * @param $bean RedBean_OODBBean
    *    A redbean
    * @return Model
    *    The given bean boxed in a Model.
    */
   public function wrap($bean) {
      return $this->getFactory()->wrap($bean);
   }
   
   /**
    * Get the intermediate bean for an association.
    * @param $name string
    *    The name of the association.
    * @return RedBean_OODBBean
    *    The intermediate bean for the given association.
    */
   public function getIntermediate($name) {
      $name = 'metalizerIntermediate' . ucfirst($name);
      
      $intermediate = $this->bean->fetchAs('metalizerintermediate')->$name;
      
      if (!$intermediate || !$intermediate->id) {
         $intermediate = R()->dispense('metalizerintermediate');
         $this->bean->$name = $intermediate;
      } 
      
      return $intermediate;
   }
    
   /**
    * Associate the current model with an other model.
    * @param $name string
    *    The name of the association.
    * @param $model Model
    *    The other model.
    * @return Model
    *    $this
    */
   protected function associate($name, $model) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("associate $model in $name");
      }
      
      $this->getFactory()->associate($name, $this, $model);
      return $this;
   }
   
   /**
    * Retrieve all associated models with the current model.
    * @param $name string
    *    The name of the association.
    * @param $sql string
    *    The WHERE part of the query. Optional, empty by default.
    * @param $params array
    *    The parameters for the $sql. Optional, empty by default.
    * @return array[Model]
    *    All related models for the current model in the given association
    */
   protected function related($name, $sql = '', $params = array()) {
      return $this->getFactory()->related($name, $this, $sql, $params);
   }
   
   /**
    * Unassociate a model from this bean.
    * @param $name string
    *    he name of the association.
    * @param $model Model
    *    The model to unassociate
    * @param $delete boolean
    *    Option. If true, $model is trashed. <code>false</code> by default.
    * @return Model
    *    $this
    */
   protected function unassociate($name, $model, $delete = false) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("unassociate $model in $name");
      }
      
      $this->getFactory()->unassociate($name, $this, $model, $delete);
      return $this;
   }
   
   /**
    * Unassociate many models from the current model.
    * @param $name string
    *    he name of the association.
    * @param $delete boolean
    *    Option. If true, the unassociated models will be trashed. <code>false</code> by default.
    * @param $sql string
    *    The WHERE part of the query. Optional, empty by default. An empty $sql mean you will remove all models from this association.
    * @param $params array
    *    The parameters for the $sql. Optional, empty by default.
    * @return Model
    *    $this
    */
   protected function clearRelations($name, $delete = false, $sql = '', $params = array()) {
      $this->getFactory()->clearRelations($name, $this, $delete, $sql, $params);
      return $this;
   }
   
   /**
    * Test if a model is related to this one.
    * @param $name string
    *    The name of the association.
    * @param $model Model
    *    The other model.
    * @return boolean
    *    <code>true</code> if the given model is related to the current model in the given association, <code>false</code> otherwise.
    */
   protected function isRelated($name, $model) {
      return $this->getFactory()->areRelated($name, $this, $model);
   }
}
