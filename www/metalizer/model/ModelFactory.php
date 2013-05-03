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
 * Can handle an model class.
 * @author David Reignier
 *
 */
class ModelFactory extends MetalizerObject {

   /**
    * The handled class
    * @var string
    */
   private $class;

   /**
    * The database table name
    * @var string
    */
   private $table;

   /**
    * All instances of objects indexed by id.
    * @var array
    */
   private $instances = array();

   /**
    * Alive subclasses ModelFactory objects
    * @var array[ModelFactory]
    */
   private $subFactories = array();

   /**
    * All known subclasses of the handled class.
    * @var array[String]
    */
   private $subClasses = array();

   /**
    * <code>true</code> if $subClasses has changed since the last save, <code>false</code> otherwise.
    * @var boolean
    */
   private $subClassesChanged = false;

   /**
    * The order by of the next query
    * @var string
    */
   private $orderBy = null;

   /**
    * The limit part of the next query
    * @var string
    */
   private $limit = null;

   /**
    * The offset part of the next query
    * @var string
    */
   private $offset = null;

   /**
    * Construct a new ModelClassHandler
    * @param $class string
    * 	The class to handle
    */
   public function __construct($class) {
      if (!@is_subclass_of($class, 'Model')) {
         throw new InternalErrorException("$class is not a subclass of Model");
      }

      $this->class = $class;

      // Try to load sub classes
      if ($subClasses = store()->load("metalizer.model.subclasses_$this->class")) {
         $this->subClasses = $subClasses;
      }

      // Find the table and register to super factories.
      while (get_parent_class($class) != 'Model') {
         $class = get_parent_class($class);
         model($class)->registerSubFactory($this);
         model($class)->registerSubClass($this->class);
      }

      $this->table = strtolower($class);
   }

   /**
    * @return string
    *    The table of the factory
    */
   public function getTable() {
      return $this->table;
   }

   /**
    * Cache and clear instances.
    * Save the subclasses.
    */
   public function finalize() {
      store()->store("metalizer.model.subclasses_$this->class", $this->subClasses);
   }

   /**
    * Create a new model object.
    * @return Model
    * 	A new model object ready to be used. The class of the object is the handled class of this factory.
    */
   public function dispense() {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug('Dispense called');
      }
      
      $model = $this->newInstance();

      $bean = R()->dispense($this->table);
      $bean->metalizerClass = $this->class;

      $bean->setModel($model);
      $model->setBean($bean);

      $model->afterDispense();

      return $model;
   }

   /**
    * Store a model object in the database
    * @param $model Model
    * 	The model object to store.
    */
   public function store($model) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Storing $model");
      }
      
      R()->store($model->getBean());
   }

   /**
    * Delete a model object in the database
    * @param $model Model
    * 	The model object to delete.
    */
   public function trash($model) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Trashing $model");
      }
      
      R()->trash($model->getBean());
   }

   /**
    * Called by a Model before a trash
    * @param $model Model
    *    The trashed model.
    */
   public function beforeTrash($model) {
      unset($this->instances[$model->getId()]);
   }

   /**
    * Alias of findById.
    * @see ModelFactory#findById
    */
   public function load($id) {
      return $this->findById($id);
   }

   /**
    * Set the limit part of the next query
    * @param $limit int
    *    The limit part of the next query
    * @param $offset int
    *    The offset part of the next query
    * @return ModelFactory
    *    $this
    */
   public function limit($limit, $offset = 0) {
      $this->limit = $limit;
      $this->offset = $offset;

      return $this;
   }

   /**
    * Set the "order by" part of the next query
    * @param $field string
    *    The "order by" part of the next query
    * @return ModelFactory
    *    $this
    */
   public function orderBy($field) {
      $this->orderBy = $field;

      return $this;
   }

   /**
    * Reset the next query parts
    * @return ModelFactory
    *    $this
    */
   public function reset() {
      $this->limit = null;
      $this->offset = null;
      $this->orderBy = null;

      return $this;
   }

   /**
    * Find a model by its id
    * @param $id int
    * 	An id.
    * @return Model
    * 	The model object with the given id. Or null.
    */
   public function findById($id) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("findById $id");
      }
      
      if ($instance = $this->findInstance($id)) {
         return $instance;
      }

      $bean = R()->load($this->table, $id);

      if (!$bean->id || !($bean->metalizerClass == $this->class || is_subclass_of($bean->metalizerClass, $this->class))) {
         return null;
      }

      return $this->loadInstance($bean);
   }

   private function generateSubclassesSqlPart() {
      return '("' . implode('","', array_merge($this->subClasses, array($this->class))) . '")';
   }

   /**
    * Find one object for this factory.
    * @param $where string
    * 	Optional. The WHERE part of the query. If $where is missing, the result will be the first Model of the table.
    * @param $params array
    * 	Optional. The parameters for $where.
    * @return Model
    * 	The model corresponding to the query. Or null.
    */
   public function findOne($where = '', $params = array()) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("findOne where $where");
      }
      
      if ($where) {
         $where = "($where) AND ";
      }

      $bean = R()->findOne($this->table, $where . 'metalizerClass IN ' . $this->generateSubclassesSqlPart(), $params);

      if (!$bean || !$bean->id) {
         return null;
      }

      return $this->wrap($bean);
   }

   /**
    * Find objects by a property.
    * @param $property string
    * 	The name of a property
    * @param $value mixed
    * 	The value of the property for the query.
    * @return array
    * 	An array of Model.
    */
   public function findBy($property, $value) {
      return $this->find("$property = ?", array($value));
   }

   /**
    * Find one object by a property.
    * @param $property string
    * 	The name of a property
    * @param $value mixed
    * 	The value of the property for the query
    * @return Model
    * 	The model corresponding to the query. Or null.
    */
   public function findOneBy($property, $value) {
      return $this->findOne("$property = ?", array($value));
   }

   /**
    * The base 'find' method.
    * @param $where string
    * 	The WHERE part of the query.
    * @param $params array
    * 	The parameters for $where.
    * @return array
    * 	An array of Model.
    */
   public function find($where = '', $params = array()) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("find where $where with limit $this->limit, offset $this->offset and order by $this->orderBy");
      }
      
      $result = array();
      $extra = '';

      if ($where) {
         $where = "($where) AND ";
      }

      $where .= " metalizerClass IN " . $this->generateSubclassesSqlPart();

      // Do we use named parameters or anonymous parameters ?
      $useNamedParam = !sizeof($params);
      if (!$useNamedParam) {
         $paramsKeys = array_keys($params);
         $useNamedParam = !is_integer($paramsKeys[0]);
      }

      // Handle order by
      if ($this->orderBy) {
         $order = '';
         $offset = 0;
         
         if (strpos($this->orderBy, 'DESC') !== false) {
            $offset = 4;
         }
         
         if (strpos($this->orderBy, 'ASC') !== false) {
            $offset = 3;
         }
         
         if ($offset) {
            $order = substr($this->orderBy, -$offset);
            $this->orderBy = trim(substr($this->orderBy, 0, -$offset));
         }
         
         $extra .= " ORDER BY `$this->orderBy` $order";
      }

      // Handle limit and offset
      if ($this->limit) {
         $extra .= " LIMIT $this->offset, $this->limit";
      }

      $beans = R()->find($this->table, $where . $extra, $params);
      
      $result = $this->wrapAll($beans);
      
      if ($this->limit == 1) {
         $result = sizeof($result) > 0 ? $result[0] : null;
      }

      $this->reset();

      return $result;
   }

   /**
    * Count objects by a property.
    * @param $property string
    *    The name of a property
    * @param $value mixed
    *    The value of the property for the query.
    * @return integer
    *    The count result.
    */
   public function countBy($property, $value) {
      return $this->count("$property = ?", array($value));
   }

   /**
    * Count objects.
    * @param $where string
    *    The WHERE part of the query.
    * @param $params array
    *    The parameters for $where.
    * @return integer
    *    The count result.
    */
   public function count($where = null, $params = array()) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Count where $where");
      }
      
      if ($where) {
         $where = "($where) AND ";
      }
      $where .= " metalizerClass IN " . $this->generateSubclassesSqlPart();

      return R()->count($this->table, "$where", $params);
   }

   /**
    * Make a new instance for this factory.
    * @return Model
    * 	A new model instance.
    */
   private function newInstance() {
      $class = $this->class;
      return new $class();
   }

   /**
    * Try to find an instance in this factory and subfactories.
    * @param $id int
    * 	The id of an instance
    * @param $deeper bool
    * 	If true, the factory will search in subfactories for the instance.
    * @return Model
    * 	The model instance, or null.
    */
   private function findInstance($id, $deeper = true) {
      if (isset($this->instances[$id])) {
         return $this->instances[$id];
      }

      if ($deeper) {
         foreach ($this->subFactories as $factory) {
            if ($result = $factory->findInstance($id, false)) {
               return $result;
            }
         }
      }

      return null;
   }

   /**
    * Create a new instance for this factory with a bean.
    * @param $bean RedBean_OODBBean
    * 	The bean for the new instance
    * @return Model
    * 	A new Model created with $bean
    */
   private function loadInstance($bean) {
      if ($bean->metalizerClass != $this->class) {
         return model($bean->metalizerClass)->loadInstance($bean);
      }

      $model = $this->newInstance();
      $model->setBean($bean);
      $bean->setModel($model);
      $this->instances[$model->getId()] = $model;

      return $model;
   }

   /**
    * Wrap a redbean in a Model.
    * @param $bean RedBean_OODBBean
    *    A redbean
    * @return Model
    *    The given bean boxed in a Model.
    */
   public function wrap($bean) {
      if (!$bean->id) {
         return null;
      }

      $factory = model($bean->metalizerClass);

      if ($instance = $factory->findInstance($bean->id, false)) {
         return $instance;
      }

      return $factory->loadInstance($bean);
   }

   /**
    * Wrap an array of redbean in Model
    * @param $beans array[RedBean_OODBBean]
    *    An array of redbean
    * @return array[Model]
    *    The given beans boxed in a Model.
    */
   public function wrapAll($beans) {
      $result = array();

      foreach ($beans as $id => $bean) {
         if ($bean->id && $bean->id == $id) {
            $result[$id] = $this->wrap($bean);
         } else {
            $this->log()->warning("wrapAll : A given bean is not valid");
         }
      }

      return $result;
   }

   /**
    * Register a ModelFactory as a subclass handler for this ModelFactory.
    * @param $handler ModelFactory 
    * 	A ModelFactory
    */
   protected function registerSubFactory($factory) {
      $this->subFactories[] = $factory;
   }

   /**
    * Register a subclass for this ModelFactory.
    * @param $class string 
    *    A subclass of Model.
    */
   protected function registerSubClass($class) {
      if (!in_array($class, $this->subClasses)) {
         $this->subClasses[] = $class;
         $this->subClassesChanged = true;
      }
   }
   
   /**
    * Create a single way association between 2 models.
    * @param $name string
    *    The name of the association
    * @param $first Model
    *    The first model
    * @param $second
    *    The second model
    */
   public function associate($name, $first, $second) {
      if (!$first->isStored()) {
         $first->store();
      }
      
      if (!$second->isStored()) {
         $second->store();
      }
      
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Associate $first with $second in $name");
      }
   
      $intermediate = $first->getIntermediate($name);
      
      $type = $second->getFactory()->getTable();
      if (!$intermediate->associationType) {
         $intermediate->associationType = $type;
      } else if ($intermediate->associationType != $type) {
         throw new ModelException("The <b>$name</b> association is already bind to the <b>$intermediate->associationType</b> class. You can't add a <b>" . $second->getClass() . '</b> in this association');
      }
      
      R()->associate($intermediate, $second->getBean());
   }
   
   /**
    * Retrieve all related models from a given model.
    * @param $name string
    *    The name of the association
    * @param $model Model
    *    A model.
    * @param $sql string
    *    The WHERE part of the query. Optional, empty by default.
    * @param $params array
    *    The parameters for the $sql. Optional, empty by default.
    * @return array[Model]
    *    All related models for the current model in the given association
    */
   public function related($name, $model, $sql = '', $params = array()) {
      $intermediate = $model->getIntermediate($name);
      
      if (!$intermediate->id) {
         return array();
      }
      
      return $this->wrapAll(R()->related($intermediate, $intermediate->associationType, $sql, $params));
   }
   
   /**
    * Unassociate two model.
    * @param $name string
    *    he name of the association.
    * @param $first Model
    *    The first model
    * @param $second
    *    The second model
    * @param $delete boolean
    *    Option. If true, $second is trashed. <code>false</code> by default.
    */
   public function unassociate($name, $first, $second, $delete = false) {
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Unassociate $first with $second in $name");
      }
      
      $intermediate = $first->getIntermediate($name);
      
      R()->unassociate($intermediate, $second->getBean());
      
      if ($delete) {
         $second->trash();
      }
   }
   
   /**
    * Unassociate many models from a model
    * @param $name string
    *    he name of the association.
    * @param $model Model
    *    A model.
    * @param $delete boolean
    *    Option. If true, the unassociated models will be trashed. <code>false</code> by default.
    * @param $sql string
    *    The WHERE part of the query. Optional, empty by default. An empty $sql mean you will remove all models from this association.
    * @param $params array
    *    The parameters for the $sql. Optional, empty by default.
    */
   public function clearRelations($name, $model, $delete = false, $sql = '', $params = array()) {
      foreach ($this->related($name, $model, $sql, $params) as $id => $related) {
         $this->unassociate($name, $model, $related, $delete);
      }
   }
   
   /**
    * Test if a two model are related
    * @param $name string
    *    The name of the association.
    * @param $first Model
    *    The first model
    * @param $second
    *    The second model
    * @return boolean
    *    <code>true</code> if the $second is related to $first in the given association, <code>false</code> otherwise.
    */
   public function areRelated($name, $first, $second) {
      $intermediate = $first->getIntermediate($name);
      
      if (!$intermediate->id || !$intermediate->associationType) {
         return false;
      }
      
      return R()->areRelated($intermediate, $second->getBean());
   }

}
