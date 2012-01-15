<?php

require_once(realpath(dirname(__FILE__)).'/../../../init.php');

class MFW_Router_Route_HandlerTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct() {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');
        $this->assertInstanceOf('MFW_Router_Route_Handler', $handler);
    }

    public function testConstructWithTooManySlashes()
    {
        $this->setExpectedException('UnexpectedValueException');
        $handler = new MFW_Router_Route_Handler('thecontroller/the/action');
    }

    public function testConstructWithTooFewSlashes()
    {
        $this->setExpectedException('UnexpectedValueException');
        $handler = new MFW_Router_Route_Handler('thecontrollertheaction');
    }

    public function testConstructWithSpace()
    {
        $this->setExpectedException('UnexpectedValueException');
        $handler = new MFW_Router_Route_Handler('thecontroller theaction');
    }

    public function testGetController()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');
        $this->assertEquals($handler->getController(), 'thecontroller');
    }

    public function testGetAction()
    {
        $handler = new MFW_Router_Route_Handler('thecontroller/theaction');
        $this->assertEquals($handler->getAction(), 'theaction');
    }
}