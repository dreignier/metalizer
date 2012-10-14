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
 * Provide some helper for headers.
 * @author David Reignier
 *
 */
class HeaderUtil extends Util {
   
   /**
    * Set a header with the syntax "$header: $value"
    * @param $header string
    *    The header name
    * @param $value string
    *    The header value.
    */
   public function set($header, $value) {
      header("$header: $value");
   }
   
   /**
    * Set the http response code.
    * @param $code int
    *    An http response code.
    */
   public function setHttpResponseCode($code) {
      header("HTTP/1.1 $code");
   }
   
   /**
    * Redirect to an url.
    * @param $url string
    *    An url.
    */
   public function redirect($url) {
      $this->set('Location', $url);
   }
   
}

function redirect($url) {
   util('Header')->redirect($url);
}
