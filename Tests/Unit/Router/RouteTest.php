<?php

require_once(realpath(dirname(__FILE__)).'/../../init.php');

class MFW_Router_RouteTest extends PHPUnit_Framework_TestCase
{
    public function testContruct()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/', $handler);
        $this->assertInstanceOf('MFW_Router_Route', $route);
    }

    public function testGetName()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/', $handler);
        $this->assertEquals('index', $route->getName());
    }

    public function testGetUriIndex()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/', $handler);
        $this->assertEquals('/', $route->getUri());
    }

    public function testGetUriPath()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path', $handler);
        $this->assertEquals('/some/path', $route->getUri());
    }

    public function testGetHandler()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path', $handler);
        $this->assertInstanceOf('MFW_Router_Route_Handler', $route->getHandler());
    }

    public function testGetDefaultsEmpty()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path', $handler);
        $this->assertEquals(0, count($route->getDefaults()));
    }

    public function testGetDefaultsWithItems()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path', $handler, array('param1'=>'something', 'param2'=>false));
        $this->assertEquals(2, count($route->getDefaults()));
    }

    public function testGetRequirementsEmpty()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path', $handler);
        $this->assertEquals(0, count($route->getRequirements()));
    }

    public function testGetRequirementsWithItems()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path', $handler, array(), array('req1'=>'\s+', 'req2'=>'\d+'));
        $this->assertEquals(2, count($route->getRequirements()));
    }

    public function testGetParsedUriStatic()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path', $handler, array('param1'=>'something'), array('req1'=>'\s+', 'req2'=>'\d+'));
        $this->assertEquals('/some/path', $route->getParsedUri(array()));
    }

    public function testGetParsedUriOptionalVariable()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1', $handler, array('param1'=>false, 'param2'=>'else'));
        $this->assertEquals('/some/path', $route->getParsedUri(array()));
    }

    public function testGetParsedUriOptionalVariableDefault()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1', $handler, array('param1'=>'something', 'param2'=>'else'));
        $this->assertEquals('/some/path/something', $route->getParsedUri(array()));
    }

    public function testGetParsedUriOptionalVariableFilled()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1', $handler, array('param1'=>'something', 'param2'=>'else'));
        $this->assertEquals('/some/path/foo', $route->getParsedUri(array('param1'=>'foo')));
    }

    public function testGetParsedUriOptionalVariableMissingDefault()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>false, 'param2'=>'else'));

        $this->setExpectedException('DomainException');
        $route->getParsedUri(array());
    }

    public function testGetParsedUriOptionalVariableMissingFilled()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>false, 'param2'=>'else'));

        $this->setExpectedException('DomainException');
        $route->getParsedUri(array('param2'=>'foo'));
    }

    public function testGetParsedUriRequiredVariableWithMissingOptionalVariable()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>false));

        $this->setExpectedException('DomainException');
        $route->getParsedUri(array('param2'=>'foo'));
    }

    public function testGetParsedUriRequiredVariableMissing()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>'foo'));

        $this->setExpectedException('DomainException');
        $route->getParsedUri(array());
    }

    public function testGetParsedUriRequiredVariableValid()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>'foo'));

        $this->assertEquals('/some/path/foo/bar', $route->getParsedUri(array('param2'=>'bar')));
    }

    public function testGetParsedUriVariableMeetsRequirements()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>'foo'), array('params2'=>'^\d+$'));

        $this->assertEquals('/some/path/foo/12345', $route->getParsedUri(array('param2'=>12345)));
    }

    public function testGetParsedUriVariableDoesNotMeetRequirements()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');

        $route = new MFW_Router_Route('index', '/some/path/:param1/:param2', $handler, array('param1'=>'foo'), array('param2'=>'^\d+$'));

        $this->setExpectedException('UnexpectedValueException');
        $route->getParsedUri(array('param2'=>'bar'));
    }
}