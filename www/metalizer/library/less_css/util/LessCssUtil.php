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
class LessCssUtil extends Util {
      
   /**
    * The less compiler.
    * @var lessc
    */
   private $lessc = null;
   
   /**
    * Compile a less file.
    * @param $inFile string
    *    The input file.
    * @param $outFile
    *    Optional. If given, this file is used as the output.
    * @return mixed
    *    Return the result if $outFile is missing. Otherwise, it return nothing.
    */
   public function compile($inFile, $outFile = null) {
      require_once getLibraryPath('less_css') . 'external/lessc.php';
      
      if (!$this->lessc) {
         $this->lessc = new lessc();
      }
      
      if ($this->log()->isDebugEnabled()) {
         $this->log()->debug("Compile $inFile");
      }
      
      $result = $this->lessc->compileFile($inFile, $outFile);
      
      if (!$outFile) {
         return $result;
      }
   }
   
   /**
    * Create an url for a less file.
    * @param $file string
    *    The file name.
    * @return string
    *    The url for the less file.
    */
   public function url($file) {
      return randomParamUrl(siteUrl("less/$file"));
   }
   
}

/**
 * @see LessCssUtil#url
 */
function lessCssUrl($file) {
   return util('LessCss')->url($file);
}
