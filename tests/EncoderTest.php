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

class Encoder extends \PHPUnit_Framework_TestCase
{
    const TEST_DOM = <<<XML
            <ns:foo xmlns:ns="http://example.com/ns">
                <bar baz="42" xmlns:ns2="http://example.com/ns2" ns2:qux="blah"/>
            </ns:foo>
XML;

    public function setUp()
    {
        // Emulate a server located in Ireland that uses
        // indentation and the <string> tag.
        $this->encoder = new \fpoirotte\XRL\NativeEncoder(
            new \fpoirotte\XRL\Encoder(
                new \DateTimeZone("Europe/Dublin"),
                true,
                true
            )
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
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeEmptyParameters()
    {
        $request    = new \fpoirotte\XRL\Request('emptyParams', array());
        $this->assertEqualsRequest('empty', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeMultipleParameters()
    {
        $request    = new \fpoirotte\XRL\Request('multiParams', array(42, 'test'));
        $this->assertEqualsRequest('multi', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncode32BitsSignedInteger()
    {
        $request    = new \fpoirotte\XRL\Request('i4Param', array(2147483647));
        $this->assertEqualsRequest('i4', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncode64BitsSignedInteger()
    {
        $request    = new \fpoirotte\XRL\Request('i8Param', array(gmp_init('9223372036854775807')));
        $this->assertEqualsRequest('i82', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeBigInteger()
    {
        $request    = new \fpoirotte\XRL\Request('bigintParam', array(gmp_init('9223372036854775808')));
        $this->assertEqualsRequest('bigint', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeBoolean()
    {
        $request    = new \fpoirotte\XRL\Request('boolParam', array(true));
        $this->assertEqualsRequest('bool', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeBoolean2()
    {
        $request    = new \fpoirotte\XRL\Request('boolParam', array(false));
        $this->assertEqualsRequest('bool2', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeString()
    {
        $request    = new \fpoirotte\XRL\Request('stringParam', array(''));
        $this->assertEqualsRequest('string', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeString2()
    {
        $request    = new \fpoirotte\XRL\Request('stringParam', array('test'));
        $this->assertEqualsRequest('string2', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeDouble()
    {
        $request    = new \fpoirotte\XRL\Request('doubleParam', array(3.14));
        $this->assertEqualsRequest('double', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeDatetime()
    {
        // Emulate a client located in Metropolitain France.
        $tz         = new \DateTimeZone('Europe/Paris');
        $date       = new \DateTime('1985-11-28T14:00:00', $tz);
        $request    = new \fpoirotte\XRL\Request('dateTimeParam', array($date));
        $this->assertEqualsRequest('datetime', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeBinary()
    {
        // An invalid UTF-8 sequence.
        $request    = new \fpoirotte\XRL\Request('binaryParam', array("\xE8\xE9\xE0"));
        $this->assertEqualsRequest('binary', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeNumericArray()
    {
        $array      = array('test', 42);
        $request    = new \fpoirotte\XRL\Request('numArray', array($array));
        $this->assertEqualsRequest('num_array', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeEmptyArray()
    {
        $array      = array();
        $request    = new \fpoirotte\XRL\Request('numArray', array($array));
        $this->assertEqualsRequest('empty_array', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeAssociativeArray()
    {
        $array      = array('foo' => 'test', 'bar' => 42);
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $this->assertEqualsRequest('assoc_array', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeAssociativeArray2()
    {
        $array      = array('foo', 42 => 'bar');
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $this->assertEqualsRequest('assoc_array2', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeAssociativeArray3()
    {
        $array      = array(42 => 'foo', 'bar');
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $this->assertEqualsRequest('assoc_array3', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeSimpleXmlElement()
    {
        $sxml = simplexml_load_string(self::TEST_DOM);
        $request    = new \fpoirotte\XRL\Request('domParam', array($sxml));
        $this->assertEqualsRequest('dom', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeDomnode()
    {
        $dom = new \DomDocument();
        $dom->loadXML(self::TEST_DOM);
        $request    = new \fpoirotte\XRL\Request('domParam', array($dom));
        $this->assertEqualsRequest('dom', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeRequest
     * @covers          \fpoirotte\XRL\NativeEncoder::convert
     */
    public function testEncodeXmlWriter()
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->writeRaw(self::TEST_DOM);
        $request    = new \fpoirotte\XRL\Request('domParam', array($writer));
        $this->assertEqualsRequest('dom', $this->encoder->encodeRequest($request));
    }

    /**
     * @covers          \fpoirotte\XRL\Encoder::encodeError
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeError
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
     * @covers          \fpoirotte\XRL\NativeEncoder::encodeResponse
     */
    public function testEncodeSuccessfulResponse()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'responses' .
            DIRECTORY_SEPARATOR . 'success.xml'
        );

        $response   = array(42, 'test');
        $received   = $this->encoder->encodeResponse($response);
        $this->assertSame($content, $received);
    }
}
