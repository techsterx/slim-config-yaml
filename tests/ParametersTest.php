<?php

namespace BurningDiode\Slim\Config;

class ParametersTest extends \PHPUnit_Framework_TestCase
{
    public function testGlobalParameters()
    {
        $app = new \Slim\Slim();

        $data = ['Item 1', 'Item 2', 'Item 3'];

        Yaml::_()->addParameters(['item1' => 'Item 1'])
            ->addParameters(['item2' => 'Item 2'])
            ->addFile(dirname(__FILE__).'/fixtures/global.yml');

        $this->assertEquals($data, $app->config('items'));
    }
}
