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
			$this->log()->info(getIp() . ":" . getRequestMethod() . ":$pathInfo");
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
					
               // The first matching pattern must be the used one. So don't remove that.
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

		util('Page')->check($this->page, $this->method, $this->params);
	}

	/**
	 * Execute the page of the resolver with the method and parameters.
    * @return Page
    *    The page object.
	 */
	public function run() {
		return util('Page')->run($this->page, $this->method, $this->params);
	}
   
}   
