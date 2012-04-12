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

include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers.php');

class   DecoderTest
extends PHPUnit_Framework_TestCase
{
    protected $_decoder;

    public function setUp()
    {
        $this->_decoder = new XRL_Decoder(FALSE);
    }

    protected function _getXML($folder, $filename)
    {
        $content = file_get_contents(
            dirname(__FILE__) .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . $folder .
            DIRECTORY_SEPARATOR . $filename . '.xml'
        );

        $result = array(array($content, TRUE));

        // Remove all whitespaces.
        $content = str_replace(array(' ', "\n", "\r", "\t"), '', $content);

        // Remove the XML declaration.
        $content = str_replace(
            '<'.'?xmlversion="1.0"encoding="UTF-8"?'.'>',
            '',
            $content
        );

        $result[] = array($content, FALSE);
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
    public function testDecodeRequestWithEmptyParameters($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('emptyParams', $request->getProcedure());
        $this->assertEquals(0, count($params));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithMultipleParameters($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('multiParams', $request->getProcedure());
        $this->assertEquals(2, count($params));
        $this->assertSame(42, $params[0]);
        $this->assertSame('test', $params[1]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithIntegerParameter($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('intParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(42, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBooleanParameter($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(TRUE, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBooleanParameter2($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('boolParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(FALSE, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithStringParameter($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('', $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithStringParameter2($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('test', $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithStringParameter3($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('stringParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame('test', $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithDoubleParameter($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('doubleParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame(3.14, $params[0]);
    }

#    public function testDecodeRequestWithDateTimeParameter()
#    {
#        $request    = new XRL_Request('dateTimeParam', array());
#        $received   = $this->_decoder->decodeRequest($request);
#        $this->assertEquals($this->METHOD_DATETIME_PARAM, $received);
#    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithBinaryParameter($xml)
    {
        $request = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('binaryParam', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame("\xE8\xE9\xE0", $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithNumericArray($xml)
    {
        $array      = array('test', 42);
        $request    = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithNumericArray2($xml)
    {
        $array      = array();
        $request    = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('numArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray($xml)
    {
        $array      = array('foo' => 'test', 'bar' => 42);
        $request    = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray2($xml)
    {
        $array      = array('foo', 42 => 'bar');
        $request    = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

    /**
     * @dataProvider requestProvider
     */
    public function testDecodeRequestWithAssociativeArray3($xml)
    {
        $array      = array(42 => 'foo', 'bar');
        $request    = $this->_decoder->decodeRequest($xml);
        $this->assertInstanceOf('XRL_Request', $request);

        $params = $request->getParams();
        $this->assertEquals('assocArray', $request->getProcedure());
        $this->assertEquals(1, count($params));
        $this->assertSame($array, $params[0]);
    }

#    public function testDecodeRequestWithObject()
#    {
#        $request    = new XRL_Request('objParam', array());
#        $received   = $this->_decoder->decodeRequest($request);
#        $this->assertEquals($this->METHOD_OBJ_PARAM, $received);
#    }

    /**
     * @dataProvider responseProvider
     */
    public function testDecodeFailure($xml, $indented)
    {
        $response = NULL;
        try {
            $this->_decoder->decodeResponse($xml);
        }
        catch (Exception $response) {
            // Nothing to do here.
        }
        if (!$response)
            $this->fail('An exception was expected');

        $this->assertInstanceOf('XRL_Exception', $response);
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
    public function testDecodeSuccessfulResponse($xml)
    {
        $expected = array(42, 'test');
        $response = $this->_decoder->decodeResponse($xml);
        $this->assertSame($expected, $response);
    }
}

