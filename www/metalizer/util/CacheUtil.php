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
 * Handle the cache. Can be access by the cache() function. See cache.php for configuration values.
 * In development mode, the cache does nothing.
 *
 * Value names should be like 'foo.bar.some_stuff.my_value'.
 *
 * @author David Reignier
 */
class CacheUtil extends Util {

   /**
    * The global cache of metalizer.
    * @var ICache
    */
   private $cache;
   
   public function __construct() {
      $this->cache = new SimpleCache('global');
   }
   
   /**
    * @return ICache
    *    The global cache of metalizer 
    */  
   public function cache() {
      return $this->cache;
   }
   
   public function finalize() {
      $this->cache->finalize();
   }
}

/**
 * @see CacheUtil#cache
 * @return ICache 
 */
function cache() {
   return util('Cache')->cache();
}
