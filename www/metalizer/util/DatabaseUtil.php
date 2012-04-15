<?php
class DatabaseUtil extends Util {
	
	public function get($name) {
		return manager('Database')->get($name);
	}
	
}

function database($name = 'metalizer') {
	return Util('Database')->get($name);
}