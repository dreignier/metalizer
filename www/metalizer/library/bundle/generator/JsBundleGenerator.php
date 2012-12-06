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
 * Can generate a JS bundle.
 * @author David Reignier
 *
 */
class JsBundleGenerator extends BundleGenerator {
   
   /**
    * Construct a new JsBundleGenerator
    */
   public function __construct() {
      parent::__construct('js');
   }
   
  /**
    * @see BundleGenerator#html
    */
   public function html($url) {
      echo '<script type="text/javascript" src="' . $url . '" /></script>';
   }
   
  /**
    * @see BundleGenerator#finalize
    */
   public function finalize($path) {
      require_once getLibraryPath('bundle') . 'external/jsmin.php';
      
      $content = file_get_contents($path);
      $content = JSMin::minify($content);
      
      // Fix a JSMin bug with the '+ ++' sequence (like in this.id="ui-id"+ ++n)
      $content = str_replace('+++', '+ ++', $content);
      
      file_put_contents($path, $content);
   }
   
}      