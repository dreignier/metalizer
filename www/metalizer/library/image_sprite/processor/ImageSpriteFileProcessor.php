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
 * The less css file processor for the bundle library.
 * @author David Reignier
 *
 */
class ImageSpriteFileProcessor extends DefaultFileProcessor {
   
   /**
    * @see BundleFileProcessor#path
    */
   public function path($pattern) {
      return PATH_RESOURCE_GEN . $pattern;
   }
   
   /**
    * @see BundleFileProcessor#url
    */
   public function url($path) {
      return resUrl($path, false);
   }
   
   /**
    * @see BundleFileProcessor#read
    */
   public function initialize($pattern) {
      $pattern = str_replace('*', '_star_', $pattern);
      util('ImageSprite')->sprite($pattern, $pattern);
   }
 
}   