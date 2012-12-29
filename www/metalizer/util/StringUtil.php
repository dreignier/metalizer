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
 * Helpers for strings.
 * @author David Reignier
 *
 */
class StringUtil extends Util {

   /**
    * Escape html caracters in a string.
    * @param $string string
    *    A string.
    * @return string
    *    The given string with hml caracters escaped.
    */
   public function escapeHtml($string) {
      return htmlspecialchars($string);
   }

   /**
    * Generate a random string
    * @param $length int
    *    The length of the string
    * @param $chars string
    *    Optional. A list of possible chararacter for the random string.
    * @return string
    *    A random string
    */
   public function random($length, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz012345789') {
      $result = '';
      $max = strlen($chars) - 1;
      while ($length-- > 0) {
         $result .= substr($chars, mt_rand(0, $max), 1);
      }

      return $result;
   }

}

function escapeHtml($string) {
   return util('String')->escapeHtml($string);
}
