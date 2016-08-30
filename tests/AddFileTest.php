<?php

namespace BurningDiode\Slim\Config;

class AddFileTest extends \PHPUnit_Framework_TestCase
{
    public function testAddFile()
    {
        $app = new \Slim\Slim();

        $data = array('item1', 'item2');

        Yaml::_()->addFile(dirname(__FILE__) . '/fixtures/index.yml');

        $this->assertEquals($data, $app->config('items'));
    }
}
