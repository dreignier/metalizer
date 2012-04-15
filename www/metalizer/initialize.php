<?php
/**
 * Metalizer initialization file.
 */
// *** Classloader initialization ***

require_once PATH_METALIZER_CORE . 'ClassLoader.php';
classLoader()->initialize();

// *** Non-object functions ***
// We need to require some files for non object functions

require_once PATH_METALIZER_MANAGER . 'UtilManager.php';

// *** ManagerManager initialization ***
ManagerManager::initialize();

// *** UtilManager initialization ***
// Create the UtilManager if it doesn't exist
manager('Util');