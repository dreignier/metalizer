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
    * Say if the object is sleeping.
    * @var boolean
    */
   private $sleeping = false;

   /**
    * The manager of the object. Can be null.
    * @var Manager
    */
   private $manager = null;

   /**
    * The looger for the object
    * @var Logger
    */
   private $logger = null;

   /**
    * Get the manager of the object.
    * @return Manager
    * 	The manager of the object, or null.
    */
   public function getManager() {
      return $this->manager;
   }

   /**
    * Set the manager of the object.
    * @param $manager Manager
    * 	A manager
    */
   public function setManager($manager) {
      $this->manager = $manager;
   }

   /**
    * @return true if the object is sleeping, false otherwise.
    */
   public function isSleeping() {
      return $this->sleeping;
   }

   /**
    * Put the object in the sleep state. MetalizerObject#onSleep will be called.
    */
   public function sleep() {
      if (!$this->sleeping) {
         $this->onSleep();
         $this->manager = null;
         $this->sleeping = true;
         $this->logger = null;
      }
   }

   /**
    * Called when MetalizerObject#sleep is called. Do nothing by default. Subclasses should override this method.
    */
   public function onSleep() {

   }

   /**
    * Wake up the object.
    * @param $manager Manager
    * 	Optional. The new manager of the object.
    */
   public function wakeUp($manager = null) {
      if ($this->sleeping) {
         if ($manager) {
            $this->manager = $manager;
         }
         $this->onWakeUp();
         $this->sleeping = false;
      }
   }

   /**
    * Called when MetalizerObject#wakeUp is called. Do nothing by default. Subclasses should override this method.
    */
   public function onWakeUp() {

   }

   /**
    * @return string
    * 	A string representation of the object.
    */
   public function toString() {
      return $this->getClass();
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

   /**
    * Called at the end of the application, just before every object are put in the sleep mode.
    */
   public function finalize() {
      $this->onFinalize();
   }

   /**
    * Called in MetalizerObject#finalize().
    * Subclasses should override it.
    */
   public function onFinalize() {

   }

}
