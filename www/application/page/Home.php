<?php
class Home extends Page {

	public function index() {
		echo "<h1>It works !</h1>";

		database()->query('CREATE TABLE test (test VARCHAR(20))');
	}
}