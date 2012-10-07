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

define('PATH_RESSOURCE_BUNDLE', PATH_RESSOURCE . 'bundle/');
define('PATH_RESSOURCE_BUNDLE_CSS', PATH_RESSOURCE_BUNDLE . 'css/');
define('PATH_RESSOURCE_BUNDLE_JS', PATH_RESSOURCE_BUNDLE . 'js/');
 
/**
 * Provide some helper for file and directory manipulation.
 * @author David Reignier
 *
 */
class ResourceBundleUtil extends Util {
   
   /**
    * The css bundle generator
    * @var CssBundleGenerator
    */
   private $cssGenerator;
   
   /**
    * The js bundle generator
    * @var JsBundleGenerator
    */
   private $jsGenerator;
   
   /**
    * Construct a new ResourceBundleUtil.
    */
   public function __construct() {
      // Clean the bundle folders
      util('File')->rmdir(PATH_RESSOURCE_BUNDLE);
      mkdir(PATH_RESSOURCE_BUNDLE);
      mkdir(PATH_RESSOURCE_BUNDLE_JS);
      mkdir(PATH_RESSOURCE_BUNDLE_CSS);
      
      $this->cssGenerator = new CssBundleGenerator();
      $this->jsGenerator = new JsBundleGenerator();
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
   public function css($bundle, $files) {
      $this->bundle($bundle, $files, $this->cssGenerator);
   }
   
   /**
    * Generate a js bundle.
    * @param $bundle string
    *    The bundle name
    * @param $files string
    *    The files in the bundle
    */
   public function js($bundle, $files) {
      $this->bundle($bundle, $files, $this->jsGenerator);
   }
   
}

/**
 * @see ResourceBundleUtil#css
 */
function cssBundle($bundle, $files) {
   util('ResourceBundle')->css($bundle, $files);
}

/**
 * @see ResourceBundleUtil#js
 */
function jsBundle($bundle, $files) {
   util('ResourceBundle')->js($bundle, $files); 
}
