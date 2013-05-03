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
 
$scriptStart = microtime(true);
 
// *** Some very basic functions. For debug only ***

function debug($message) {
   if (is_object($message) && $message instanceof Exception) {
      echo '<code>Exception occured : (' . $message->getCode() . ') ' . $message->getMessage() . '<br/>';
      echo $message->getFile() . '(' . $message->getLine() . ')';
      echo str_replace('#', '<br/>#', $message->getTraceAsString()) . '</code>';
   } else {
      echo "<code>$message</code><br/>";
   }
}


function bench($message = '') {
   static $last = null;
   
   if (!$last) {
      $last = microtime(true);
   } else {
      $now = microtime(true);
      debug("Bench $message : " . ($now - $last));
      $last = $now;
   }
}
bench();

require 'path.php';

// *** Metalizer initialization ***

require PATH_METALIZER . 'initialize.php';

try {
   // 200 is the default response
   _header()->setHttpResponseCode(200);
   
   // *** Header handling ***
   // Set some header value according to the configuration
   foreach (config('header.default') as $header => $value) {
      if ($value) {
         logTrace("Set header : '$header' = '$value'");
         _header()->set($header, $value);
      } else {
         logTrace("Remove header : '$header'");
         _header()->remove($header);
      }
   }

   // *** Resolve the path ***
   $pathInfo = trim(@server()->get('PATH_INFO'));
   logDebug("Path info : $pathInfo");
   
   // *** Execute the filter chain ***
   $chainer = new FilterChainer($pathInfo);
   $chainer->run();
   metalizer()->setPath($chainer->getPath());
   
   $resolver = new PageResolver(metalizer()->path());
   
   // Let's go !
   ob_start();
   $page = $resolver->run();
   
   $output = ob_get_clean();
   
   if (config('output.clean') && extension_loaded('tidy') && class_exists('tidy') && $page->cleanOutput()) {
      logDebug('Cleaning ouput');
      
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
   _header()->setHttpResponseCode($exception instanceof HttpException ? $exception->getCode() : 500);
   ob_end_clean();

   logError('Exception occured : ' . get_class($exception) . ' (' . $exception->getCode() . ') ' . $exception->getMessage());
   logError($exception->getFile() . '(' . $exception->getLine() . ')');
   logError($exception->getTraceAsString());

   if (config('page.error') && class_exists(config('page.error')) && config('page.error') instanceof Page) {
      $page = new Error();
      call_user_func_array(array($page, config('page.default_method')));
      $age->display();
   } else {
      // Default error handle
      echo 'Exception occured : ' . get_class($exception) . ' (' . $exception->getCode() . ') ' . $exception->getMessage() . '<br/>';
      echo $exception->getFile() . '(' . $exception->getLine() . ')';
      echo str_replace('#', '<br/>#', $exception->getTraceAsString());
   }
}

// *** Metalizer finalization ***

require PATH_METALIZER . 'finalize.php';

logDebug('Page executed in ' . (microtime(true) - $scriptStart));

logDebug('Metalizer end');
