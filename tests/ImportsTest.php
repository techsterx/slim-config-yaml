<?php

namespace BurningDiode\Slim\Config;

class ImportsTest extends \PHPUnit_Framework_TestCase
{
    public function testImports()
    {
        $app = new \Slim\Slim();

        $data = array('Item 1', 'Item 2', 'Item 3');

        Yaml::_()->addFile(dirname(__FILE__).'/fixtures/imports.yml');

        $this->assertEquals($data, $app->config('items'));
    }

    public function testMergeImports()
    {
        $app = new \Slim\Slim();

        $data = array('item-a' => 'Item A', 'item-1' => 'Item 1', 'item-2' => 'Item 2', 'item-3' => 'Item 3');

        Yaml::_()->addFile(dirname(__FILE__).'/fixtures/merge1.yml');

        $this->assertEquals($data, $app->config('items'));
    }
}
