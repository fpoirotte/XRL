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

class Decoder extends \PHPUnit_Framework_TestCase
{
    public function getRequest($path)
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'requests' .
            DIRECTORY_SEPARATOR .
            str_replace('/', DIRECTORY_SEPARATOR, $path . '.xml')
        );

        $decoder = new \fpoirotte\XRL\NativeDecoder(
            new \fpoirotte\XRL\Decoder(
                new \DateTimeZone("Europe/Dublin"),
                true
            )
        );

        $request = $decoder->decodeRequest(
            'data://;base64,' . base64_encode($content)
        );
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Request', $request);
        return $request;
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeEmptyParameters()
    {
        $request    = $this->getRequest('empty');
        $params     = $request->getParams();
        $this->assertEquals('emptyParams', $request->getProcedure());
        $this->assertEquals(0, count($params));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeMultipleParameters()
    {
        $request    = $this->getRequest('multi');
        $params     = $request->getParams();
        $this->assertEquals('multiParams', $request->getProcedure());
        $this->assertEquals(2, count($params));
        $this->assertSame(42, $params[0]);
        $this->assertSame('test', $params[1]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeInteger()
    {
        $request    = $this->getRequest('int');
        $params     = $request->getParams();
        $this->assertEquals('intParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // 2**31-1
        $this->assertSame((1 << 30) - 1 + (1 << 30), $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecode8BitsSignedInteger()
    {
        $request    = $this->getRequest('i1');
        $params     = $request->getParams();
        $this->assertEquals('i1Param', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // 2**8-1
        $this->assertSame((1 << 7) - 1, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecode16BitsSignedInteger()
    {
        $request    = $this->getRequest('i2');
        $params     = $request->getParams();
        $this->assertEquals('i2Param', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // 2**16-1
        $this->assertSame((1 << 15) - 1, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecode32BitsSignedInteger()
    {
        $request    = $this->getRequest('i4');
        $params     = $request->getParams();
        $this->assertEquals('i4Param', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // 2**31-1
        $this->assertSame((1 << 30) - 1 + (1 << 30), $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecode64BitsSignedInteger()
    {
        $request    = $this->getRequest('i8');
        $params     = $request->getParams();
        $this->assertEquals('i8Param', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // 2**63-1
        $expected = gmp_sub(gmp_pow(2, 63), 1);
        $this->assertSame(gmp_strval($expected), gmp_strval($params[0]));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecode64BitsSignedInteger2()
    {
        $request    = $this->getRequest('i82');
        $params     = $request->getParams();
        $this->assertEquals('i8Param', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // 2**63-1
        $expected = gmp_sub(gmp_pow(2, 63), 1);
        $this->assertSame(gmp_strval($expected), gmp_strval($params[0]));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeBigInteger()
    {
        $request    = $this->getRequest('bigint');
        $params     = $request->getParams();
        $this->assertEquals('bigintParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // 2**63
        $expected = gmp_pow(2, 63);
        $this->assertSame(gmp_strval($expected), gmp_strval($params[0]));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeBoolean()
    {
        $request    = $this->getRequest('bool');
        $params     = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(true, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeBoolean2()
    {
        $request    = $this->getRequest('bool2');
        $params     = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(false, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeString()
    {
        $request    = $this->getRequest('string');
        $params     = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeString2()
    {
        $request    = $this->getRequest('string2');
        $params     = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('test', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeString3()
    {
        $request    = $this->getRequest('string3');
        $params     = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeDouble()
    {
        $request    = $this->getRequest('double');
        $params     = $request->getParams();
        $this->assertEquals('doubleParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(3.14, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeDatetime()
    {
        $request    = $this->getRequest('datetime');
        $params     = $request->getParams();
        $this->assertEquals('dateTimeParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // Emulate a client located in Metropolitain France.
        $tz         = new \DateTimeZone('Europe/Paris');
        $reference  = new \DateTime('1985-11-28T14:00:00+0100', $tz);
        $this->assertEquals($reference->format('U'), $params[0]->format('U'));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeDom()
    {
        $xml = <<<XML
            <ns:foo xmlns:ns="http://example.com/ns">
                <bar baz="42" xmlns:ns2="http://example.com/ns2" ns2:qux="blah"/>
            </ns:foo>
XML;
        $dom = new \DomDocument();
        $dom->loadXML($xml);

        $request    = $this->getRequest('dom');
        $params     = $request->getParams();
        $this->assertEquals('domParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertEqualXmlStructure(
            $dom->firstChild,
            dom_import_simplexml($params[0])
        );
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeBinary()
    {
        $request    = $this->getRequest('binary');
        $params     = $request->getParams();
        $this->assertEquals('binaryParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame("\xE8\xE9\xE0", $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeNumericArray()
    {
        $request    = $this->getRequest('num_array');
        $array      = array('test', 42);
        $params     = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeEmptyArray()
    {
        $request    = $this->getRequest('empty_array');
        $array      = array();
        $params     = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeAssociativeArray()
    {
        $request    = $this->getRequest('assoc_array');
        $array      = array('foo' => 'test', 'bar' => 42);
        $params     = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeAssociativeArray2()
    {
        $request    = $this->getRequest('assoc_array2');
        $array      = array('foo', 42 => 'bar');
        $params     = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeRequest
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeAssociativeArray3()
    {
        $request    = $this->getRequest('assoc_array3');
        $array      = array(42 => 'foo', 'bar');
        $params     = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @covers                      \fpoirotte\XRL\Decoder::decodeResponse
     * @covers                      \fpoirotte\XRL\NativeDecoder::decodeRequest
     * @expectedException           \fpoirotte\XRL\Exception
     * @expectedExceptionCode       42
     * @expectedExceptionMessage    Exception: Test_failure
     */
    public function testDecodeFailureResponse()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'responses' .
            DIRECTORY_SEPARATOR . 'failure.xml'
        );

        $decoder = new \fpoirotte\XRL\NativeDecoder(
            new \fpoirotte\XRL\Decoder(
                new \DateTimeZone("Europe/Dublin"),
                true
            )
        );

        $decoder->decodeResponse('data://;base64,' . base64_encode($content));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeResponse
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeSuccessfulResponse()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'responses' .
            DIRECTORY_SEPARATOR . 'success.xml'
        );

        $decoder = new \fpoirotte\XRL\NativeDecoder(
            new \fpoirotte\XRL\Decoder(
                new \DateTimeZone("Europe/Dublin"),
                true
            )
        );

        $response = $decoder->decodeResponse(
            'data://;base64,' . base64_encode($content)
        );
        $this->assertSame(array(42, 'test'), $response);
    }
}
