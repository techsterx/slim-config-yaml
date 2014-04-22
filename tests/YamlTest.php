<?php

namespace BurningDiode\Slim\Config;

use Slim\Slim;
use Symfony\Component\Yaml\Yaml;

class YamlTest extends \PHPUnit_Framework_TestCase
{
	public function testParse()
	{
		$app = Slim::getInstance();

		$data = array('lorem' => 'ipsum', 'dolor' => 'sit');
		$yml = Yaml::dump($data);
		$parsed = Yaml::parse($yml);
		$this->assertEquals($data, $parsed);
	}
}
