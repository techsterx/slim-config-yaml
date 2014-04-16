<?php

namespace techsterx\SlimConfig;

use Symfony\Component\Yaml\Yaml as sYaml;

class Yaml extends Singleton
{
	protected static $instance = null;

	protected static $files = array();

	private $values = array();

	public function __construct()
	{
		$files = self::$files;

		foreach ($files as $file) {
			$yml = sYaml::parse($file);

			$this->values[] = &$yml;
		}
	}

	public static function addFile($file)
	{
		if (self::$instance !== null) {
			throw new \Exception('You need to set the path before calling ' . __CLASS__ . '::getInstance() method.');
		} elseif (!file_exists($file) || !is_file($file)) {
			throw new \Exception('The configuration file does not exist.');
		} else {
			self::$files[] = $file;
		}
	}

	public static function setDirectory($directory)
	{
		if (self::$instance !== null) {
			throw new \Exception('You need to set the path before calling ' . __CLASS__ . '::getInstance() method.');
		} elseif (!file_exists($directory) || !is_dir($directory)) {
			throw new \Exception('The configuration directory does not exist.');
		} else {
			self::$files = array();

			if (substr($directory, -1) != DIRECTORY_SEPARATOR) {
				$directory .= DIRECTORY_SEPARATOR;
			}

			foreach (glob($directory . '*.yml') as $file) {
				self::addFile($file);
			}
		}
	}

	public function __set($key, $value)
	{
		$this->values[$key] = $value;
	}

	public function __get($key)
	{
		if (!array_key_exists($key, $this->values)) {
			throw new \Exception('Invalid configuration key - ($key)');
		}

		return $this->values[$key];
	}
}
