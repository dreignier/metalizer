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
 * Provide helper for URL.
 * @author David Reignier
 *
 */
class UrlUtil extends Util {
   
   private $randomParam;
   
   public function __construct() {
      $this->randomParam = rand(10000, 99999);
   }
   
   private function url($url) {
      return config('url.root') . $url;
   }
   
   private function resourceUrl($url) {
      if (isDevMode()) {
         return $url . '?_=' . $this->getRandomParam();
      } else {
         return $url;
      }
   }
   
   public function getRandomParam() {
      return $this->randomParam;
   }
   
   public function site($url) {
      return $this->url(config('url.site.base') . $url);
   }
   
   public function css($url) {
      return $this->resourceUrl($this->url(PATH_RESSOURCE_CSS . $url)); 
   }
   
   public function js($url) {
      return $this->resourceUrl($this->url(PATH_RESSOURCE_JS . $url));
   }
   
   public function img($url) {
      return $this->resourceUrl($this->url(PATH_RESSOURCE_IMG . $url));
   }
   
   public function res($url) {
      return $this->resourceUrl($this->url(PATH_RESSOURCE . $url));
   }
} 

function siteUrl($url) {
   return Util('Url')->site($url);
}

function cssUrl($url) {
   return Util('Url')->css($url);
}

function jsUrl($url) {
   return Util('Url')->js($url);
}

function imgUrl($url) {
   return Util('Url')->img($url);
}

function resUrl($url) {
   return Util('Url')->res($url);
}