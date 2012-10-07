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
    * Called right after the construction of a new model. It does nothing.
    * Subclasses should override this method.
    */
   public function initialize() {

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
      $this->getClassHandler()->store($this);
   }

   /**
    * Detele the current model in the database and caches.
    */
   public function trash() {
      $this->getClassHandler()->trash($this);
   }

   /**
    * @return ModelFactory
    * 	The factory for this model.
    */
   public function getClassHandler() {
      return model($this->getClass());
   }

}