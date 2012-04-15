<?php
class ConfigurationUtil extends Util {
	
	private $configuration;
	
	public function __construct() {
		// Load the configuration.
		
		$config = array();
		
		// Metalizer default configuration
		foreach (glob(PATH_METALIZER_CONFIGURATION . '*.php') as $file) {
			require $file; 
		}
		
		// Application configuration
		foreach (glob(PATH_APPLICATION_CONFIGURATION . '*.php') as $file) {
			require $file; 
		}
		
		$this->configuration = $config;
	}
	
	public function get($key) {
		if (isset($this->configuration[$key])) {
			return $this->configuration[$key];
		}
		
		throw new ConfigurationKeyNotFoundException("$key can't be found");
	}
	
	
}

function config($key) {
	return util('Configuration')->get($key);
}