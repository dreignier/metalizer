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
 * Provide helpers for libraries.
 * @author David Reignier
 *
 */
class LibraryUtil extends Util {

   /**
    * Keep a cache for libraries paths.
    * @var array[string]
    */
   private $cache = array();

   /**
    * Get a library path
    * @param $name string
    *    A library name.
    * @return string
    *    The path to the library directory.
    * @throws LibraryException
    *    If the given library doesn't exist.
    */
   public function path($name) {
      if (isset($this->cache[$name])) {
         return $this->cache[$name];
      }

      $paths = array(PATH_APPLICATION_LIBRARY . "$name/", PATH_METALIZER_LIBRARY . "$name/");

      foreach ($paths as $path) {
         if (is_dir($path)) {
            $this->cache[$name] = $path;
            return $path;
         }
      }

      throw new LibraryException("Library not found : $name");
   }

   /**
    * @return boolean
    *    true if the given library exists, false otherwise.
    */
   public function exists($name) {
      try {
         $this->getPath($name);
         return true;
      } catch (LibraryException $exception) {
         return false;
      }
   }

}

/**
 * @return LibraryUtil
 */
function library() {
   return util('Library');
}

/**
 * @see LibraryUtil#getPath
 */
function getLibraryPath($name) {
   return util('Library')->path($name);
}

/**
 * @see LibraryUtil#exists
 */
function isLibraryExists($name) {
   return util('Library')->exists($name);
}
