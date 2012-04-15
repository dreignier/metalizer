<?php
class ServerUtil extends Util {
	
	public function get($key) {
		return $_SERVER[$key];
	}
	
}