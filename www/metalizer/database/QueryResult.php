<?php
/**
 * A result of a Database query.
 * @author David Reignier
 *
 */
class QueryResult extends MetalizerObject {

	/**
	 * Create a new query.
	 * @param $query mysqli_stmt 
	 * 	A mysqli query result.
	 * @return QueryResult
	 */
	public function __construct(mysqli_stmt $query) {

	}

	/**
	 * Get the next row, or null if the result is at the end.
	 * @return array[mixed] 
	 * 	An array representation of the next row
	 */
	public function next() {

	}

	/**
	 * Return the results count.
	 * @return int 
	 * 	The results count.
	 */
	public function count() {

	}

	/**
	 *
	 * @return bool 
	 * 	true is the QueryResult if empty (eg. count == 0), false otherwise.
	 */
	public function isEmpty() {

	}
}