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
    * The metalizer oodb for redbean
    * @var OODB
    */
   private $oodb;

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
    * <code>true</code> if redbean is connected to the database, <code>false</code> otherwise.
    * @var boolean
    */
   private $connected = false;

   /**
    * Construct a new RedbeanUtil
    */
   public function __construct() {
      $this->dynamicToStatic = new RedbeanUtil_DynamicToStatic();
   }

   /**
    * Connect the database and set the freeze parameter.
    * Also set the formatter.
    */
   public function connect() {
      if ($this->connected) {
         return;
      }
      
      if ($this->log()->isInfoEnabled()) {
         $this->log()->info('Initialize redbean');
      }
      
      R::setup(config('database.connection_string'), config('database.user'), config('database.password'));
      R::freeze(config('database.freeze'));
      R::debug(true, $this->log());
      RedBean_ModelHelper::setModelFormatter(new ModelFormatter());

      // Configure redbean with some of our own classes.
      
      $class = $this->writerMapping[get_class(R::$writer)];
      $writer = new $class(R::$adapter);

      $this->oodb = new OODB($writer);
      $this->oodb->setUtil($this);
      
      R::configureFacadeWithToolbox(new RedBean_ToolBox($this->oodb, R::$adapter, $writer));
      
      $this->connected = true;
   }
   
   /**
    * Close the database connection.
    */
   public function finalize() {
      if (!$this->connected) {
         return;
      }
      
      if ($this->log()->isInfoEnabled()) {
         $this->log()->info('Finalize redbean');
      }
      
      $this->oodb->getCache()->finalize();
      R::close();
   }

   /**
    * @return RedbeanUtil_DynamicToStatic
    * 	You can use this to access to the "R" classes without the "static" way.
    */
   public function get() {
      $this->connect();
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

/**
 * @return RedbeanUtil
 */
function redbean() {
   return util('Redbean');
}


