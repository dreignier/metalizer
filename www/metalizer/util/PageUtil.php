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
 * Page utility functions
 * @author David Reignier
 *
 */
class PageUtil extends Util {

   /**
    * Check if a combinaison of a page, a method and arguments is valid.
    * @param $page string
    *    A Page subclass name
    * @param $method string
    *    A method name.
    * @param $args array
    *    Arguments for the method.
    * @return boolean
    *    Always return true.
    * @throws InternalErrorException
    *    If the given combinaison is not valid, an except is thrown.
    */
   public function check($page, $method, $args) {
      if (!$page || !$method) {
         throw new InternalErrorException();
      }

      if (!class_exists($page) || !is_subclass_of($page, 'Page')) {
         throw new InternalErrorException("$page is not a valid Page subclass");
      }

      // Check if all is ok
      $reflectionClass = new ReflectionClass($page);

      if (!$reflectionClass->hasMethod($method)) {
         throw new InternalErrorException("There's no method '$method' in the page '$page'");
      }

      $reflectionMethod = $reflectionClass->getMethod($method);

      if (!$reflectionMethod->isPublic() || $reflectionMethod->isStatic() || $reflectionMethod->isAbstract()) {
         throw new InternalErrorException("The method '$method' in the $page class is not valid");
      }

      if ($reflectionMethod->getNumberOfRequiredParameters() > sizeof($args)) {
         throw new InternalErrorException("Method '$method' found in the $page class but require " . $reflectionMethod->getNumberOfRequiredParameters() . " parameters (" . sizeof($args) . " given)");
      }

      return true;
   }

   /**
    * Run a method page with arguments.
    * @param $page string
    *    A Page subclass name
    * @param $method string
    *    A method name.
    * @param $_ array
    *    The arguments for the method. It can be an array or a list of arguments.
    * @return Page
    *    The generated page.
    * @throws InternalErrorException
    *    If the given combinaison is not valid, an except is thrown.
    */
   public function run($page, $method, $_) {
      $args = array();
      if (is_array($_)) {
         $args = $_;
      } else if ($_) {
         $args = array_slice(func_get_args(), 2);
      }

      $this->check($page, $method, $args);
      return $this->runWithoutCheck($page, $method, $args);
   }

   /**
    * Same as PageUtil#run but  PageUtil#check is not called.
    * @param $page string
    *    A Page subclass name
    * @param $method string
    *    A method name.
    * @param $args array
    *    Arguments for the method.
    */
   public function runWithoutCheck($page, $method, $args) {
      $page = new $page();
      call_user_func_array(array($page, $method), $args);
      $page->display();

      return $page;
   }

}

/**
 * @return PageUtil
 */
function page() {
   return util('Page');
}
