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

// *** Contants defines ***

define('PATH_ROOT', './');

define('PATH_METALIZER', PATH_ROOT . 'metalizer/');
define('PATH_METALIZER_CONTROLLER', PATH_ROOT . 'metalizer/controller/');
define('PATH_METALIZER_CORE', PATH_ROOT . 'metalizer/core/');
define('PATH_METALIZER_DATABASE', PATH_ROOT . 'metalizer/database/');
define('PATH_METALIZER_EXCEPTION', PATH_ROOT . 'metalizer/exception/');
define('PATH_METALIZER_MANAGER', PATH_ROOT . 'metalizer/manager/');
define('PATH_METALIZER_MODEL', PATH_ROOT . 'metalizer/model/');
define('PATH_METALIZER_UTIL', PATH_ROOT . 'metalizer/util/');
define('PATH_METALIZER_VIEW', PATH_ROOT . 'metalizer/view/');
define('PATH_METALIZER_CONFIGURATION', PATH_ROOT . 'metalizer/configuration/');

define('PATH_APPLICATION', PATH_ROOT . 'application/');
define('PATH_APPLICATION_PAGE', PATH_ROOT . 'application/page/');
define('PATH_APPLICATION_MODEL', PATH_ROOT . 'application/model/');
define('PATH_APPLICATION_UTIL', PATH_ROOT . 'application/util/');
define('PATH_APPLICATION_TEMPLATE', PATH_ROOT . 'application/template/');
define('PATH_APPLICATION_CHROME', PATH_ROOT . 'application/chrome/');
define('PATH_APPLICATION_CONFIGURATION', PATH_ROOT . 'application/configuration/');
define('PATH_APPLICATION_WEBSCRIPT', PATH_ROOT . 'application/webscript/');

define('PATH_CACHE', PATH_ROOT . '../cache/');
define('PATH_LOG', PATH_ROOT . '../log/');
define('PATH_DATA', PATH_ROOT . '../data/');

// *** Metalizer initialization ***

require PATH_METALIZER . 'initialize.php';

require PATH_APPLICATION_PAGE . 'Home.php';

error_reporting(-1);

try {

	// *** Create the page ***
	$pathInfo = trim(@Util('Server')->get('PATH_INFO'));

	$pathInfoPointer = 0;
	$page = null;
	$params = array();

	if (!$pathInfo || $pathInfo == '/') {
		$page = config('page.home');
	} else {
		foreach(config('page.patterns') as $name => $pattern) {
			if (preg_match("@^$pattern$@", $pathInfo, $params)) {
				$page = $name;
				break;
			}
		}
		
		if (!$page) {
			throw new PageNotFoundException();
		}
	}
	
	if (!$page || !class_exists($page) || !is_subclass_of($page, 'Page')) {
		throw new PageNotFoundException("$page is not a valid Page class");
	}

	// Check if all is ok
	$reflectionClass = new ReflectionClass($page);
	
	if (!$reflectionClass->hasMethod('execute')) {
		throw new NotImplementedException("Page found but not implemented");
	}
	
	$reflectionMethod = $reflectionClass->getMethod('execute');

	if (!$reflectionMethod->isPublic() || $reflectionMethod->isStatic() || $reflectionMethod->isAbstract()) {
		throw new NotImplementedException("The method 'execute' in the $page class is not valid");
	}
	
	// Let's go !
	ob_start();
	$page = new $page();
	call_user_func_array(array($page, 'execute'), $params);
	ob_end_flush();
} catch (Exception $exception) {
	header("HTTP/1.1 " . (is_a($exception, 'HttpException') ? $exception->getCode() : 500));
	ob_end_clean();
	if (class_exists('Error') && is_a('Error', 'Page')) {
		$page = new Error();
		$page->execute($exception);
	} else {
		// Default error handle
		echo 'Exception occured : (' . $exception->getCode() . ') ' . $exception->getMessage() . '<br/>';
		echo $exception->getFile() . '(' . $exception->getLine() . ')';
		echo str_replace('#', '<br/>#', $exception->getTraceAsString());
	}
}

// *** Metalizer finalization ***

require PATH_METALIZER . 'finalize.php';
