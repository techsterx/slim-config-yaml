<?php

namespace BurningDiode\Slim\Config;

class EscapeTest extends \PHPUnit_Framework_TestCase
{
	public function testEscapedParameters()
	{
		$app = new \Slim\Slim();

		$data = array('%Item 1%', 'Item 2', 'Item 3');

		Yaml::_()->addParameters(array('item2' => 'Item 2'))
			->addFile(dirname(__FILE__) . '/fixtures/escape.yml');

		$this->assertEquals($data, $app->config('items'));
	}
}
