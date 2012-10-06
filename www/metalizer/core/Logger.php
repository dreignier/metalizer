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
 * Handle logs for a MetalizerObject
 * @author David Reignier
 *
 */
class Logger extends MetalizerObject {

   /**
    * The handled object
    * @var MetalizerObject
    */
   private $object = null;

   /**
    * Construct a new Logger.
    * @param $object MetalizerObject
    * 	The handled object
    */
   public function __construct($object) {
      $this->object = $object;
   }

   /**
    * Make a log message.
    * @param $message string
    * 	The message
    * @return string
    * 	The object log name and the message.
    */
   private function makeLogMessage($message) {
      $logName = $this->object->getLogName();
      if ($logName) {
         $logName = "[$logName]";
         return "$logName $message";
      } else {
         return $message;
      }
   }

   /**
    * Log a message with the trace level
    * @param $message string
    * 	The message to log
    */
   public function trace($message) {
      _log($this->object, $this->makeLogMessage($message), METALIZER_LOG_TRACE);
   }

   /**
    * Log a message with the debug level
    * @param $message string
    * 	The message to log
    */
   public function debug($message) {
      _log($this->object, $this->makeLogMessage($message), METALIZER_LOG_DEBUG);
   }

   /**
    * Log a message with the info level
    * @param $message string
    * 	The message to log
    */
   public function info($message) {
      _log($this->object, $this->makeLogMessage($message), METALIZER_LOG_INFO);
   }

   /**
    * Log a message with the warning level
    * @param $message string
    * 	The message to log
    */
   public function warning($message) {
      _log($this->object, $this->makeLogMessage($message), METALIZER_LOG_WARNING);
   }

   /**
    * Log a message with the error level
    * @param $message string
    * 	The message to log
    */
   public function error($message) {
      _log($this->object, $this->makeLogMessage($message), METALIZER_LOG_ERROR);
   }

   /**
    * @return bool
    * 	true is the trace log is enabled, false otherwise
    */
   public function isTraceEnabled() {
      return isLogEnabled($this->object, METALIZER_LOG_TRACE);
   }

   /**
    * @return bool
    * 	true is the trace log is enabled, false otherwise
    */
   public function isDebugEnabled() {
      return isLogEnabled($this->object, METALIZER_LOG_DEBUG);
   }

   /**
    * @return bool
    * 	true is the trace log is enabled, false otherwise
    */
   public function isInfoEnabled() {
      return isLogEnabled($this->object, METALIZER_LOG_INFO);
   }

   /**
    * @return bool
    * 	true is the trace log is enabled, false otherwise
    */
   public function isWarningEnabled() {
      return isLogEnabled($this->object, METALIZER_LOG_WARNING);
   }

   /**
    * @return bool
    * 	true is the trace log is enabled, false otherwise
    */
   public function isErrorEnabled() {
      return isLogEnabled($this->object, METALIZER_LOG_ERROR);
   }

}
