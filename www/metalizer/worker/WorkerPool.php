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
 * Handle the workers.
 * @author David Reignier
 *
 */
class WorkerPool extends MetalizerObject {

   /**
    * The worker pool size
    * @var int
    */
   private $size;
   
   /**
    * The workers list.
    * @var array[string]
    */
   private $workers;
   
   /**
    * The execution list.
    * @var array[string]
    */
   private $executionList;
   
   /**
    * Retrieve the worker pool configuration and data
    */
   public function __construct() {
      $this->size = config("worker.pool_size");
      $this->workers = config("worker.workers");
      
      if ($executionList = store()->load("worker.pool.execution_list")) {
         // Remove old workers
         $tempList = array();
         foreach ($executionList as $worker) {
            if (in_array($worker, $this->workers)) {
               $tempList[] = $worker;
            }
         }
         $executionList = $tempList;
         
         // Add new workers
         foreach ($this->workers as $worker) {
            if (!in_array($worker, $executionList)) {
               $executionList[] = $worker;
            }
         }
         
         $this->executionList = $executionList;
      } else {
         $this->executionList = $this->workers;
      }
   }

   /**
    * Run the workers
    */
   public function run() {
      $workers = array();
      foreach ($this->executionList as $worker) {
         if ($this->isExecutable($worker)) {
            $workers[] = $worker;
            
            if (sizeof($workers) >= $this->size) {
               break;
            }
         }
      }
      
      foreach ($workers as $worker) {
         $this->execute($worker);
      }
      
      store()->store("worker.pool.execution_list", $this->executionList);
   }
   
   /**
    * @param $worker string
    *    A worker name
    * @return boolean
    *    <code>true</code> if the given worker can be executed, <code>false</code> otherwise.
    */
   private function isExecutable($worker) {
      if (!$this->getConfigurationValue($worker, 'enabled')) {
         return false;
      }
      
      $now = now();
      $interval = $this->getConfigurationValue($worker, 'interval');
      $lastExecution = store()->load("worker.$worker.last_execution");
      
      if ($lastExecution === null) {
         // Never executed
         return true;
      }
      
      return $lastExecution + $interval < $now;
   }
   
   /**
    * Execute a worker
    * @param $worker string
    *    A worker name.
    */
   private function execute($name) {
      if ($this->log()->isInfoEnabled()) {
         $this->log()->info("Execute $name");
      }
      
      $class = $this->getConfigurationValue($name, 'class');
 
      $lastExecution = store()->load("worker.$name.last_execution");
      if ($lastExecution === null) {
         // Never executed
         $lastExecution = 0;
      }
      
      $worker = new $class($name, $lastExecution);

      try {
         $worker->execute();
      } catch (Exception $exception) {
         if ($this->log()->isErrorEnabled()) {
            $this->log()->error('Exception occured : ' . get_class($exception) . ' (' . $exception->getCode() . ') ' . $exception->getMessage());
            $this->log()->error($exception->getFile() . '(' . $exception->getLine() . ')');
            $this->log()->error($exception->getTraceAsString());
         }
      }
      
      // Put the worker at the bottom of the execution list
      $pointer = 0;
      while ($this->executionList[$pointer] != $name) {
         $pointer += 1;
      }
      
      for (;$pointer < sizeof($this->executionList) - 1; ++$pointer) {
         $this->executionList[$pointer] = $this->executionList[$pointer + 1];
      }
      
      $this->executionList[$pointer] = $name;
      
      // Store the last execution
      store()->store("worker.$name.last_execution", now());
   }
   
   /**
    * Get a configuration value for a worker
    * @param $worker string
    *    A worker name
    * @param $value string
    *    The configuration value name
    * @param $default mixed
    *    The default value for the configuration value. Optional, null by default.
    * @return mixed
    *    The configuration value for the given parameters.
    */
   private function getConfigurationValue($worker, $value, $default = null) {
      return config("worker.workers.$worker.$value", $default);
   }

}