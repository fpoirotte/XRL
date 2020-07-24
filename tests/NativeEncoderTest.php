<?php
/*
 * This file is part of XRL, a simple XML-RPC Library for PHP.
 *
 * Copyright (c) 2012, XRL Team. All rights reserved.
 * XRL is licensed under the 3-clause BSD License.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fpoirotte\XRL\tests;

class SerialClass1 implements \Serializable
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function serialize()
    {
        return serialize($this->data);
    }

    public function unserialize($serialized)
    {
    }
}

class SerialClass2
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __sleep()
    {
        return array('data');
    }
}

class NonSerialClass
{
}

class NativeEncoder extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->encoder = $this->getMockBuilder('\\fpoirotte\\XRL\\EncoderInterface')->getMock();
    }

    public function nativeTypes()
    {
        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->writeElement('foo', 'bar');

        $dom = new \DOMDocument();
        $dom->loadXML('<foo>bar</foo>');

        return array(
            array(null, '\\fpoirotte\\XRL\\Types\\Nil'),
            array(true, '\\fpoirotte\\XRL\\Types\\Boolean'),
            array(false, '\\fpoirotte\\XRL\\Types\\Boolean'),
            array(42, '\\fpoirotte\\XRL\\Types\\I4'),
            array(0x8FFFFFFFF, '\\fpoirotte\\XRL\\Types\\I8'),
            array(3.14, '\\fpoirotte\\XRL\\Types\\Double'),
            array('test', '\\fpoirotte\\XRL\\Types\\StringType'),
            array("\xE8\xE9\xE0", '\\fpoirotte\\XRL\\Types\\Base64'),
            array(array(), '\\fpoirotte\\XRL\\Types\\ArrayType'),
            array(array(1, 2, 3), '\\fpoirotte\\XRL\\Types\\ArrayType'),
            array(
                array(23 => 42, 'foo' => 'bar'),
                '\\fpoirotte\\XRL\\Types\\Struct'
            ),
            array(\gmp_init('42'), '\\fpoirotte\\XRL\\Types\\I4'),
            array(\gmp_init('0x8FFFFFFFF'), '\\fpoirotte\\XRL\\Types\\I8'),
            array(
                gmp_init('0x8FFFFFFFFFFFFFFFF'),
                '\\fpoirotte\\XRL\\Types\\BigInteger'
            ),
            array(
                new \fpoirotte\XRL\Types\Nil(null),
                '\\fpoirotte\\XRL\\Types\\Nil'
            ),
            array(new \DateTime(), '\\fpoirotte\\XRL\\Types\\DateTimeIso8601'),
            array($dom, '\\fpoirotte\\XRL\\Types\\Dom'),
            array($xml, '\\fpoirotte\\XRL\\Types\\Dom'),
            array(
                simplexml_load_string('<foo>bar</foo>'),
                '\\fpoirotte\\XRL\\Types\\Dom'
            ),
            array(new SerialClass1('test'), '\\fpoirotte\\XRL\\Types\\StringType'),
            array(new SerialClass1("\xE8\xE9\xE0"), '\\fpoirotte\\XRL\\Types\\Base64'),
            array(new SerialClass2('test'), '\\fpoirotte\\XRL\\Types\\StringType'),
            array(new SerialClass2("\xE8\xE9\xE0"), '\\fpoirotte\\XRL\\Types\\Base64'),
        );
    }

    /**
     * @covers          \fpoirotte\XRL\NativeEncoder::__construct
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeError
     */
    public function testEncodeError()
    {
        $encoder    = new \fpoirotte\XRL\NativeEncoder($this->encoder);
        $exc        = new \Exception();

        $this->encoder
            ->expects($this->once())
            ->method('encodeError')
            ->with($this->equalTo($exc))
            ->will($this->returnValue($exc));

        $this->assertSame($exc, $encoder->encodeError($exc));
    }

    /**
     * @dataProvider    nativeTypes
     * @covers          \fpoirotte\XRL\NativeEncoder::__construct
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeResponse
     */
    public function testEncodeResponse($native, $expectedType)
    {
        $this->encoder
            ->expects($this->once())
            ->method('encodeResponse')
            ->will($this->returnArgument(0));

        $encoder    = new \fpoirotte\XRL\NativeEncoder($this->encoder);
        $this->assertInstanceOf($expectedType, $encoder->encodeResponse($native));
    }

    /**
     * @covers          \fpoirotte\XRL\NativeEncoder::__construct
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeResponse
     */
    public function testEncodeResponse2()
    {
        $this->encoder
            ->expects($this->once())
            ->method('encodeResponse')
            ->will($this->returnArgument(0));

        $encoder    = new \fpoirotte\XRL\NativeEncoder($this->encoder);
        $response   = $encoder->encodeResponse(new \Exception('foo', 42));

        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Struct', $response);
        $this->assertArrayHasKey('faultString', $response);
        $this->assertSame('Exception: foo', $response['faultString']->get());
        $this->assertArrayHasKey('faultCode', $response);
        $this->assertSame(42, $response['faultCode']->get());
    }

    /**
     * @covers                      \fpoirotte\XRL\NativeEncoder::convert
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Unconvertible type
     */
    public function testEncodeResponse3()
    {
        $encoder    = new \fpoirotte\XRL\NativeEncoder($this->encoder);
        $response   = $encoder->encodeResponse(new NonSerialClass());
    }
}
