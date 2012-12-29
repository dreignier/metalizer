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
 * Handle the $_SESSION variable.
 * @author David Reignier
 *
 */
class SessionUtil extends Util {

   /**
    * @param $name string
    *    A string.
    * @return string
    *    "metalizer.$name"
    */
   private function prepareName($name) {
      return "metalizer.$name";
   }

   /**
    * Set a session value.
    * @param $name string
    *    Name of the value.
    * @param $value mixed
    *    The value.
    */
   public function set($name, $value) {
      $name = $this->prepareName($name);

      $_SESSION[$name] = $value;
   }

   /**
    * Get a session value
    * @param $name string
    *    Name of the value.
    * @return mixed
    *    The value. Or null if the value does not exist.
    */
   public function get($name) {
      $name = $this->prepareName($name);

      if (!isset($_SESSION[$name])) {
         return null;
      }

      return $_SESSION[$name];
   }

   /**
    * Clean a session value.
    * @param $name string
    *    Name of the value.
    */
   public function clean($name) {
      $name = $this->prepareName($name);

      if (isset($_SESSION[$name])) {
         unset($_SESSION[$name]);
      }
   }

   /**
    * Clean all session variables.
    */
   public function cleanAll() {
      foreach ($_SESSION as $name => $value) {
         if (substr($name, 0, 10) == 'metalizer.') {
            unset($_SESSION[$name]);
         }
      }
   }

}

/**
 * @return SessionUtil
 */
function session() {
   return util('session');
}
