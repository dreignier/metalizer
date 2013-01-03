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
    * An array containing the mapping between redbean query writers and metalizer query writers.
    */
   private $writerMapping = array(
      'RedBean_QueryWriter_CUBRID' => 'CubridQueryWriter',
      'RedBean_QueryWriter_MySQL' => 'MysqlQueryWriter',
      'RedBean_QueryWriter_Oracle' => 'OracleQueryWriter',
      'RedBean_QueryWriter_PostgreSQL' => 'PostgreSqlQueryWriter',
      'RedBean_QueryWriter_SQLiteT' => 'SQLiteTQueryWriter'
   );

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
      R::freeze(config('database.freeze'));
      R::debug(true, $this->log());
      RedBean_ModelHelper::setModelFormatter(new ModelFormatter());

      // Configure redbean with some of our own classes.
      
      $class = $this->writerMapping[get_class(R::$writer)];
      $writer = new $class(R::$adapter);
      
      $this->cache = new BeanCache();

      $oodb = new OODB($writer);
      $oodb->setCache($this->cache);
      $oodb->setUtil($this);
      
      R::configureFacadeWithToolbox(new RedBean_ToolBox($oodb, R::$adapter, $writer));
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
 * @see RedbeanUtil#get
 */
function R() {
   return util('Redbean')->get();
}
