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
 * Mother class of all classes in Metalizer.
 * @author David Reignier
 *
 */
class MetalizerObject {

   /**
    * The looger for the object
    * @var Logger
    */
   private $logger = null;

   /**
    * @return string
    * 	A string representation of the object.
    */
   public function toString() {
      return $this->getClass();
   }
   
   /**
    * Replace the __toString magic method by toString
    */
   public function __toString() {
      return $this->toString();
   }

   /**
    *
    * @return string
    * 	Same as <code>get_class($object)</code>
    */
   public function getClass() {
      return get_class($this);
   }

   /**
    * Get the log name (for log messages). Subclasses should override this method.
    * @return string
    * 	An empty string
    */
   public function getLogName() {
      return "";
   }
   
   /**
    * Finalize the object
    */
   public function finalize() {
      
   }

   /**
    * Get the logger
    * @return Logger
    * 	The logger of the current object.
    */
   public function log() {
      if (!$this->logger) {
         $this->logger = new Logger($this);
      }

      return $this->logger;
   }

}
