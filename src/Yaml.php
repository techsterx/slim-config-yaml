<?php

namespace BurningDiode\Slim\Config;

use Slim\Slim;
use Symfony\Component\Yaml\Yaml as YamlParser;
use BurningDiode\Slim\Config\ParameterBag;

class Yaml
{
	protected static $instance;
	protected static $slim;
	protected static $parameters = array();
	protected static $global_parameters = array();

	/**
	 * addFile - Parse .yml file and add it to slim
	 *
	 * @param string $file
	 * @param bool $reset (optional)
	 *
	 * @return Yaml
	 */
	public function addFile($file, $resource = null)
	{
		if (!file_exists($file) || !is_file($file)) {
			throw new \Exception('The configuration file ' . $file . ' does not exist.');
		} else {
			if ($resource === null) {
				$resource = $file;
			}

			if (!isset($resource, self::$parameters)) {
				self::$parameters[$resource] = new ParameterBag();
			}

			$content = YamlParser::parse(file_get_contents($file));

			if ($content !== null) {
				$content = self::parseImports($content, $resource);

				$content = self::parseParameters($content, $resource);

				self::addConfig($content, $resource);
			}
		}

		return self::getInstance();
	}

	/**
	 * addDirectory - Parse .yml files in a given directory
	 *
	 * @param string $directory
	 * @param array $parameters (optional)
	 *
	 * @return Yaml
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

		return self::getInstance();
	}

	/**
	 * addParameters - Adds global parameters for use by all resources
	 *
	 * @param array $parameters
	 *
	 * @return Yaml
	 */
	public function addParameters(array $parameters)
	{
		self::$global_parameters = $parameters;

		return self::getInstance();
	}

	/**
	 * getInstance - Get or create the current instance
	 *
	 * @return Yaml
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
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

	protected function addConfig($content, $resource)
	{
		foreach ($content as $key => $value) {
			$value = self::$parameters[$resource]->resolveValue($value);

			self::$slim->config($key, $value);
		}
	}

	protected function parseImports($content, $resource)
	{
		if (isset($content['imports'])) {
			$chdir = dirname($resource);

			foreach ($content['imports'] as $import) {
				self::addFile($chdir . DIRECTORY_SEPARATOR . $import['resource'], $resource);
			}

			unset($content['imports']);
		}

		return $content;
	}

	protected function parseParameters($content, $resource)
	{
		if (isset($content['parameters'])) {
			$parameters = array_merge($content['parameters'], self::$global_parameters);

			self::$parameters[$resource]->add($parameters);
			self::$parameters[$resource]->resolve();

			unset($content['parameters']);
		}

		return $content;
	}

	private function __construct() {
		self::$slim = Slim::getInstance();
	} 

	private function __clone(){} 
	private function __wakeup(){}
}
