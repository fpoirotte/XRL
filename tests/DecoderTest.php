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
    protected function getXML($folder, $filename)
    {
        // Emulate a server located in Ireland.
        $tz = new \DateTimeZone("Europe/Dublin");

        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . $folder .
            DIRECTORY_SEPARATOR . $filename . '.xml'
        );

        $result = array(
            array(new \fpoirotte\XRL\Decoder($tz, true), $content, true),
            array(new \fpoirotte\XRL\Decoder($tz, false), $content, true),
        );

        // Remove all whitespaces.
        $content = str_replace(array(' ', "\n", "\r", "\t"), '', $content);

        // Remove the XML declaration.
        $content = str_replace(
            '<'.'?xmlversion="1.0"encoding="UTF-8"?'.'>',
            '',
            $content
        );

        $result[] = array(new \fpoirotte\XRL\Decoder($tz, true), $content, false);
        $result[] = array(new \fpoirotte\XRL\Decoder($tz, false), $content, false);
        return $result;
    }

    public function requestProvider($method)
    {
        $len = strlen('testDecodeRequestWith');
        if (strncmp($method, 'testDecodeRequestWith', $len)) {
            throw new \Exception('Bad request for provider');
        }
        $method = (string) substr($method, $len);

        if (strpos($method, 'Parameters') !== false) {
            list($prefix, $index) = explode('Parameters', $method);
        } elseif (strpos($method, 'Parameter') !== false) {
            list($prefix, $index) = explode('Parameter', $method);
        } elseif (strpos($method, 'Array') !== false) {
            list($prefix, $index) = explode('Array', $method);
        } else {
            list($prefix, $index) = array($method, '');
        }

        $mapping = array(
            'Empty'         => 'empty',
            'Multiple'      => 'multi',
            'Integer'       => 'int',
            'Boolean'       => 'bool',
            'String'        => 'string',
            'Double'        => 'double',
            'Numeric'       => 'num_array',
            'Associative'   => 'assoc_array',
            'Binary'        => 'binary',
            'DateTime'      => 'datetime',
        );

        if (isset($mapping[$prefix])) {
            return $this->getXML('requests', $mapping[$prefix] . $index);
        }
        return $this->getXML('requests', $prefix . $index);
    }

    public function responseProvider($method)
    {
        $len = strlen('testDecode');
        if (strncmp($method, 'testDecode', $len)) {
            throw new \Exception('Bad request for provider');
        }
        $method = (string) substr($method, $len);

        if ($method == 'Failure') {
            return $this->getXML('responses', 'failure');
        }
        if ($method == 'SuccessfulResponse') {
            return $this->getXML('responses', 'success');
        }
        throw new \Exception('Request for unknown data');
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithEmptyParameters($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('emptyParams', $request->getProcedure());
        $this->assertEquals(0, count($params));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithMultipleParameters($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('multiParams', $request->getProcedure());
        $this->assertEquals(2, count($params));
        $this->assertSame(42, $params[0]->get());
        $this->assertSame('test', $params[1]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithIntegerParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('intParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(42, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBooleanParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(true, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBooleanParameter2($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(false, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithStringParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('', $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithStringParameter2($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('test', $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithDoubleParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('doubleParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(3.14, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithDateTimeParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('dateTimeParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // Emulate a client located in Metropolitain France.
        $tz         = new \DateTimeZone('Europe/Paris');
        $reference  = new \DateTime('1985-11-28T14:00:00+0100', $tz);
        $this->assertEquals($reference->format('U'), $params[0]->get()->format('U'));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBinaryParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('binaryParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame("\xE8\xE9\xE0", $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithNumericArray($decoder, $xml)
    {
        $array      = array('test', 42);
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithNumericArray2($decoder, $xml)
    {
        $array      = array();
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray($decoder, $xml)
    {
        $array      = array('foo' => 'test', 'bar' => 42);
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray2($decoder, $xml)
    {
        $array      = array('foo', 42 => 'bar');
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]->get());
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray3($decoder, $xml)
    {
        $array      = array(42 => 'foo', 'bar');
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof \fpoirotte\XRL\Request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]->get());
    }

    /**
     * @dataProvider responseProvider
     */
    public function testDecodeFailure($decoder, $xml, $indented)
    {
        $response = null;
        try {
            $decoder->decodeResponse($xml);
        } catch (\Exception $response) {
            // Nothing to do here.
        }
        if (!$response) {
            $this->fail('An exception was expected');
        }

        $this->assertTrue($response instanceof \fpoirotte\XRL\Exception);
        $this->assertEquals(42, $response->getCode());
        if ($indented) {
            $expected = 'Exception: Test_failure';
        } else {
            $expected = 'Exception:Test_failure';
        }
        $this->assertEquals($expected, $response->getMessage());
    }

    /**
     * @dataProvider responseProvider
     */
    public function testDecodeSuccessfulResponse($decoder, $xml)
    {
        $expected = array(42, 'test');
        $response = $decoder->decodeResponse($xml)->get();
        $this->assertSame($expected, $response);
    }
}
