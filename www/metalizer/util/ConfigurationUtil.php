<?php
/**
 * The ConfigurationUtil provide a easy way to read configurations values. It reads all files in configurations folders (metalizer and application).
 * @author David Reignier
 *
 */
class ConfigurationUtil extends Util {
	
	/**
	 * All configuration values
	 * @var array[mixed]
	 */
	private $configuration;
	
	/**
	 * Construct a new ConfigurationUtil. All configuration files are loaded.
	 * @return ConfigurationUtil
	 */
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
	
	/**
	 * Get a configuration value.
	 * @param $key string
	 * 	The name of the value
	 * @return mixed 
	 * 	The value
	 * @throws ConfigurationKeyNotFoundException
	 * 	If the key can't be found in the configuration values.
	 */
	public function get($key) {
		if (isset($this->configuration[$key])) {
			return $this->configuration[$key];
		}
		
		throw new ConfigurationKeyNotFoundException("$key can't be found");
	}
	
	
}

/**
 * @see ConfigurationUtil#get
 */
function config($key) {
	return util('Configuration')->get($key);
}