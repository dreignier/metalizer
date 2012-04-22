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
	$pathInfo = explode(config('url.separator'), trim(@Util('Server')->get('PATH_INFO'), '/'));

	$pathInfoPointer = 0;
	$page = null;
	$method = null;
	$params = array();

	if ($pathInfo && is_array($pathInfo)) {
		$pathInfoSize = sizeof($pathInfo);
		$folder = PATH_APPLICATION_PAGE;

		// Search for the controller
		while (($pathInfoPointer < $pathInfoSize) && !$page) {
			$element = $pathInfo[$pathInfoPointer];
				
			if (is_dir($folder . $element)) {
				$folder = $folder . $element;
			} else if (class_exists($element) && is_subclass_of($element, 'Page')) {
				$page = $element;
			} else {
				throw new PageNotFoundException("Page not found : $page");
			}
				
			$pathInfoPointer += 1;
		}

		// Search for the method
		if ($pathInfoPointer < $pathInfoSize) {
			$method = $pathInfo[$pathInfoPointer];
			$pathInfoPointer += 1;
		}

		// Search for params
		while ($pathInfoPointer < $pathInfoSize) {
			$params[] = $pathInfo[$pathInfoPointer];
			$pathInfoPointer += 1;
		}

	}

	if ($page == null) {
		$page = config('page.name.default');
	}

	if (!$page || !class_exists($page) || !is_subclass_of($page, 'Page')) {
		throw new PageNotFoundException("page.name.default ($page) is not a valid Page class");
	}

	if ($method == null) {
		$method = config('page.method.default');
	}

	// Check if all is ok
	$reflectionClass = new ReflectionClass($page);

	if (!$reflectionClass->hasMethod($method)) {
		throw new PageNotFoundException("Method not found : $method");
	}

	$reflectionMethod = $reflectionClass->getMethod($method);

	if (!$reflectionMethod->isPublic() || $reflectionMethod->isStatic()) {
		throw new PageNotFoundException("Method not found : $method");
	}

	if (sizeof($params) < $reflectionMethod->getNumberOfRequiredParameters()) {
		throw new PageNotFoundException("Not enought argument");
	}

	// Let's go !
	$page = new $page();
	call_user_func_array(array($page, $method), $params);
} catch (Exception $exception) {
	if (class_exists('Error') && is_a('Error', 'Page')) {
		$page = new Error();
		$page->handle($exception);
	} else {
		echo 'Exception occured and no Error page found : ' . $exception->getMessage() . '<br/>';
		echo str_replace('#', '<br/>#', $exception->getTraceAsString());
	}
}

// *** Metalizer finalization ***

require PATH_METALIZER . 'finalize.php';
