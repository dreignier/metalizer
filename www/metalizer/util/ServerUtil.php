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
    * 	The key of the value.
    * @return mixed
    * 	$_SERVER[$key]
    */
   public function get($key) {
      return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
   }

   /**
    * @return string
    *    The current remote IP.
    */
   public function getIp() {
      return $this->get('REMOTE_ADDR');
   }

   /**
    * @return string
    *    The current request method.
    */
   public function getRequestMethod() {
      return $this->get('REQUEST_METHOD');
   }

   /**
    * Get the request body.
    * @return string
    *    The request body as string.
    */
   public function getRequestBody() {
      return file_get_contents('php://input');
   }

   public function getRequestContentType() {
      return $this->get('CONTENT_TYPE');
   }

}

/**
 * @return ServerUtil
 *    The ServerUtil
 */
function server() {
   return util('Server');
}

function getIp() {
   return util('Server')->getIp();
}

function getRequestMethod() {
   return util('Server')->getRequestMethod();
}

function getRequestBody() {
   return util('Server')->getRequestBody();
}

function getRequestContentType() {
   return util('Server')->getRequestContentType();
}
