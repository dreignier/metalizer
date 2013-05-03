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

define('IN_MILLISECONDS', 1000);
define('MINUTE', 60);
define('HOUR', MINUTE * 60);
define('DAY', HOUR * 24);
define('WEEK', DAY * 7);

/**
 * Provide some constants and helper for time and date.
 * @author David Reignier
 *
 */
class TimeUtil extends Util {

   /**
    * Return the current time in a Unix timestamp.
    * @param $object boolean
    *    If true, the result is a DateTime object.
    * @return mixed
    * 	The current time. If $object is true, the result is a DateTime object. If $object is false, the result is <code>time()</code>.
    */
   public function now($object = false) {
      $time = time();
      $datetime = new DateTime();
      $time -= $datetime->getOffset();
      
      if ($object) {
         $datetime->setTimeZone(new DateTimeZone('UTC'));
         $datetime->setTimeStamp($time);
         return $datetime;
      } else {
         return $time;
      }
   }
   
   public function datetime($time) {
      $result = new DateTime();
      $result->setTimestamp($time);
      return $result;
   }
   
   public function parse($format, $time) {
      $result = Datetime::createFromFormat($format, $time);
      
      if (!$result || !($result instanceof DateTime)) {
         throw new DateFormatException("Can't parse '$time' with the format '$format'");
      }
      
      return $result->getTimestamp();
   }
   
   public function format($format, $time = null) {
      $date = new Datetime();
      $date->setTimestamp($time ? $time : time());
      return $date->format($format);
   }
   
}

/**
 * @return TimeUtil
 */
function _time() {
   return util('Time');
}

/**
 * @see TimeUtil#now
 */
function now() {
   return util('Time')->now();
}
