<?php

namespace BurningDiode\Slim\Config;

class ImportsTest extends \PHPUnit_Framework_TestCase
{
	public function testImports()
	{
		$app = \Slim\Slim::getInstance();

		$data = array('Item 1', 'Item 2', 'Item 3');

		Yaml::_()->addFile(dirname(__FILE__) . '/fixtures/imports.yml');

		$this->assertEquals($data, $app->config('items'));
	}
}
