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
 * A ModelClassHandler handle a subclass of Model. Each subclass of Model must have a ModelClassHandler.
 * The ModelClassHandler of a class is an instance of a subclass of ModelClassHandler.
 * @author David Reignier
 *
 */
class ModelClassHandler extends MetalizerObject {

	/**
	 * The handled class of the ModelClassHandler.
	 * @var string
	 */
	private $class;
	
	/**
	 * The name of the table used by the ModelClassHandler
	 * @var string
	 */
	private $table;
	
	/**
	 * The class descriptor.
	 * @var ModelClassDescriptor
	 */
	private $descriptor;
	
	/**
	 * Construct a new ModelClassHandler
	 * @param $class string
	 * 	The class handled by the ModelClassHandler.
	 * @return ModelClassHandler
	 */
	public function __construct($class) {
		$this->class = $class;
		$this->table = strtolower($class);
		$descriptorClass = $class + 'ClassDescriptor';
		$this->descriptor = new $descriptorClass();
	}
	
	/**
	 * Find some object of the handled class.
	 * @param $where string
	 * 	The where part of the query.
	 * @param $offset int
	 * 	Optional. The offset of the query. 0 by default.
	 * @param $count int
	 * 	Optional. The number of element. If undefined, all objets are returned.
	 * @return mixed
	 * 	An array of the handled class. Or a single object (or null) if $count is 1.
	 */
	public function find($where, $count = null, $offset = 0) {
	}
	
	/**
	 * Find all objects of the handled class.
	 * @param $offset int
	 * 	Optional. The offset of the query. 0 by default.
	 * @param $count int
	 * 	Optional. The number of element. If undefined, all objets are returned.
	 * @return array[Model]
	 * 	An array of the handled class.
	 */
	public function findAll($count = null, $offset = 0) {
		return $this->find(null, $count, $offset);
	}
	
	 /**
	 * Find some object of the handled class by a specific field.s.
	 * @param $field string
	 * 	The name of a field of the handled class
	 * @param $value mixed
	 * 	The expected value of the given field.
	 * @param $offset int
	 * 	Optional. The offset of the query. 0 by default.
	 * @param $count int
	 * 	Optional. The number of element. If undefined, all objets are returned.
	 * @return mixed
	 * 	An array of the handled class. Or a single object (or null) if $count is 1.
	 */
	public function findBy($field, $value, $count = null, $offset = 0) {
		return $this->find("`$field` = $value", $count, $offset);	
	}
	
	/**
	 * Same as findBy, expect that $field is always 'id' and $count is 1.
	 * @see ModelClassHandler#findBy 
	 */
	public function findById($value) {
		return $this->findBy('id', $value, 1, 0);
	}
	
	/**
	 * Get the table of the ModelClassHandler.
	 * @return string
	 * 	The table of the ModelClassHandler
	 */
	public function getTable() {
		return $this->table;
	}
	
	/**
	 * Delete an object from the database.
	 * @param $model Model
	 * 	A model. It must be of the handled class (or at least, a subclass of the handled class).
	 */
	public function delete($model) {
		
	}
	
	/**
	 * Register or update a model in the database.
	 * @param $model Model
	 * 	A model. It must be of the handled class (or at least, a subclass of the handled class).
	 */
	public function save($model) {
		
	}
}
