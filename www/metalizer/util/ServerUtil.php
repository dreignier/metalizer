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
 * Helpers for the $_SERVER supervar.
 * @author David Reignier
 *
 */
class ServerUtil extends Util {

   /**
    * Get a value in $_SERVER.
    * @param $key string
    * 	The key of the value/
    * @return mixed
    * 	$_SERVER[$key]
    */
   public function get($key) {
      return $_SERVER[$key];
   }

   /**
    * Get the IP of the current client.
    */
   public function getIp() {
      return $this->get('REMOTE_ADDR');
   }

}

function getIp() {
   return util('Server')->getIp();
}
