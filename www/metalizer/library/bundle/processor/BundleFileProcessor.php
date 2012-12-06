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
 * A FileProcessor can be used by a BundleGenerator to handle bundle files.
 * @author David Reignier
 *
 */
abstract class BundleFileProcessor extends MetalizerObject {
   
   /**
    * The name of the bundle
    * @var string
    */
   private $name;
   
   /**
    * Construct a new BundleFileProcessor
    * @param $name string
    *    The name of the bundle
    */
   public function __construct($name) {
      $this->name = $name;
   }
   
   /**
    * @return string
    *    The name of the bundle
    */
   public function getName() {
      return $this->name;
   }
   
   /**
    * @param $path string
    *    A path to a file
    * @return boolean
    *    <code>true</code> if the given file is valid for the current bundle, <code>false</code> otherwise.
    */
   abstract public function isValid($path);
   
   /**
    * @param $pattern string
    *    A glob pattern
    * @return string
    *    The good path for the given pattern.
    */
   abstract public function path($pattern);
   
   /**
    * @param $path string
    *    A path to a file
    * @return string
    *    The good url for the given file.
    */
   abstract public function url($path);
   
   /**
    * Called only in production. Read the content of a file. 
    * @param $path string
    *    A path to a file
    * @return string
    *    The content of the given file.
    */
   abstract public function read($path);
   
   /**
    * Initialize a pattern before the bundle creation.
    * @param $pattern string
    *    A glob pattern
    */
   abstract public function initialize($pattern);
   
}   