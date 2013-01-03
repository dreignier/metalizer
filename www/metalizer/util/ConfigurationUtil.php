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
 * The ConfigurationUtil provide a easy way to read configurations values. It reads all files in configurations folders (metalizer and application).
 * @author David Reignier
 *
 */
class ConfigurationUtil extends Util {

   /**
    * All configuration values
    * @var array[mixed]
    */
   private $configuration;

   /**
    * Construct a new ConfigurationUtil. All configuration files are loaded.
    * @return ConfigurationUtil
    */
   public function __construct() {
      $config = array();

      // Metalizer default configuration
      foreach (_file()->glob(PATH_METALIZER_CONFIGURATION . '*.php') as $file) {
         require $file;
      }

      // Metalizer default libraries configuration
      foreach (_file()->glob(PATH_METALIZER_LIBRARY . '*/configuration/*.php') as $file) {
         require $file;
      }

      // Application default libraries configuration
      foreach (_file()->glob(PATH_APPLICATION_LIBRARY . '*/configuration/*.php') as $file) {
         require $file;
      }

      // Application configuration
      foreach (_file()->glob(PATH_APPLICATION_CONFIGURATION . '*.php') as $file) {
         require $file;
      }

      $this->configuration = $config;
   }

   /**
    * Get a configuration value.
    * @param $key string
    * 	The name of the value
    * @return mixed
    * 	The value
    * @throws ConfigurationKeyNotFoundException
    * 	If the key can't be found in the configuration values.
    */
   public function get($key) {
      if (isset($this->configuration[$key])) {
         return $this->configuration[$key];
      }

      return null;
   }

}

/**
 * @return ConfigurationUtil
 */
function config($key = null) {
   if ($key == null) {
      return util('Configuration');
   }
   
   return util('Configuration')->get($key);
}
