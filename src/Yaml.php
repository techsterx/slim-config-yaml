<?php

namespace BurningDiode\Slim\Config;

use Slim\Slim;
use Symfony\Component\Yaml\Yaml as SymYaml;

class Yaml
{
	/**
	 * The singleton object
	 *
	 * @var Yaml
	 */
	protected static $instance = null;

	/**
	 * addFile - Parse .yml file and add it to slim
	 *
	 * @return void
	 */
	public function addFile($file)
	{
		if (!file_exists($file) || !is_file($file)) {
			throw new \Exception('The configuration file does not exist.');
		} else {
			$slim = Slim::getInstance();
			$yaml = SymYaml::parse(file_get_contents($file));

			foreach ($yaml as $key => $value) {
				$slim->config($key, $value);
			}
		}
	}

	/**
	 * addDirectory - Parse .yml files in a given directory
	 *
	 * @return void
	 */
	public function addDirectory($directory)
	{
		if (!file_exists($directory) || !is_dir($directory)) {
			throw new \Exception('The configuration directory does not exist.');
		} else {
			if (substr($directory, -1) != DIRECTORY_SEPARATOR) {
				$directory .= DIRECTORY_SEPARATOR;
			}

			foreach (glob($directory . '*.yml') as $file) {
				self::addFile($file);
			}
		}
	}

	/**
	 * getInstance - Get or create the current instance
	 *
	 * @return Yaml
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * _ - Shorthand for getInstance
	 * 
	 * @return Yaml
	 */
	public static function _()
	{
		return self::getInstance();
	}

	private function __construct(){}
	private function __clone(){} 
	private function __wakeup(){}
}
