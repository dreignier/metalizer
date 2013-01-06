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
 * Index file of the application. All pages are start here.
 */
 
// *** Some very basic fonctions. For debug only ***

function debug($message) {
   if (is_object($message) && is_a($message, 'Exception')) {
      echo 'Exception occured : (' . $message->getCode() . ') ' . $message->getMessage() . '<br/>';
      echo $message->getFile() . '(' . $message->getLine() . ')';
      echo str_replace('#', '<br/>#', $message->getTraceAsString());
   } else {
      echo $message . '<br/>';
   }
}


function bench() {
   static $last = null;
   
   if (!$last) {
      $last = microtime();
   } else {
      $now = microtime();
      debug("Bench : " . ($now - $last));
      $last = $now;
   }
}
bench();
 
// *** Contants defines ***

define('PATH_ROOT', './');

define('PATH_METALIZER', PATH_ROOT . 'metalizer/');
define('PATH_METALIZER_CONTROLLER', PATH_METALIZER . 'controller/');
define('PATH_METALIZER_CORE', PATH_METALIZER . 'core/');
define('PATH_METALIZER_DATABASE', PATH_METALIZER . 'database/');
define('PATH_METALIZER_EXCEPTION', PATH_METALIZER . 'exception/');
define('PATH_METALIZER_MANAGER', PATH_METALIZER . 'manager/');
define('PATH_METALIZER_MODEL', PATH_METALIZER . 'model/');
define('PATH_METALIZER_UTIL', PATH_METALIZER . 'util/');
define('PATH_METALIZER_VIEW', PATH_METALIZER . 'view/');
define('PATH_METALIZER_CONFIGURATION', PATH_METALIZER . 'configuration/');
define('PATH_METALIZER_EXTERNAL', PATH_METALIZER . 'external/');
define('PATH_METALIZER_LIBRARY', PATH_METALIZER . 'library/');

define('PATH_APPLICATION', PATH_ROOT . 'application/');
define('PATH_APPLICATION_PAGE', PATH_APPLICATION . 'page/');
define('PATH_APPLICATION_MODEL', PATH_APPLICATION . 'model/');
define('PATH_APPLICATION_UTIL', PATH_APPLICATION . 'util/');
define('PATH_APPLICATION_TEMPLATE', PATH_APPLICATION . 'template/');
define('PATH_APPLICATION_CHROME', PATH_APPLICATION . 'chrome/');
define('PATH_APPLICATION_CONFIGURATION', PATH_APPLICATION . 'configuration/');
define('PATH_APPLICATION_LIBRARY', PATH_APPLICATION . 'library/');

define('PATH_RESOURCE', 'resource/');
define('PATH_RESOURCE_CSS', PATH_RESOURCE . 'css/');
define('PATH_RESOURCE_JS', PATH_RESOURCE . 'js/');
define('PATH_RESOURCE_IMG', PATH_RESOURCE . 'img/');
define('PATH_RESOURCE_GEN', PATH_RESOURCE . 'gen/');

define('PATH_CACHE', PATH_ROOT . '../cache/');
define('PATH_LOG', PATH_ROOT . '../log/');
define('PATH_DATA', PATH_ROOT . '../data/');

// *** Metalizer initialization ***

require PATH_METALIZER . 'initialize.php';

error_reporting(config('error.reporting'));
set_time_limit(config('php.time_limit'));

try {
   // *** Resolve the page, the method and the parameters ***
	$pathInfo = trim(@server()->get('PATH_INFO'));
   $chainer = new FilterChainer($pathInfo);
   $pathInfo = $chainer->run();
   $resolver = new PageResolver($pathInfo);

   // Let's go !
   ob_start();
   $page = $resolver->run();
   
   $output = ob_get_clean();
   
   if (config('output.clean') && extension_loaded('tidy') && class_exists('tidy') && $page->cleanOutput()) {
      $configuration = config('output.clean.configuration');
      $tidy = new tidy();
      $tidy->parseString($output, $configuration, 'utf8');
      $tidy->cleanRepair();
      $output = $tidy->html()->value;
      
      // Fix a tidy bug with DOCTYPE
      if ($configuration['doctype'] && substr($output, 0, 9) != '<!DOCTYPE') {
         $output = $configuration['doctype'] . "\n$output";
      }
   }
   
   echo $output;
} catch (Exception $exception) {
   _header()->setHttpResponseCode((is_a($exception, 'HttpException') ? $exception->getCode() : 500));
   ob_end_clean();

   logError('Exception occured : (' . $exception->getCode() . ') ' . $exception->getMessage());
   logError($exception->getFile() . '(' . $exception->getLine() . ')');
   logError($exception->getTraceAsString());

   if (config('page.error') && class_exists(config('page.error')) && is_a(config('page.error'), 'Page')) {
      $page = new Error();
      call_user_func_array(array($page, config('page.default_method')));
   } else {
      // Default error handle
      echo 'Exception occured : (' . $exception->getCode() . ') ' . $exception->getMessage() . '<br/>';
      echo $exception->getFile() . '(' . $exception->getLine() . ')';
      echo str_replace('#', '<br/>#', $exception->getTraceAsString());
   }
}

// *** Metalizer finalization ***

require PATH_METALIZER . 'finalize.php';
