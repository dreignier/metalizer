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
   
   /**
    * The random parameter used for resources url.
    * @var int
    */
   private $randomParam;
   
   public function __construct() {
      $this->randomParam = rand(10000, 99999);
   }
   
   /**
    * @param $url string
    *    An url.
    * @return string
    *    $url with the url.root configuration value before.
    */
   private function url($url) {
      if (substr($url, 0, 1) == '/') {
         $url = substr($url, 1);
      }
      return config('url.root') . $url;
   }
   
   /**
    * @param $url string
    *    An url.
    * @return string
    *    $url with the random parameter in production mode. In development mode, this method return $url.
    */
   public function randomParamUrl($url) {
      return $url . (strpos($url, '?') !== false ? '&' : '?') . '_=' . $this->getRandomParam();
   }
   
   /**
    * @return int
    *    The random parameter used for resources url.
    */
   public function getRandomParam() {
      return $this->randomParam;
   }
   
   /**
    * @param $url string
    *    An url.
    * @return string
    *    A site url.
    */
   public function site($url) {
      if (substr($url, 0, 1) != '/') {
         $url = "/$url";
      }
      
      return $this->url(config('url.site.base') . $url);
   }
   
   /**
    * @param $url string
    *    An url.
    * @return string
    *    A css url.
    */
   public function css($url) {
      return $this->randomParamUrl($this->url(PATH_RESOURCE_CSS . $url)); 
   }
   
   /**
    * @param $url string
    *    An url.
    * @return string
    *    A js url.
    */
   public function js($url) {
      return $this->randomParamUrl($this->url(PATH_RESOURCE_JS . $url));
   }
   
   /**
    * @param $url string
    *    An url.
    * @return string
    *    A image url.
    */
   public function img($url) {
      return $this->randomParamUrl($this->url(PATH_RESOURCE_IMG . $url));
   }
   
   /**
    * @param $url string
    *    An url.
    * @param $prefix bool
    *    If true, the url is prefixed with the resource path.
    * @return string
    *    A resource url.
    */
   public function res($url, $prefix = true) {
      return $this->randomParamUrl($this->url(($prefix ? PATH_RESOURCE : '') . $url));
   }
} 

/**
 * @see UrlUtil#site
 */
function siteUrl($url) {
   return util('Url')->site($url);
}

/**
 * @see UrlUtil#css
 */
function cssUrl($url) {
   return util('Url')->css($url);
}

/**
 * @see UrlUtil#js
 */
function jsUrl($url) {
   return util('Url')->js($url);
}

/**
 * @see UrlUtil#img
 */
function imgUrl($url) {
   return util('Url')->img($url);
}

/**
 * @see UrlUtil#res
 */
function resUrl($url, $prefix = true) {
   return util('Url')->res($url, $prefix);
}

/**
 * @see UrlUtil#randomParamUrl
 */
function randomParamUrl($url) {
   return util('Url')->randomParamUrl($url);
}
