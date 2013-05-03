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
 * Helpers for the cookies
 *
 * @author David Reignier
 */
class CookieUtil extends Util {
   
      /**
    * Get a cookie value
    * @param $name string
    *    The cookie name
    */
   public function get($name) {
      return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; 
   }
   
   public function set($name, $value, $expire = 0) {
      if ($expire != 0) {
         $expire += now();
      }
      
      setcookie($name, $value, $expire);
   }
      
}

/**
 * @return CookieUtil
 */
function cookie() {
   return util('Cookie');
}  