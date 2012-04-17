<?php
/**
 * Used by Database.
 * @author David Reignier
 *
 */
class MysqlException extends MetalizerException {

	/**
	 * Constrcut a new MysqlException.
	 * @param $errno int
	 * 	The error number.
	 * @param $error string
	 * 	The error message.
	 * @return MysqlException
	 */
	public function __construct($errno, $error) {
		parent::__construct("Query error ($errno) : $error");
	}
	
}