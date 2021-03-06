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

define('MANAGER_MANAGER_CACHE_FILE', PATH_CACHE . 'metalizer/managers');

/**
 * The manager of all managers in the application.
 * @author David Reignier
 *
 */
class ManagerManager extends Manager {

   /**
    * ManagerManager is a singleton
    * @var ManagerManager
    */
   static private $instance = null;

   /**
    * Construct a new ManagerManager. Because of php, we can't set this constructor in private.
    * _DO NOT USE IT_
    * @return ManagerManager
    */
   public function __construct() {
      if (ManagerManager::$instance) {
         
      }
      
      parent::__construct('Manager');
   }

   /**
    * Initialize the ManagerManager. After that, you can access to the ManagerManager with the ManagerManager#instance() function.
    */
   static public function initialize() {
      // REMINDER : We can't use utils here, because absolutely nothing is initialized
      ManagerManager::$instance = new ManagerManager();
   }

   /**
    * The unique instance of ManagerManager.
    * @return ManagerManager
    */
   static public function instance() {
      return ManagerManager::$instance;
   }

}

/**
 * Get a manager.
 * @param $name string
 * 	The name of a manager
 * @return Manager
 * 	The manager corresponding to the given name.
 */
function manager($name) {
   return ManagerManager::instance()->get($name);
}
