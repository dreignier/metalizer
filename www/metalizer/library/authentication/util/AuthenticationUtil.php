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

define('AUTHENTICATION_SESSION_NAME', "authentication.user");
 
/**
 * Provide easy access to authentication functions. AuthenticationUtil use BaseUser to represent users.
 * If you want to extend the BaseUser behavior, just use a subclass.
 * @author David Reignier
 *
 */
class AuthenticationUtil extends Util {
   
   /**
    * The current user
    * @var BaseUser
    */
   private $currentUser = null;

   /**
    * We must clean the current user.
    */
   public function onSleep() {
      $this->currentUser = null;
   }

   /**
    * Do an authentication challenge. 
    * @param $login string
    *    A login or an email.
    * @param $password string
    *    A password
    * @return mixed
    *    if the login/password couple is correct, the corresponding BaseUser is returned. Otherwise, null is returned.
    */
   public function challenge($login, $password) {
      $user = model('BaseUser')->findOneBy('login', $login);
      if (!$user) {
         $user = model('BaseUser')->findOneBy('email', $login);
      }
      
      if (!$user) {
         return null;
      }
      
      return $user->challengePassword($password) ? $user : null;
   }
   
   /**
    * Login a user.
    * @param $user BaseUser
    *    A base user.
    */
   public function login($user) {
      $this->currentUser = $user;
      session()->set(AUTHENTICATION_SESSION_NAME, $user->getLogin());
   }
   
   /**
    * Logout the current user. Do nothing if there's no current user.
    */
   public function logout() {
      session()->clean(AUTHENTICATION_SESSION_NAME);
      $this->currentuser = null;
   }
   
   /**
    * Get the current user.
    * @return BaseUser
    *    The current BaseUser if the current user is authenticated. null otherwise.
    */
   public function getCurrentUser() {
      if ($this->currentUser) {
         return $this->currentUser;
      }   
      
      $login = session()->get(AUTHENTICATION_SESSION_NAME);
      
      if (!$login) {
         return null;
      }
      
      $user = model('BaseUser')->findOneBy('login', $login);
      
      if (!$user) {
         return null;
      }
      
      $this->currentUser = $user;
      return $user;
   }
   
   /**
    * Check if the current user is authenticated.
    * @throws UnauthorizedException
    *    If the user is not authenticated
    */
   public function mustBeAuthenticated() {
      if (!$this->getCurrentuser()) {
         throw new UnauthorizedException();
      }
   }

}

/**
 * @see AuthenticationUtil#getCurrentUser
 * @return BaseUser
 */
function getCurrentUser() {
   return util('Authentication')->getCurrentUser();
}

/**
 * @see AuthenticationUtil#mustBeAuthenticated
 */
function mustBeAuthenticated() {
   util('Authentication')->mustBeAuthenticated();
}
