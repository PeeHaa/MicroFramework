<?php

require_once(realpath(dirname(__FILE__)).'/../../init.php');

class MFW_Router_RewriteTest extends PHPUnit_Framework_TestCase
{
    public function testGetUri($name = null, array $params = array())
    {
        $router = new MFW_Router_Rewrite;
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $router->addRoute(
            new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>'foo'), array('param2'=>'^\d+$'))
        );

        $this->assertEquals('/some/path/foo/12345', $router->getUri('index', array('param2'=>12345)));
    }

    public function testGetUriWithInvalidRoute($name = null, array $params = array())
    {
        $router = new MFW_Router_Rewrite;
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $router->addRoute(
            new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>'foo'), array('param2'=>'^\d+$'))
        );

        $this->setExpectedException('OutOfBoundsException');
        $router->getUri('invalid', array('param2'=>12345));
    }

    public function testSetControllerPath()
    {
        $router = new MFW_Router_Rewrite;

        $this->assertEquals(null, $router->setControllerPath(realpath(dirname(__FILE__))));
    }

    public function testSetControllerPathInvalid()
    {
        $router = new MFW_Router_Rewrite;

        $this->setExpectedException('InvalidArgumentException');
        $router->setControllerPath('/this/path/does/not/exist/jksdchjksdhgcjdsbgh');
    }
}