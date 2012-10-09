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
   
   public function html($url) {
      echo  '<link type="text/css" rel="stylesheet" href="' . $url . '" />';
   }
   
   public function resolveFileUrl($file) {
      if (isLibraryExists('less_css') && substr($file, -5) == '.less') {
         return lessCssUrl($file);
      } else {
         return cssUrl($file);
      }
   }
   
   public function resolveFilePath($file) {
      return PATH_RESSOURCE_CSS . $file;
   }
   
   public function resolveBundlePath($bundle) {
      return PATH_RESSOURCE_BUNDLE_CSS . str_replace('.', '/', $bundle) . '.css';
   }
   
   public function resolveBundleUrl($bundle) {
      return resUrl('bundle/css/' . str_replace('.', '/', $bundle) . '.css');
   }
   
   public function readFile($file) {
      if (isLibraryExists('less_css') && substr($file, -5) == '.less') {
         return util('LessCss')->compile($file);
      } else {
         return file_get_contents($file);
      }
   }
   
   public function filePathToUrl($path) {
      return $this->resolveFileUrl(substr($path, strlen(PATH_RESSOURCE_CSS)));
   }
   
   public function minify($file) {
   }
}      