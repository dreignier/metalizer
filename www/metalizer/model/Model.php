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
class Model extends MetalizerObject {

   /**
    * The redbean bean used by the model.
    * @var RedBean_OODBBean
    */
   protected $model;
   
   /**
    * Called just after the constructor.
    * Should be override by subclasses.
    */
   public function initialize() {
      
   }
   
   /**
    * @return bool
    *    true if the model is already stored in the database, false otherwise.
    *    Note that a stored object can still be out of date in the database.
    */
   public function isStored() {
      return $this->getid() != 0;
   }

   /**
    * Get the redbean bean of this model.
    * You'll need this method if you want to make a relation between two model object.
    * @return RedBean_OODBBean
    *	 The redbean of this model.
    */
   public function getModel() {
      return $this->model;
   }

   /**
    * Set the redbean bean of this model.
    * This method should _NOT_ be used by an other class than ModelFactory.
    * @param $model RedBean_OODBBean
    *		The new redbean bean for this model.
    */
   public function setModel($model) {
      $this->model = $model;
   }

   /**
    * Get the current id of the model.
    * @return int
    * 	The current id of the model.
    */
   public function getId() {
      return $this->model->id;
   }

   /**
    * Store or update the current model in the database.
    */
   public function store() {
      $this->getFactory()->store($this);
   }

   /**
    * Delete the current model in the database and caches.
    */
   public function trash() {
      $this->getFactory()->trash($this);
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
    */
   protected function set($name, $value) {
      $reflection = new ReflectionClass($this->getClass());
      $method = 'validate' . ucfirst($name);
      if ($reflection->hasMethod($method)) {
         if (!call_user_func(array($this, $method), $value)) {
            throw new ModelValidationException($name, $value);
         }
      }
      
      $this->model->$name = $value;
   }
   
   /**
    * A generic getter for models classes.
    * @param $name string
    *    The name of the field to get
    * @return mixed
    *    The value of the wanted file.
    */
   protected function get($name) {
      return $this->model->$name;
   }
   
   /**
    * Try to validate the model. Validate is called when the model must store itself in the database.
    * @return bool
    *    Always true
    * @throws ModelException
    *    If the model is not valid.
    */
   public function validate() {
      $reflection = new ReflectionClass($this->getClass());
      
      foreach ($reflection->getMethods() as $method) {
         if (substr($method->name, 0, 8) == 'validate' && strlen($method->name) > 8) {
            $name = lcfirst(substr($method->name, 8));
            $value = $this->get($name);
            if (!call_user_func(array($this, $method->name), $value)) {
               throw new ModelValidationException($name, $value);
            }
         }
      } 
      
      return true;
   }
   
}
