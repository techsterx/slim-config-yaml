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

	/**
	 * addFile - Parse .yml file and add it to slim
	 *
	 * @param string $file
	 * @param bool $reset (optional)
	 *
	 * @return void
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

			//self::$parameters->resolve();
		}
	}

	/**
	 * addDirectory - Parse .yml files in a given directory
	 *
	 * @param string $directory
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
		if (self::$slim === null) {
			self::$slim = Slim::getInstance();
		}

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
			self::$parameters[$resource]->add($content['parameters']);
			self::$parameters[$resource]->resolve();

			unset($content['parameters']);
		}

		return $content;
	}

	private function __construct() { } 
	private function __clone(){} 
	private function __wakeup(){}
}
