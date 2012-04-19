<?php
class Home extends Page {

	public function index() {
		echo "<h1>It works !</h1>";
		$samples = model('Sample')->find('id=1');
		print_r($samples);
	}
}
