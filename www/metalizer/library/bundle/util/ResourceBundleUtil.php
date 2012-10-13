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
class ResourceBundleUtil extends Util {

   /**
    * Construct a new ResourceBundleUtil.
    */
   public function __construct() {
      
   }
   
   /**
    * Generic method to generate a bundle
    * @param $bundle string
    *    The bundle name
    * @param $files string
    *    The files in the bundle
    * @param $generator BundleGenerator
    *    The generator for the bundle.
    */
   private function bundle($bundle, $files, $generator) {
      $generator->generate($bundle, $files);
   }
      
   /**
    * Generate a css bundle.
    * @param $bundle string
    *    The bundle name
    * @param $files string
    *    The files in the bundle
    */
   public function css($bundle) {
      $files = config("bundle.$bundle.css");
      
      if (!$files) {
         throw new BundleException("Can't find '$bundle' css bundle configuration");
      }
      
      $this->bundle($bundle, $files, new CssBundleGenerator());
   }
   
   /**
    * Generate a js bundle.
    * @param $bundle string
    *    The bundle name
    * @param $files string
    *    The files in the bundle
    */
   public function js($bundle) {
      $files = config("bundle.$bundle.js");
      
      if (!$files) {
         throw new BundleException("Can't find '$bundle' js bundle configuration");
      }
      
      $this->bundle($bundle, $files, new JsBundleGenerator());
   }
   
}

/**
 * @see ResourceBundleUtil#css
 */
function cssBundle($bundle) {
   util('ResourceBundle')->css($bundle);
}

/**
 * @see ResourceBundleUtil#js
 */
function jsBundle($bundle) {
   util('ResourceBundle')->js($bundle); 
}
