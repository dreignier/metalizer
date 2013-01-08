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
 * A SimpleCache is just a HotCache with a ColdCache as subcache. 
 * @author David Reignier
 */
class SimpleCache extends HotCache {
      
   public function __construct($name) {
      if ($this->log()->isInfoEnabled()) {
         $this->log()->info("Initializing ...");
      }
      
      parent::__construct($name . '_hot');
      $this->subcache = new ColdCache($name . '_cold');
   }
      
}