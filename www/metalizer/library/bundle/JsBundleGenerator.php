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
   
   public function html($url) {
      echo '<script type="text/javascript" src="' . $url . '" /></script>';
   }
   
   public function resolveFileUrl($file) {
      return jsUrl($file);
   }
   
   public function resolveFilePath($file) {
      return PATH_RESSOURCE_JS. $file;
   }
   
   public function resolveBundlePath($bundle) {
      return PATH_RESSOURCE_BUNDLE_JS . str_replace('.', '/', $bundle) . '.js';
   }
   
   public function resolveBundleUrl($bundle) {
      return resUrl('bundle/js/' . str_replace('.', '/', $bundle) . '.js');
   }
   
   public function readFile($file) {
      return file_get_contents($file);
   }
   
   public function minify($file) {
      require_once getLibraryPath('bundle') . 'external/jsmin.php';
      file_put_contents($file, JSMin::minify(file_get_contents($file)));
   }
   
   public function filePathToUrl($path) {
      return $this->resolveFileUrl(substr($path, strlen(PATH_RESSOURCE_JS)));
   }
}      