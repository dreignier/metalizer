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
 * Can generate a CSS bundle.
 * @author David Reignier
 *
 */
class CssBundleGenerator extends BundleGenerator {

   /**
    * Construct a new CssBundleGenerator.
    */
   public function __construct() {
      parent::__construct('css');
   }

   /**
    * @see BundleGenerator#html
    */
   public function html($url) {
      echo '<link type="text/css" rel="stylesheet" href="' . $url . '" />';
   }

   /**
    * @see BundleGenerator#finalize
    */
   public function finalize($path) {
      $css = file_get_contents($path);

      // Simple CSS Minifier from http://www.lateralcode.com/css-minifier/
      $css = preg_replace('#\s+#', ' ', $css);
      $css = preg_replace('#/\*.*?\*/#s', '', $css);
      $css = str_replace('; ', ';', $css);
      $css = str_replace(': ', ':', $css);
      $css = str_replace(' {', '{', $css);
      $css = str_replace('{ ', '{', $css);
      $css = str_replace(', ', ',', $css);
      $css = str_replace('} ', '}', $css);
      $css = str_replace(';}', '}', $css);

      file_put_contents($path, $css);
   }

}
