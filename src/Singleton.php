<?php

namespace techsterx\SlimConfig;

abstract class Singleton
{
	protected function __constrct()
	{
	}

	public static function getInstance()
	{
		static $instance = null;

		if ($instance === null) {
			$instance = new static();
		}

		return $instance;
	}

	private function __close()
	{
	}

	private function __wakeup()
	{
	}
}
