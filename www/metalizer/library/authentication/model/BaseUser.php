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
 * Represent a minimal user. It just handle name, email and password.
 * @author David Reignier
 *
 */
class BaseUser extends Model {
   
   /**
    * Set the email.
    * @param $email string
    *    A valid email.
    */
   public function setEmail($email) {
      return $this->set('email', $email);
   }
   
   /**
    * Set the password. The password will be encrypted using the crypt php function. The salt is auto generated.
    * @param $password string
    *    A clear password.
    */
   public function setPassword($password) {
      return $this->set('password', crypt($password));
   }
   
   /**
    * Set the login.
    * @param $login string
    *    A login.
    */
   public function setLogin($login) {
      return $this->set('login', $login);
   }
   
   /**
    * @return string
    *    The email of the user.
    */
   public function getEmail() {
      return $this->get('email');
   }
 
   /**
    * @return login
    *    The login of the user
    */
   public function getLogin() {
      return $this->get('login');
   }
   
   /**
    * @param $password string
    *    A clear password.
    * @return bool
    *    true if the given clear password is the password of this user, false otherwise.
    */
   public function challengePassword($password) {
      $crypted = $this->get('password');
      return crypt($password, $crypted) == $crypted;
   }
} 