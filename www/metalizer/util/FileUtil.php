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
 * Provide some helper for file and directory manipulation.
 * @author David Reignier
 *
 */
class FileUtil extends Util {

   /**
    * Check if the directory for a file path exists. If not, the directory is created.
    * @param $file string
    *    A file path.
    */
   public function checkDirectory($file) {
      if (file_exists($file)) {
         return;
      }

      $file = explode('/', $file);

      if (sizeof($file) > 0) {
         // Remove the file name
         $file = array_slice($file, 0, sizeof($file) - 1);

         // Remove the last /
         $path = substr(PATH_ROOT, 0, -1);
         foreach ($file as $directory) {
            $path = "$path/$directory";

            if (!file_exists($path)) {
               mkdir($path);
            }
         }
      }
   }

   /**
    * Remove a directory and its content, definitively.
    * @param $dir string
    *    The directory.
    */
   public function rmdir($dir) {
      if (is_dir($dir)) {
         $objects = @scandir($dir);
         if (is_array($objects)) {
            foreach ($objects as $object) {
               if ($object != "." && $object != "..") {
                  if (@filetype($dir . "/" . $object) == "dir") {
                     $this->rmdir($dir . "/" . $object);
                  } else {
                     @unlink($dir . "/" . $object);
                  }
               }
            }
         }   
         @reset($objects);
         @rmdir($dir);
      }
   }
   
   /**
    * Same as php glob, but defaults flags are not the same and there's an extra parameter.
    * @todo handle the ** joker
    * @param $pattern string
    *    The pattern for glob
    * @param $onlyFile blool
    *    If true, only file are returned. Optional. True by default.
    * @param $flags int
    *    Optional. Default : GLOB_MARK | GLOB_BRACE
    *    GLOB_ONLYFILE is a metalizer flag. 
    */
   public function glob($pattern, $onlyFile = true, $flags = null) {
      if ($flags === null) {
         $flags = GLOB_MARK + GLOB_BRACE;
      }
      
      $glob = glob($pattern, $flags);
      
      $result = array();
      foreach ($glob as $value) {
         if (!$onlyFile || !is_dir($value)) {
            $result[$value] = str_replace('\\', '/', $value);
         }
      }
      
      return $result;
   }
}
