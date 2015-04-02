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
	 * Parse .yml file and add it to slim
	 *
	 * @param string $file
	 * @param string $resource (optional)
	 *
	 * @return self
	 */
	public function addFile($file, $resource = null)
	{
		if (!file_exists($file) || !is_file($file)) {
			throw new \Exception('The configuration file ' . $file . ' does not exist.');
		} else {
			if ($resource === null) {
				$resource = $file;
			}

			if (!array_key_exists($resource, self::$parameters)) {
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
	 * Parse .yml files in a given directory
	 *
	 * @param string $directory
	 *
	 * @return self
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
	 * Adds global parameters for use by all resources
	 *
	 * @param array $parameters
	 *
	 * @return self
	 */
	public function addParameters(array $parameters)
	{
		self::$global_parameters = array_merge(self::$global_parameters, $parameters);

		return self::getInstance();
	}

	/**
	 * Create a new or return the current instance
	 *
	 * @return self
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Shorthand for getInstance
	 * 
	 * @return self
	 */
	public static function _()
	{
		return self::getInstance();
	}

	/**
	 * Resolves parameters and adds to Slim's config singleton
	 *
	 * @param array $content
	 * @param string $resource
	 *
	 * @return void
	 */
	protected function addConfig(array $content, $resource)
	{
		foreach ($content as $key => $value) {
			$value = self::$parameters[$resource]->resolveValue($value);

			self::$slim->config(array($key => $value), true);
		}
	}

	/**
	 * Parses the imports section of a resource and includes them
	 *
	 * @param array $content
	 * @param string $resource
	 *
	 * @return array
	 */
	protected function parseImports(array $content, $resource)
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
		$parameters = self::$global_parameters;

		if (isset($content['parameters'])) {
			$parameters = array_merge($content['parameters'], $parameters);

			unset($content['parameters']);
		}

		self::$parameters[$resource]->add($parameters);
		self::$parameters[$resource]->resolve();

		return $content;
	}

	private function __construct() {
		self::$slim = Slim::getInstance();
	} 

	private function __clone(){} 
	private function __wakeup(){}
}
