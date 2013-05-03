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

define('PATH_METALIZER_LANG', PATH_METALIZER . 'lang/');
define('PATH_APPLICATION_LANG', PATH_APPLICATION . 'lang/');
define('PATH_RESOURCE_JS_LANG', PATH_RESOURCE_GEN . 'lang/');

/**
 * Provide easy access to i18n functions.
 * @author David Reignier
 *
 */
class I18NUtil extends Util {

   /**
    * All the lang keys
    * @var array[string];
    */
   private $i18n = null;

   /**
    * The current language.
    * @var string
    */
   private $currentLanguage = null;

   public function __construct() {
      if (!isDevMode()) {
         _file()->checkDirectory(PATH_RESOURCE_JS_LANG);
         // Generate all lang js files once for all.
         $page = new I18NJsPage();
         foreach (config('lang.languages') as $language) {
            $file = PATH_RESOURCE_JS_LANG . "$language.js";
            if (!file_exists($file)) {
               $this->load($language);
               ob_start();
               $page->generate($language, $this->getAll());
               $page->display();
               $content = ob_get_clean();
               file_put_contents($file, $content);
            }
         }
      }
   }

   /**
    * Check if a language is available.
    * @param $language string
    *    A language key
    */
   private function checkLanguage($language) {
      $availableLanguages = config('lang.languages');

      if (!in_array($language, $availableLanguages)) {
         throw new I18NException("$language is not available. See the 'lang.languages' configuration for more informations");
      }
   }

   /**
    * Load a language.
    * @param $language string
    *    The language to load.
    */
   public function load($language) {
      if ($this->log()->isInfoEnabled()) {
         $this->log()->info("Loading $language");
      }
      
      $this->checkLanguage($language);

      if ($this->log()->isInfoEnabled()) {
         $this->log()->info("Reading lang files");
      }
      
      $lang = array();

      // Metalizer default lang files
      foreach (_file()->glob(PATH_METALIZER_LANG . "$language/*.php") as $file) {
         require $file;
      }

      // Metalizer libraries lang files
      foreach (_file()->glob(PATH_METALIZER_LIBRARY . "*/lang/$language/*.php") as $file) {
         require $file;
      }

      // Application libraries lang files
      foreach (_file()->glob(PATH_APPLICATION_LIBRARY . "*/lang/$language/*.php") as $file) {
         require $file;
      }

      // Application lang files
      foreach (_file()->glob(PATH_APPLICATION_LANG . "$language/*.php") as $file) {
         require $file;
      }

      $this->i18n = $lang;

      $this->currentLanguage = $language;
   }

   /**
    * Get a i18n value.
    * @param $key string
    *    The wanted i18n key.
    * @param $_ ...
    *    All extra parameters are given to the sprintf function with the i18n value.
    * @return string
    *    The i18n value. If the key does not exist, it return '$' . $key '$'.
    */
   public function i18n($key, $_ = null) {
      if ($this->i18n === null) {
         $this->load(config('lang.default'));
      }

      if (isset($this->i18n[$key])) {
         $params = func_get_args();
         $params[0] = $this->i18n[$key];
         return call_user_func_array('sprintf', $params);
      } else {
         return '$' . $key . '$';
      }
   }
   
   /**
    * Include a internotialized text.
    * @param $key string
    *    The text key
    */
   public function text($key) {
      $file = PATH_APPLICATION_LANG . $this->currentLanguage . "/text/$key.php";
      
      if (!file_exists($file)) {
         // Look in metalizer folder
         $file = PATH_METALIZER_LANG . $this->currentLanguage . "/text/$key.php";
         
         if (!file_exists($file)) {
            // Key not found
            echo '$' . "$key.text" . '$';
            return;
         }
      }
      
      include($file);
   }

   /**
    * Test if a key exists.
    * @param $key string
    *    An i18n key
    * @return bool
    *    True if the key exists, false otherwise.
    */
   public function has($key) {
      return isset($this->i18[$key]);
   }

   /**
    * @return string
    *    The current language.
    */
   public function getCurrentLanguage() {
      return $this->currentLanguage;
   }

   /**
    * @return array[string]
    *    All i18n values.
    */
   public function getAll() {
      return $this->i18n;
   }

   /**
    * @param $language string
    *     A language
    * @return string
    *    The url to the js file for the given language
    */
   public function jsFileUrl($language) {
      $this->checkLanguage($language);

      if (isDevMode()) {
         return randomParamUrl(siteUrl("i18n/js/$language"));
      } else {
         return resUrl(PATH_RESOURCE_JS_LANG . "$language.js", false);
      }
   }
   
   /**
    * Load a language according to the Accept-Language header.
    */
   public function loadFromAcceptLanguage() {
      $acceptLanguage = server()->get('HTTP_ACCEPT_LANGUAGE');
      
      $languages;
      if (strpos($acceptLanguage, ',') !== false) {
         $languages = explode(',', $acceptLanguage);
      } else {
         $languages = array($acceptLanguage);
      }
      
      foreach ($languages as $language) {
         foreach (config('lang.languages') as $available) {
            if (strpos($language, $available) !== false) {
               $this->load($available);
               return;
            }
         }
      }
      
      $this->load(config('lang.default'));
   }

}

function i18n($key, $_ = null) {
   return call_user_func_array(array(util('I18N'), 'i18n'), func_get_args());
}

function getCurrentLanguage() {
   return util('I18N')->getCurrentLanguage();
}

function i18nJsFileUrl($language) {
   return util('I18N')->jsFileUrl($language);
}
