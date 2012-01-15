<?php

require_once(realpath(dirname(__FILE__)).'/../../init.php');

class MFW_Mail_AddressTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $mailaddress = new MFW_Mail_Address('info@example.com');

        $this->assertInstanceOf('MFW_Mail_Address', $mailaddress);
    }

    public function testConstructWithName()
    {
        $mailaddress = new MFW_Mail_Address('info@example.com', 'Info Address');

        $this->assertInstanceOf('MFW_Mail_Address', $mailaddress);
    }

    public function testConstructWithInvalidAddress()
    {
        $this->setExpectedException('UnexpectedValueException');

        $mailaddress = new MFW_Mail_Address('in@fo@example.com');
    }

    public function testConstructWithInvalidAddressAndName()
    {
        $this->setExpectedException('UnexpectedValueException');

        $mailaddress = new MFW_Mail_Address('in@fo@example.com', 'Info Address');
    }

    public function testGetAddress()
    {
        $mailaddress = new MFW_Mail_Address('info@example.com', 'Info Address');

        $this->assertEquals('info@example.com', $mailaddress->getAddress());
    }

    public function testGetNameNull()
    {
        $mailaddress = new MFW_Mail_Address('info@example.com');

        $this->assertEquals(null, $mailaddress->getName());
    }

    public function testGetName()
    {
        $mailaddress = new MFW_Mail_Address('info@example.com', 'Info Address');

        $this->assertEquals('Info Address', $mailaddress->getName());
    }

    public function testGetRfcString()
    {
        $mailaddress = new MFW_Mail_Address('info@example.com');

        $this->assertEquals('info@example.com', $mailaddress->getRfcString());
    }

    public function testGetRfcStringWithName()
    {
        $mailaddress = new MFW_Mail_Address('info@example.com', 'Info Address');

        $this->assertEquals('Info Address <info@example.com>', $mailaddress->getRfcString());
    }

    public function testIsValidAddress()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress('info@example.com'));
    }

    public function testIsValidAddress2()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress('Abc\@def@example.com'));
    }

    public function testIsValidAddress3()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress('customer/department=shipping@example.com'));
    }

    public function testIsValidAddress4()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress('"Abc\@def"@example.com'));
    }

    public function testIsValidAddress5()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress('!def!xyz%abc@example.com'));
    }

    public function testIsValidAddress6()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("dclo@us.ibm.com"));
    }

    public function testIsValidAddress7()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("abc\\@def@example.com"));
    }

    public function testIsValidAddress8()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("abc\\\\@example.com"));
    }

    public function testIsValidAddress9()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("Fred\\ Bloggs@example.com"));
    }

    public function testIsValidAddress10()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("Joe.\\\\Blow@example.com"));
    }

    public function testIsValidAddress11()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("\"Abc@def\"@example.com"));
    }

    public function testIsValidAddress12()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("\"Fred Bloggs\"@example.com"));
    }

    public function testIsValidAddress13()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("customer/department=shipping@example.com"));
    }

    public function testIsValidAddress14()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("\$A12345@example.com"));
    }

    public function testIsValidAddress15()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("!def!xyz%abc@example.com"));
    }

    public function testIsValidAddress16()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("_somename@example.com"));
    }

    public function testIsValidAddress17()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("user+mailbox@example.com"));
    }

    public function testIsValidAddress18()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("peter.piper@example.com"));
    }

    public function testIsValidAddress19()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("Doug\\ \\\"Ace\\\"\\ Lovell@example.com"));
    }

    public function testIsValidAddress20()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("\"Doug \\\"Ace\\\" L.\"@example.com"));
    }

    public function testIsValidAddress21()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("abc@def@example.com"));
    }

    public function testIsValidAddress22()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("abc\\\\@def@example.com"));
    }

    public function testIsValidAddress23()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("abc\\@example.com"));
    }

    public function testIsValidAddress24()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("@example.com"));
    }

    public function testIsValidAddress25()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("doug@"));
    }

    public function testIsValidAddress26()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("\"qu@example.com"));
    }

    public function testIsValidAddress27()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("ote\"@example.com"));
    }

    public function testIsValidAddress28()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress(".dot@example.com"));
    }

    public function testIsValidAddress29()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("dot.@example.com"));
    }

    public function testIsValidAddress30()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("two..dot@example.com"));
    }

    public function testIsValidAddress31()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("\"Doug \"Ace\" L.\"@example.com"));
    }

    public function testIsValidAddress32()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("Doug\\ \\\"Ace\\\"\\ L\\.@example.com"));
    }

    public function testIsValidAddress33()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("hello world@example.com"));
    }

    public function testIsValidAddress34()
    {
        $this->assertEquals(false, MFW_Mail_Address::isValidAddress("gatsby@f.sc.ot.t.f.i.tzg.era.l.d."));
    }

    public function testIsValidAddress35()
    {
        $this->assertEquals(true, MFW_Mail_Address::isValidAddress("sally.phillips+anything@gmail.com"));
    }
}