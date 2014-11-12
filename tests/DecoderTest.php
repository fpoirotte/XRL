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

        $decoder = new \fpoirotte\XRL\Decoder(
            new \DateTimeZone("Europe/Dublin"),
            true
        );

        $request = $decoder->decodeRequest(
            'data://;base64,' . base64_encode($content)
        );
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Request', $request);
        return $request;
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeEmptyParameters()
    {
        $request    = $this->getRequest('empty');
        $params     = $request->getParams();
        $this->assertSame('emptyParams', $request->getProcedure());
        $this->assertSame(0, count($params));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeMultipleParameters()
    {
        $request    = $this->getRequest('multi');
        $params     = $request->getParams();
        $this->assertSame('multiParams', $request->getProcedure());
        $this->assertSame(2, count($params));
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeInteger()
    {
        $request    = $this->getRequest('int');
        $params     = $request->getParams();
        $this->assertSame('intParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Int', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecode8BitsSignedInteger()
    {
        $request    = $this->getRequest('i1');
        $params     = $request->getParams();
        $this->assertSame('i1Param', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\I1', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecode16BitsSignedInteger()
    {
        $request    = $this->getRequest('i2');
        $params     = $request->getParams();
        $this->assertSame('i2Param', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\I2', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecode32BitsSignedInteger()
    {
        $request    = $this->getRequest('i4');
        $params     = $request->getParams();
        $this->assertSame('i4Param', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\I4', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecode64BitsSignedInteger()
    {
        // The type in this test file has no namespace.
        $request    = $this->getRequest('i8');
        $params     = $request->getParams();
        $this->assertSame('i8Param', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\I8', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecode64BitsSignedInteger2()
    {
        // This test file uses Apache's extension namespace.
        $request    = $this->getRequest('i82');
        $params     = $request->getParams();
        $this->assertSame('i8Param', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\I8', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeBigInteger()
    {
        $request    = $this->getRequest('bigint');
        $params     = $request->getParams();
        $this->assertSame('bigintParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\BigInteger', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeBoolean()
    {
        // true
        $request    = $this->getRequest('bool');
        $params     = $request->getParams();
        $this->assertSame('boolParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Boolean', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeBoolean2()
    {
        // false
        $request    = $this->getRequest('bool2');
        $params     = $request->getParams();
        $this->assertSame('boolParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Boolean', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeString()
    {
        // empty string with tag
        $request    = $this->getRequest('string');
        $params     = $request->getParams();
        $this->assertSame('stringParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\String', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeString2()
    {
        // regular string
        $request    = $this->getRequest('string2');
        $params     = $request->getParams();
        $this->assertSame('stringParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\String', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeString3()
    {
        // empty string without any tag
        $request    = $this->getRequest('string3');
        $params     = $request->getParams();
        $this->assertSame('stringParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\String', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeDouble()
    {
        $request    = $this->getRequest('double');
        $params     = $request->getParams();
        $this->assertSame('doubleParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Double', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeDatetime()
    {
        $request    = $this->getRequest('datetime');
        $params     = $request->getParams();
        $this->assertSame('dateTimeParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\DateTimeIso8601', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeDom()
    {
        $request    = $this->getRequest('dom');
        $params     = $request->getParams();
        $this->assertSame('domParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Dom', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeBinary()
    {
        $request    = $this->getRequest('binary');
        $params     = $request->getParams();
        $this->assertSame('binaryParam', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Base64', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeNumericArray()
    {
        $request    = $this->getRequest('num_array');
        $params     = $request->getParams();
        $this->assertSame('numArray', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\ArrayType', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeEmptyArray()
    {
        $request    = $this->getRequest('empty_array');
        $params     = $request->getParams();
        $this->assertSame('numArray', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\ArrayType', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeAssociativeArray()
    {
        $request    = $this->getRequest('assoc_array');
        $params     = $request->getParams();
        $this->assertSame('assocArray', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Struct', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeAssociativeArray2()
    {
        $request    = $this->getRequest('assoc_array2');
        $params     = $request->getParams();
        $this->assertSame('assocArray', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Struct', $params[0]);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder
     */
    public function testDecodeAssociativeArray3()
    {
        $request    = $this->getRequest('assoc_array3');
        $params     = $request->getParams();
        $this->assertSame('assocArray', $request->getProcedure());
        $this->assertSame(1, count($params));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Struct', $params[0]);
    }

    /**
     * @covers                      \fpoirotte\XRL\Decoder::decodeResponse
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

        $decoder = new \fpoirotte\XRL\Decoder();
        $response = $decoder->decodeResponse(
            'data://;base64,' . base64_encode($content)
        );
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\Struct', $response);
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\I4', $response['faultCode']);
        $this->assertInstanceOf('Exception: Test_failure', $response['faultString']);
#        $this->assertSame(42);
    }

    /**
     * @covers          \fpoirotte\XRL\Decoder::decodeResponse
     */
    public function testDecodeSuccessfulResponse()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'responses' .
            DIRECTORY_SEPARATOR . 'success.xml'
        );

        $decoder = new \fpoirotte\XRL\Decoder();
        $response = $decoder->decodeResponse(
            'data://;base64,' . base64_encode($content)
        );
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\ArrayType', $response);
        $this->assertSame(2, count($response));
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\I4', $response[0]);
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\String', $response[1]);
    }
}
