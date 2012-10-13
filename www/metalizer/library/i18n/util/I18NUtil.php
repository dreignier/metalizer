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
 
/**
 * Provide easy access to i18n functions.
 * @author David Reignier
 *
 */
class I18NUtil extends Util {
   
   private $i18n = null;
   
   public function load($language) {
      $lang = array();

      // Metalizer default lang files
      foreach (util('File')->glob(PATH_METALIZER_LANG . "$language/*.php") as $file) {
         require $file;
      }
      
      // Metalizer libraries lang files
      foreach (util('File')->glob(PATH_METALIZER_LIBRARY . "*/lang/$language/*.php") as $file) {
         require $file;
      }
      
      // Application libraries lang files
      foreach (util('File')->glob(PATH_APPLICATION_LIBRARY . "*/lang/$language/*.php") as $file) {
         require $file;
      }

      // Application lang files
      foreach (util('File')->glob(PATH_APPLICATION_LANG . "$language/*.php") as $file) {
         require $file;
      }

      $this->i18n = $lang;
   }
   
   public function i18n($key, $_ = null) {
      if ($this->i18n === null) {
         throw new I18NException('No language loaded');
      }
      
      if (isset($this->i18n[$key])) {
         $params = func_get_args();
         $params[0] = $this->i18n[$key];
         return call_user_func_array('sprintf', $params);
      } else {
         return '$' . $key . '$';
      }
   }
      
}

function i18n($key, $_ = null) {
   return call_user_func_array(array(util('I18N'), 'i18n'), func_get_args());
}
