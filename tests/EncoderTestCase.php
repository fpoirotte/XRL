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

abstract class  AbstractEncoder_TestCase
extends         PHPUnit_Framework_TestCase
{
    protected $_encoder;

    abstract protected function _getXML($folder, $filename);

    public function setUp()
    {
        $this->_encoder = new XRL_Encoder($this->_format);
    }

    public function testEncodeRequestWithEmptyParameters()
    {
        $request    = new XRL_Request('emptyParams', array());
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'empty');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithMultipleParameters()
    {
        $request    = new XRL_Request('multiParams', array(42, 'test'));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'multi');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithIntegerParameter()
    {
        $request    = new XRL_Request('intParam', array(42));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'int');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithBooleanParameter()
    {
        $request    = new XRL_Request('boolParam', array(TRUE));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'bool');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithBooleanParameter2()
    {
        $request    = new XRL_Request('boolParam', array(FALSE));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'bool2');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithStringParameter()
    {
        $request    = new XRL_Request('stringParam', array(''));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'string');
        $this->assertEquals($expected, $received);
    }
    public function testEncodeRequestWithStringParameter2()
    {
        $request    = new XRL_Request('stringParam', array('test'));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'string2');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithDoubleParameter()
    {
        $request    = new XRL_Request('doubleParam', array(3.14));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'double');
        $this->assertEquals($expected, $received);
    }

#    public function testEncodeRequestWithDateTimeParameter()
#    {
#        $request    = new XRL_Request('dateTimeParam', array());
#        $received   = $this->_encoder->encodeRequest($request);
#        $this->assertEquals($this->METHOD_DATETIME_PARAM, $received);
#    }

    public function testEncodeRequestWithBinaryParameter()
    {
        // An invalid UTF-8 sequence.
        $request    = new XRL_Request('binaryParam', array("\xE8\xE9\xE0"));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'binary');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithNumericArray()
    {
        $array      = array('test', 42);
        $request    = new XRL_Request('numArray', array($array));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'num_array');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithNumericArray2()
    {
        $array      = array();
        $request    = new XRL_Request('numArray', array($array));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'num_array2');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithAssociativeArray()
    {
        $array      = array('foo' => 'test', 'bar' => 42);
        $request    = new XRL_Request('assocArray', array($array));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'assoc_array');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithAssociativeArray2()
    {
        $array      = array('foo', 42 => 'bar');
        $request    = new XRL_Request('assocArray', array($array));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'assoc_array2');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeRequestWithAssociativeArray3()
    {
        $array      = array(42 => 'foo', 'bar');
        $request    = new XRL_Request('assocArray', array($array));
        $received   = $this->_encoder->encodeRequest($request);
        $expected   = $this->_getXML('requests', 'assoc_array3');
        $this->assertEquals($expected, $received);
    }

#    public function testEncodeRequestWithObject()
#    {
#        $request    = new XRL_Request('objParam', array());
#        $received   = $this->_encoder->encodeRequest($request);
#        $this->assertEquals($this->METHOD_OBJ_PARAM, $received);
#    }

    public function testEncodeFailure()
    {
        $failure    = new Exception('Test_failure', 42);
        $received   = $this->_encoder->encodeError($failure);
        $expected   = $this->_getXML('responses', 'failure');
        $this->assertEquals($expected, $received);
    }

    public function testEncodeSuccessfulResponse()
    {
        $response   = array(42, 'test');
        $received   = $this->_encoder->encodeResponse($response);
        $expected   = $this->_getXML('responses', 'success');
        $this->assertEquals($expected, $received);
    }
}

