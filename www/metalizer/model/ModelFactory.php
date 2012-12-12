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
    * Construct a new ModelClassHandler
    * @param $class string
    * 	The class to handle
    */
   public function __construct($class) {
      if (!is_subclass_of($class, 'Model')) {
         throw new InternalErrorException("$class is not a of Model");
      }

      $this->class = $class;
      
      if ($subClasses = store()->load("metalizer.model.subclasses_$this->class")) {
         $this->subClasses = $subClasses;
      }
            
      // Determine the table and call super factories.
      while (get_parent_class($class) != 'Model') {
         $class = get_parent_class($class);
         model($class)->registerSubFactory($this);
         model($class)->registerSubClass($this->class);
      }
      
      $this->table = strtolower($class);
   }

   /**
    * Cache and clear instances.
    * Save the subclasses.
    */
   public function onSleep() {
      $this->instances = array();
      
      store()->store("metalizer.model.subclasses_$this->class", $this->subClasses);
      $this->subClassesChanged = false;
   }
   
   /**
    * Create a new model object.
    * @return Model
    * 	A new model object ready to be used. The class of the object is the handled class of this factory.
    */
   public function dispense() {
      $model = $this->newInstance();
		
		$bean = R()->dispense($this->table);
		$bean->metalizerClass = $this->class;
      
      $model->setModel($bean);
      $model->initialize();
      
      return $model;
   }

   /**
    * Store a model object in the database
    * @param $model Model
    * 	The model object to store.
    */
   public function store($model) {
      $model->validate();
      $id = R()->store($model->getModel());
      $this->instances[$id] = $model;
   }

   /**
    * Delete a model object in the database
    * @param $model Model
    * 	The model object to delete.
    */
   public function trash($model) {
      R()->trash($model->getModel());
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
    * Find a model by its id
    * @param $id int
    * 	An id.
    * @return Model
    * 	The model object with the given id. Or null.
    */
   public function findById($id) {
      if ($instance = $this->findInstance($id)) {
         return $instance;
		}

      $bean = R()->load($this->table, $id);

      if (!$bean->id || !($bean->metalizerClass == $this->class || is_subclass_of($bean->metalizerClass, $this->class))) {
         return null;
      }

      $model = $this->loadInstance($bean);

      return $model;
   }

   /**
    * Find all objects for this factory.
    * @param $orderBy string
    * 	The ORDER BY part of the query.
    * @param $offset int
    * 	The offset of the query.
    * @param $limit int
    * 	The limit of the query.
    * @return array
    * 	An array of Model.
    */
   public function findAll($orderBy = null, $offset = 0, $limit = null) {
      return $this->find(null, array(), $orderBy, $offset, $limit);
   }

   private function generateSubclassesSqlPart() {
      return '("' . implode('","', array_merge($this->subClasses, array($this->class))) . '")';
   }

   /**
    * Find one object for this factory.
    * @param $where string
    * 	The WHERE part of the query.
    * @param $params array
    * 	The parameters for $where.
    * @return Model
    * 	The model corresponding to the query. Or null.
    */
   public function findOne($where, $params = array()) {
      $where = "($where) AND metalizerClass IN " . $this->generateSubclassesSqlPart();

      $bean = R()->findOne($this->table, $where, $params);

      if (!$bean || !$bean->id) {
         return null;
      }

      if ($instance = model($bean->metalizerClass)->findInstance($bean->id)) {
         return $instance;
		}

      $model = $this->loadInstance($bean);

      return $model;
   }

   /**
    * Find objects by a property.
    * @param $property string
    * 	The name of a property
    * @param $value mixed
    * 	The value of the property for the query.
    * @param $orderBy string
    * 	The ORDER BY part of the query.
    * @param $offset int
    * 	The offset of the query.
    * @param $limit int
    * 	The limit of the query.
    * @return array
    * 	An array of Model.
    */
   public function findBy($property, $value, $orderBy = null, $offset = 0, $limit = null) {
      return $this->find("$property = ?", array($value), $orderBy, $offset, $limit);
   }

   /**
    * Find one object by a prorperty.
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
    * @param $orderBy string
    * 	The ORDER BY part of the query.
    * @param $offset int
    * 	The offset of the query.
    * @param $limit int
    * 	The limit of the query.
    * @return array
    * 	An array of Model.
    */
   public function find($where, $params = array(), $orderBy = null, $offset = 0, $limit = null) {
      $result = array();
      $extra = '';

      $where = $where ? "($where) AND " : '';
      $where .= " metalizerClass IN " . $this->generateSubclassesSqlPart();

      // Do we use named parameters or anonymous parameters ?
      $useNamedParam = !sizeof($params);
      if ($useNamedParam) {
         $paramsKeys = array_keys($params);
         $useNamedParam = !is_integer($paramsKeys[0]);
      }

      // Handle order by
      if ($orderBy) {
         $extra .= ' ORDER BY ' . ($useNamedParam ? ':orderby' : '?');
         if ($useNamedParam) {
            $params[':orderby'] = $orderBy;
         } else {
            $params[] = $orderBy;
         }
      }

      // Handle limit and offset
      if ($limit) {
         if ($useNamedParam) {
            $extra .= ' LIMIT :offset, :limit';
            $params[':offset'] = $offset;
            $params[':limit'] = $limit;
         } else {
            $extra .= ' LIMIT ?, ?';
            $params[] = $offset;
            $params[] = $limit;
         }
      }

      $beans = R()->find($this->table, $where . $extra, $params);

      foreach ($beans as $bean) {
         $id = $bean->id;
         
         $model;
         if ($instance = model($bean->class)->findInstance($id, false)) {
            $model = $instance;
         } else {
            $model = $this->loadInstance($bean);
         }

         $result[] = $model;
      }

      if ($limit == 1) {
         $result = sizeof($result) > 0 ? $result[0] : null;
      }

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
      if ($where) {
         $where = "($where) AND ";
      }
      $where .= " metalizerClass IN " . $this->generateSubclassesSqlPart();
      
      return R()->count($this->table, "$where", $params);
   }

	/**
	 * Convert a model bean in a Model object.
	 * @param $bean RedBean_OODBBean
	 * 	The bean for the model
	 * @return Model
	 * 	The model object for the given bean.
	 */
	public function loadModel($bean) {
		if ($instance = model($bean->class)->findInstance($bean->id, false)) {
			return $instance;
		} else {
			return $this->loadInstance($bean);
		}
	}

	/**
	 * Convert an array of model bean in a array of Model objects.
	 * @param $beans array[RedBean_OODBBean]
	 * 	The beans for models
	 * @return array[Model]
	 * 	The Model objects for the given model beans.
	 */
   public function loadModels($beans) {
		$result = array();
		foreach($beans as $key => $bean) {
			$result[$key] = $this->loadModel($bean);
		}
		return $result;
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
			foreach($this->subFactories as $factory) {
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
   	if ($bean->metalizerClass == $this->class) {
	      $model = $this->newInstance();
	      $model->setModel($bean);
	      $this->instances[$model->getId()] = $model;
	
      	return $model;
		} else {
			return model($bean->metalizerClass)->loadInstance($bean);
		}
   }

   /**
    * Register a ModelFactory as a subclass handler for this ModelFactory.
    * @param ModelFactory $handler
    * 	A ModelFactory
    */
   protected function registerSubFactory($factory) {
      $this->subFactories[] = $factory;
   }
   
   /**
    * Register a subclass for this ModelFactory.
    * @param string $class
    *    A subclass of Model.
    */
   protected function registerSubClass($class) {
      if (!in_array($class, $this->subClasses)) {
         $this->subClasses[] = $class;
         $this->subClassesChanged = true;
      }
   }

}
