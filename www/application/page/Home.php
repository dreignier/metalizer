<?php
class Home extends Page {

	public function index() {
		echo "<h1>It works !</h1>";
	}
	
	public function test($test) {
		echo "<h1>Coucou : $test</h1>";
	}
}
