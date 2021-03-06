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
 * Metalizer finalization file.
 */

logDebug('Metalizer finalization');
 
// *** Application finalize ***
$finalFile = PATH_APPLICATION . 'finalize.php';
if (file_exists($finalFile)) {
   logDebug('Include application finalization');
   require_once $finalFile;
}

// *** Finalize model factories ***
logDebug('Finalize model factories');
manager('ModelFactory')->finalize();

// *** Finalize utils ***

logDebug('Finalize store util');
store()->finalize();

logDebug('Finalize redbean');
redbean()->finalize();