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
 * Can find the called Page and the method to use.
 * @author David Reignier
 *
 */
class PageResolver extends MetalizerObject {

	/**
	 * The pathInfo
	 * @var string
	 */
	private $pathInfo;

	/**
	 * The page to use
	 * @var string
	 */
	private $page;

	/**
	 * The method to use
	 * @var string
	 */
	private $method;
	
	/**
	 * Params for the method
	 * @var array[string]
	 */
	private $params;

	/**
	 * Construct a new PageResolver. All test are done.
	 * @param $pathInfo string
	 * 	The path info to use.
	 */
	public function __construct($pathInfo) {
		$this->pathInfo = $pathInfo;
		
		if ($this->log()->isInfoEnabled()) {
			$this->log()->info(getIp() . ":$pathInfo");
		}
		
		$this->page = null;
		$this->method = config('page.default_method');
		$this->params = array();
		
		if ($pathInfo && $pathInfo != '/') {
			foreach (config('page.patterns') as $pattern => $name) {
				if (preg_match("@^$pattern$@", $pathInfo, $this->params)) {
					$this->page = $name;
					
					if ($this->log()->isTraceEnabled()) {
						$this->log()->trace("Page pattern : $pattern");
						$this->log()->trace("Page name : $name");
					}
					
					break;
				}
			}
		} else {
			$this->page = config('page.home');
		}

		if (!$this->page) {
			throw new NotFoundException();
		}
      
      // Handle the http method
      if (is_array($this->page)) {
         $method = getRequestMethod();
         
         if (isset($this->page[$method])) {
            $this->page = $this->page[$method];
         } else {
            throw new MethodNotAllowedException();
         }
      }

		$separatorPos = strpos($this->page, ':');
		if ($separatorPos !== false) {
			$splittedPage = explode(":", $this->page);
			$this->page = $splittedPage[0];
			$this->method = $splittedPage[1];
		}
		
		if (sizeof($this->params)) {
			$this->params = array_slice($this->params, 1);
		}
		
		if ($this->log()->isTraceEnabled()) {
			$this->log()->trace("Page class : $this->page");
			$this->log()->trace("Page method : $this->method");
			$this->log()->trace("Page parameters : " . implode(' / ', $this->params));
		}

		if (!$this->page || !$this->method) {
			throw new InternalErrorException();
		}

		if (!class_exists($this->page) || !is_subclass_of($this->page, 'Page')) {
			throw new InternalErrorException("$this->page is not a valid Page class");
		}

		// Check if all is ok
		$reflectionClass = new ReflectionClass($this->page);

		if (!$reflectionClass -> hasMethod($this->method)) {
			throw new InternalErrorException("There's no '$this->method' in the page '$this->page'");
		}

		$reflectionMethod = $reflectionClass -> getMethod($this->method);

		if (!$reflectionMethod -> isPublic() || $reflectionMethod -> isStatic() || $reflectionMethod -> isAbstract()) {
			throw new InternalErrorException("The method '$method' in the $page class is not valid");
		}

		if ($reflectionMethod -> getNumberOfRequiredParameters() > sizeof($this->params)) {
			throw new InternalErrorException("Method '$this->method' found in the $this->page class but require " . $reflectionMethod -> getNumberOfRequiredParameters() . " parameters (" . sizeof($this->params) . " given)");
		}
	}

	/**
	 * Execute the page of the resolver with the method and parameters.
    * @return Page
    *    The page object.
	 */
	public function run() {
		$class = $this->page;
		$page = new $class();
		call_user_func_array(array($page, $this->method), $this->params);
      $page->display();
      
      return $page;
	}
   
}   
