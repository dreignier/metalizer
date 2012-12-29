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
 * Provide easy access to the redbean class.
 * @author David Reignier
 *
 */
class RedbeanUtil extends Util {

   /**
    * Used in the get method.
    * @var RedbeanUtil_DynamicToStatic
    */
   private $dynamicToStatic;

   /**
    * The cache for the beans
    * RedbeanUtil_BeanCache
    */
   private $cache;

   /**
    * Construct a new RedbeanUtil
    */
   public function __construct() {
      $this->dynamicToStatic = new RedbeanUtil_DynamicToStatic();
      $this->connect();
   }

   /**
    * Connect the database and set the freeze parameter.
    * Also set the formatter.
    */
   public function connect() {
      R::setup(config('database.connection_string'), config('database.user'), config('database.password'));
      R::freeze(config('redbean.freeze'));
      R::debug(true, $this->log());
      RedBean_ModelHelper::setModelFormatter(new RedbeanUtil_ModelFormatter());

      // Configure the facade with our own facade.
      $toolbox = R::$toolbox;
      $facade = new RedBeanUtil_MetalizerFacade($toolbox->getWriter());
      $this->cache = new RedbeanUtil_BeanCache();
      $facade->setCache($this->cache);
      $facade->setUtil($this);
      R::configureFacadeWithToolbox(new RedBean_ToolBox($facade, $toolbox->getDatabaseAdapter(), $toolbox->getWriter()));
   }

   /**
    * We need to require the redbean file and connect to the database.
    */
   public function onWakeUp() {
      $this->connect();
      $this->cache->wakeUp();
   }

   /**
    * Close the database connection.
    */
   public function onSleep() {
      $this->cache->sleep();
      if (class_exists('R')) {
         R::close();
      }
   }

   /**
    * @return RedbeanUtil_DynamicToStatic
    * 	You can use this to access to the "R" classes without the "static" way.
    */
   public function get() {
      return $this->dynamicToStatic;
   }

}

/**
 * Private class.
 * All method call are passed to the R class.
 */
class RedbeanUtil_DynamicToStatic extends MetalizerObject {

   /**
    * Magic caller. Just call the R method in a static way.
    */
   public function __call($name, $arguments) {
      return call_user_func_array("R::$name", $arguments);
   }

}

/**
 * Private class.
 * The model formatter for Redbean.
 */
class RedbeanUtil_ModelFormatter extends MetalizerObject implements RedBean_IModelFormatter {

   /**
    * In Metalizer, we always use the Bean class for the Redbean bean model.
    */
   public function formatModel($model) {
      return 'Bean';
   }

}

/**
 * Private class.
 * Handle the cache of beans.
 */
class RedbeanUtil_BeanCache extends MetalizerObject {

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

      $this->clean($bean->id, $bean->getMeta('type'));
   }

}

/**
 * Private class.
 * We override the Redbean facade with this class in Metalizer.
 * This facade can use the Metalizer cache for beans.
 */
class RedBeanUtil_MetalizerFacade extends RedBean_OODB {

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

/**
 * @see RedbeanUtil#get
 */
function R() {
   return util('Redbean')->get();
}
