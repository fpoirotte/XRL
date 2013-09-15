<?php
// © copyright XRL Team, 2012. All rights reserved.
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
*/

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers.php');

class   DecoderTest
extends PHPUnit_Framework_TestCase
{
    protected function _getXML($folder, $filename)
    {
        // Emulate a server located in Ireland.
        $tz = new DateTimeZone("Europe/Dublin");

        $content = file_get_contents(
            dirname(__FILE__) .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . $folder .
            DIRECTORY_SEPARATOR . $filename . '.xml'
        );

        $result = array(
            array(new XRL_Decoder($tz, TRUE), $content, TRUE),
            array(new XRL_Decoder($tz, FALSE), $content, TRUE),
        );

        // Remove all whitespaces.
        $content = str_replace(array(' ', "\n", "\r", "\t"), '', $content);

        // Remove the XML declaration.
        $content = str_replace(
            '<'.'?xmlversion="1.0"encoding="UTF-8"?'.'>',
            '',
            $content
        );

        $result[] = array(new XRL_Decoder($tz, TRUE), $content, FALSE);
        $result[] = array(new XRL_Decoder($tz, FALSE), $content, FALSE);
        return $result;
    }

    public function requestProvider($method)
    {
        $len = strlen('testDecodeRequestWith');
        if (strncmp($method, 'testDecodeRequestWith', $len))
            throw new Exception('Bad request for provider');
        $method = (string) substr($method, $len);

        if (strpos($method, 'Parameters') !== FALSE)
            list($prefix, $index) = explode('Parameters', $method);
        else if (strpos($method, 'Parameter') !== FALSE)
            list($prefix, $index) = explode('Parameter', $method);
        else if (strpos($method, 'Array') !== FALSE)
            list($prefix, $index) = explode('Array', $method);
        else
            list($prefix, $index) = array($method, '');

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

        if (isset($mapping[$prefix]))
            return $this->_getXML('requests', $mapping[$prefix] . $index);
        return $this->_getXML('requests', $prefix . $index);
    }

    public function responseProvider($method)
    {
        $len = strlen('testDecode');
        if (strncmp($method, 'testDecode', $len))
            throw new Exception('Bad request for provider');
        $method = (string) substr($method, $len);

        if ($method == 'Failure')
            return $this->_getXML('responses', 'failure');
        if ($method == 'SuccessfulResponse')
            return $this->_getXML('responses', 'success');
        throw new Exception('Request for unknown data');
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithEmptyParameters($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

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
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('multiParams', $request->getProcedure());
        $this->assertEquals(2, count($params));
        $this->assertSame(42, $params[0]);
        $this->assertSame('test', $params[1]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithIntegerParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('intParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(42, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBooleanParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(TRUE, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBooleanParameter2($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(FALSE, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithStringParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('', $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithStringParameter2($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('test', $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithDoubleParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('doubleParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(3.14, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithDateTimeParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('dateTimeParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        // Emulate a client located in Metropolitain France.
        $tz         = new DateTimeZone('Europe/Paris');
        $reference  = new DateTime('1985-11-28T14:00:00+0100', $tz);
        $this->assertEquals($reference->format('U'), $params[0]->format('U'));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBinaryParameter($decoder, $xml)
    {
        $request = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('binaryParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame("\xE8\xE9\xE0", $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithNumericArray($decoder, $xml)
    {
        $array      = array('test', 42);
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithNumericArray2($decoder, $xml)
    {
        $array      = array();
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray($decoder, $xml)
    {
        $array      = array('foo' => 'test', 'bar' => 42);
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray2($decoder, $xml)
    {
        $array      = array('foo', 42 => 'bar');
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray3($decoder, $xml)
    {
        $array      = array(42 => 'foo', 'bar');
        $request    = $decoder->decodeRequest($xml);
        $this->assertTrue($request instanceof XRL_Request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

#    public function testDecodeRequestWithObject()
#    {
#        $request    = new XRL_Request('objParam', array());
#        $received   = $decoder->decodeRequest($request);
#        $this->assertEquals($this->METHOD_OBJ_PARAM, $received);
#    }

    /**
     * @dataProvider responseProvider
     */
    public function testDecodeFailure($decoder, $xml, $indented)
    {
        $response = NULL;
        try {
            $decoder->decodeResponse($xml);
        }
        catch (Exception $response) {
            // Nothing to do here.
        }
        if (!$response)
            $this->fail('An exception was expected');

        $this->assertTrue($response instanceof XRL_Exception);
        $this->assertEquals(42, $response->getCode());
        if ($indented)
            $expected = 'Exception: Test_failure';
        else
            $expected = 'Exception:Test_failure';
        $this->assertEquals($expected, $response->getMessage());
    }

    /**
     * @dataProvider responseProvider
     */
    public function testDecodeSuccessfulResponse($decoder, $xml)
    {
        $expected = array(42, 'test');
        $response = $decoder->decodeResponse($xml);
        $this->assertSame($expected, $response);
    }
}

