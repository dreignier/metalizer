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
 * Metalizer initialization file.
 */
 
// *** Classloader initialization ***

require_once PATH_METALIZER_CORE . 'ClassLoader.php';
classLoader()->initialize();

// *** Start session ***
session_start();

// *** Non-object functions ***
// We need to require some files for non object functions

require_once PATH_METALIZER_MANAGER . 'UtilManager.php';

// *** ManagerManager initialization ***
ManagerManager::initialize();

// *** Util initialization ***
// Create the UtilManager if it doesn't exist
manager('Util');

// Initialize the generated resources util if it doesn't exist.
util('GeneratedResource');

// *** Application initialization ***
$initFile = PATH_APPLICATION . 'initialize.php';
if (file_exists($initFile)) {
	require_once $initFile;
}