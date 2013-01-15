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
 * A Worker in MetalizerObject is just a class with an execute method.
 * @author David Reignier
 *
 */
abstract class Worker extends MetalizerObject {
   
   /**
    * The name of the worker
    * @var string
    */
   protected $name;
   
   /**
    * The last execution of the worker, in second.
    * @var int
    */
   protected $lastExecution;
   
   public function __construct($name, $lastExecution) {
      $this->name = $name;
      $this->lastExecution = $lastExecution;
   }
   
   /**
    * Get a configuration value
    * @param $name string
    *    A configuration value name
    * @return mixed
    *    The corresponding configuration value for the current worker
    */
   public function getConfigurationValue($name) {
      return config("worker.workers.$this->name.$name");
   }
   
   /**
    * Execute the task of the Worker.
    */
   abstract function execute();
   
}   