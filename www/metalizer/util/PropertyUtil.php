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

define('PROPERTIES_FILE_NAME', 'metalizer.properties');

/**
 * Provide a way to store/retrieve global properties for the application.
 * Properties are persistents data.
 * PropertyUtil use the StoreUtil to store properties.
 * @author David Reignier
 *
 */
class PropertyUtil extends Util {

   private $properties;

   /**
    * Construct a new PropertyUtil.
    * PropertyUtil#properties is initialize using the StoreUtil.
    * @return PropertyUtil
    */
   public function __construct() {
      $this->properties = store()->load(PROPERTIES_FILE_NAME);

      if (!$this->properties) {
         $this->properties = array();
      }
   }

   /**
    * Get a property
    * @param $name string
    * 	The name of the property
    * @return mixed
    * 	The value of the property. Or null if no property exists with the given name.
    */
   public function get($name) {
      if (isset($this->properties[$name])) {
         return $this->properties[$name];
      }

      return null;
   }

   /**
    * Set a property.
    * @param $name string
    * 	The name of the property
    * @param $value mixed
    * 	The value of the property. Must be serializable.
    */
   public function set($name, $value) {
      $this->properties[$name] = $value;
   }

   /**
    * @param $name string
    * 	Name of a property
    * @return boolean
    * 	true if the property exists, false otherwise.
    */
   public function exists($name) {
      return isset($this->properties[$name]);
   }

   /**
    * Delete a property.
    * @param $name string
    * 	The name of the property.
    */
   public function delete($name) {
      unset($this->properties[$name]);
   }

   /**
    * Save the properties.
    * @see MetalizerObject#onFinalize()
    */
   public function onFinalize() {
      store()->store(PROPERTIES_FILE_NAME, $this->properties);
   }

}

/**
 * @return PropertyUtil
 *  The PropertyUtil.
 */
function property() {
   return Util('Property');
}
