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

class Encoder extends \PHPUnit\Framework\TestCase
{
    protected $encoder;

    public function setUp(): void
    {
        // Emulate a server located in Ireland that uses
        // indentation and the <string> tag.
        $this->encoder = new \fpoirotte\XRL\Encoder(
            new \DateTimeZone("Europe/Dublin"),
            true,
            true
        );
    }

    public function assertEqualsRequest($path, $data)
    {
        $this->assertXmlStringEqualsXmlFile(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'requests' .
            DIRECTORY_SEPARATOR .
            str_replace('/', DIRECTORY_SEPARATOR, $path . '.xml'),
            $data
        );
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeEmptyParameters()
    {
        $request    = new \fpoirotte\XRL\Request(
            'emptyParams',
            array()
        );
        $this->assertEqualsRequest('empty', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeMultipleParameters()
    {
        $request    = new \fpoirotte\XRL\Request(
            'multiParams',
            array(
                new \fpoirotte\XRL\Types\I4(42),
                new \fpoirotte\XRL\Types\StringType('test'),
            )
        );
        $this->assertEqualsRequest('multi', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncode32BitsSignedInteger()
    {
        $request    = new \fpoirotte\XRL\Request(
            'i4Param',
            array(new \fpoirotte\XRL\Types\I4(2147483647))
        );
        $this->assertEqualsRequest('i4', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncode32BitsSignedInteger2()
    {
        $request    = new \fpoirotte\XRL\Request(
            'intParam',
            array(new \fpoirotte\XRL\Types\IntType(2147483647))
        );
        $this->assertEqualsRequest('int', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncode64BitsSignedInteger()
    {
        $request    = new \fpoirotte\XRL\Request(
            'i8Param',
            array(
                new \fpoirotte\XRL\Types\I8(gmp_init('9223372036854775807'))
            )
        );
        $this->assertEqualsRequest('i82', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeBigInteger()
    {
        $request    = new \fpoirotte\XRL\Request(
            'bigintParam',
            array(
                new \fpoirotte\XRL\Types\BigInteger(gmp_init('9223372036854775808'))
            )
        );
        $this->assertEqualsRequest('bigint', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeBoolean()
    {
        $request    = new \fpoirotte\XRL\Request(
            'boolParam',
            array(new \fpoirotte\XRL\Types\Boolean(true))
        );
        $this->assertEqualsRequest('bool', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeBoolean2()
    {
        $request    = new \fpoirotte\XRL\Request(
            'boolParam',
            array(new \fpoirotte\XRL\Types\Boolean(false))
        );
        $this->assertEqualsRequest('bool2', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeString()
    {
        $request    = new \fpoirotte\XRL\Request(
            'stringParam',
            array(new \fpoirotte\XRL\Types\StringType(''))
        );
        $this->assertEqualsRequest('string', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeString2()
    {
        $request    = new \fpoirotte\XRL\Request(
            'stringParam',
            array(new \fpoirotte\XRL\Types\StringType('test'))
        );
        $this->assertEqualsRequest('string2', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeDouble()
    {
        $request    = new \fpoirotte\XRL\Request(
            'doubleParam',
            array(new \fpoirotte\XRL\Types\Double(3.14))
        );
        $this->assertEqualsRequest('double', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeDatetime()
    {
        // Emulate a client located in Metropolitain France.
        $tz         = new \DateTimeZone('Europe/Paris');
        $date       = new \DateTime('1985-11-28T14:00:00', $tz);
        $request    = new \fpoirotte\XRL\Request(
            'dateTimeParam',
            array(new \fpoirotte\XRL\Types\DateTimeIso8601($date))
        );
        $this->assertEqualsRequest('datetime', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeBinary()
    {
        // An invalid UTF-8 sequence.
        $request    = new \fpoirotte\XRL\Request(
            'binaryParam',
            array(new \fpoirotte\XRL\Types\Base64("\xE8\xE9\xE0"))
        );
        $this->assertEqualsRequest('binary', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeNumericArray()
    {
        $array      = new \fpoirotte\XRL\Types\ArrayType(
            array(
                new \fpoirotte\XRL\Types\StringType('test'),
                new \fpoirotte\XRL\Types\I4(42),
            )
        );
        $request    = new \fpoirotte\XRL\Request('numArray', array($array));
        $this->assertEqualsRequest('num_array', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeEmptyArray()
    {
        $array      = new \fpoirotte\XRL\Types\ArrayType(array());
        $request    = new \fpoirotte\XRL\Request('numArray', array($array));
        $this->assertEqualsRequest('empty_array', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeAssociativeArray()
    {
        $array      = new \fpoirotte\XRL\Types\Struct(
            array(
                'foo' => new \fpoirotte\XRL\Types\StringType('test'),
                'bar' => new \fpoirotte\XRL\Types\I4(42),
            )
        );
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $this->assertEqualsRequest('assoc_array', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeAssociativeArray2()
    {
        $array      = new \fpoirotte\XRL\Types\Struct(
            array(
                new \fpoirotte\XRL\Types\StringType('foo'),
                42 => new \fpoirotte\XRL\Types\StringType('bar'),
            )
        );
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $this->assertEqualsRequest('assoc_array2', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeAssociativeArray3()
    {
        $array      = new \fpoirotte\XRL\Types\Struct(
            array(
                42 => new \fpoirotte\XRL\Types\StringType('foo'),
                new \fpoirotte\XRL\Types\StringType('bar'),
            )
        );
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $this->assertEqualsRequest('assoc_array3', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder
     */
    public function testEncodeSimpleXmlElement()
    {
        $sxml = simplexml_load_string(<<<XML
            <ns:foo xmlns:ns="http://example.com/ns">
                <bar baz="42" xmlns:ns2="http://example.com/ns2" ns2:qux="blah"/>
            </ns:foo>
XML
        );
        $request    = new \fpoirotte\XRL\Request(
            'domParam',
            array(new \fpoirotte\XRL\Types\Dom($sxml))
        );
        $this->assertEqualsRequest('dom', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeError
     */
    public function testEncodeFailureResponse()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'responses' .
            DIRECTORY_SEPARATOR . 'failure.xml'
        );

        $failure    = new \Exception('Test_failure', 42);
        $received   = $this->encoder->encodeError($failure);
        $this->assertSame($content, $received);
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeResponse
     */
    public function testEncodeSuccessfulResponse()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'responses' .
            DIRECTORY_SEPARATOR . 'success.xml'
        );

        $response   = new \fpoirotte\XRL\Types\ArrayType(
            array(
                new \fpoirotte\XRL\Types\I4(42),
                new \fpoirotte\XRL\Types\StringType('test'),
            )
        );
        $received   = $this->encoder->encodeResponse($response);
        $this->assertSame($content, $received);
    }

    /**
     * @covers                      \fpoirotte\XRL\Encoder::encodeResponse
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Invalid response
     */
    public function testEncodeGarbage()
    {
        $dummy = $this->encoder->encodeResponse(null);
    }

    /**
     * @covers                      \fpoirotte\XRL\Encoder::__construct
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    $indent must be a boolean
     */
    public function testConstructor()
    {
        $dummy = new \fpoirotte\XRL\Encoder(null, 42);
    }

    /**
     * @covers                      \fpoirotte\XRL\Encoder::__construct
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    $stringTag must be a boolean
     */
    public function testConstructor2()
    {
        $dummy = new \fpoirotte\XRL\Encoder(null, false, 42);
    }

    /**
     * @covers                      \fpoirotte\XRL\Encoder
    */
    public function testIndentation()
    {
        $response = $this->getMockBuilder('\\fpoirotte\\XRL\\Types\\AbstractType')
                         ->disableOriginalConstructor()
                         ->getMock();
        $response
            ->expects($this->once())
            ->method('write');

        $encoder    = new \fpoirotte\XRL\Encoder();
        $res        = $encoder->encodeResponse($response);
        $expected   = '<methodResponse><params><param><value/></param></params></methodResponse>';
        $this->assertSame($expected, $res);
    }
}
