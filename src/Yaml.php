<?php

namespace BurningDiode\Slim\Config;

use Slim\Slim;
use Symfony\Component\Yaml\Yaml as YamlParser;

class Yaml
{
	/**
	 * The singleton object
	 *
	 * @var Yaml
	 */
	protected static $instance = null;

	protected static $slim = null;

	protected static $parameters = array();

	/**
	 * addFile - Parse .yml file and add it to slim
	 *
	 * @param string $file
	 * @param bool $reset (optional)
	 *
	 * @return void
	 */
	public function addFile($file, $reset = true)
	{
		if (!file_exists($file) || !is_file($file)) {
			throw new \Exception('The configuration file does not exist.');
		} else {
			if ($reset === true) {
				self::$parameters = array();
			}

			$content = YamlParser::parse(file_get_contents($file));

			if ($content !== null) {
				$content = self::parseImports($content, $file);

				$content = self::parseParameters($content);

				self::addConfig($content);
			}
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

	protected function addConfig($content)
	{
		if (self::$slim === null) {
			self::$slim = Slim::getInstance();
		}

		foreach ($content as $key => $value) {
			self::$slim->config($key, $value);
		}
	}

	protected function parseImports($content, $file)
	{
		if (isset($content['imports'])) {
			$chdir = dirname($file);

			foreach ($content['imports'] as $import) {
				self::addFile($chdir . DIRECTORY_SEPARATOR . $import['resource'], false);
			}

			unset($content['imports']);
		}

		return $content;
	}

	protected function parseParameters($content)
	{
		if (isset($content['parameters'])) {
			self::$parameters = array_merge(self::$parameters, $content['parameters']);

			unset($content['parameters']);
		}

		return $content;
	}

	private function __construct(){}
	private function __clone(){} 
	private function __wakeup(){}
}
