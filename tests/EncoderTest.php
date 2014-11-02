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
        $content = str_replace(array("\r\n", "\r"), "\n", $content);

        // Indent, use <string>.
        $result = array(array(new \fpoirotte\XRL\Encoder($tz, true, true), $content, true));

        // Stripped indented.
        $stripped = preg_replace('#\\s*<string>|</string>\\s*#', '', $content);
        // Indent, don't use <string>
        $result[] = array(new \fpoirotte\XRL\Encoder($tz, true, false), $stripped, true);

        // Remove all whitespaces.
        $content = str_replace(array(' ', "\n", "\r", "\t"), '', $content);

        // Remove the XML declaration.
        $content = str_replace(
            '<'.'?xmlversion="1.0"encoding="UTF-8"?'.'>',
            '',
            $content
        );

        // No indent, use <string>
        $result[] = array(new \fpoirotte\XRL\Encoder($tz, false, true), $content, false);

        // Stripped unindented.
        $stripped = str_replace(array('<string>', '</string>'), '', $content);
        // No indent, don't use <string>
        $result[] = array(new \fpoirotte\XRL\Encoder($tz, false, false), $stripped, false);

        return $result;
    }

    public function requestProvider($method)
    {
        $len = strlen('testEncodeRequestWith');
        if (strncmp($method, 'testEncodeRequestWith', $len)) {
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
        $len = strlen('testEncode');
        if (strncmp($method, 'testEncode', $len)) {
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
    public function testEncodeRequestWithEmptyParameters($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('emptyParams', array());
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithMultipleParameters($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('multiParams', array(42, 'test'));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithIntegerParameter($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('intParam', array(42));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithBooleanParameter($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('boolParam', array(true));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithBooleanParameter2($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('boolParam', array(false));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithStringParameter($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('stringParam', array(''));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithStringParameter2($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('stringParam', array('test'));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithDoubleParameter($encoder, $expected)
    {
        $request    = new \fpoirotte\XRL\Request('doubleParam', array(3.14));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithDateTimeParameter($encoder, $expected)
    {
        // Emulate a client located in Metropolitain France.
        $tz         = new \DateTimeZone('Europe/Paris');
        $date       = new \DateTime('1985-11-28T14:00:00', $tz);
        $request    = new \fpoirotte\XRL\Request('dateTimeParam', array($date));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithBinaryParameter($encoder, $expected)
    {
        // An invalid UTF-8 sequence.
        $request    = new \fpoirotte\XRL\Request('binaryParam', array("\xE8\xE9\xE0"));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithNumericArray($encoder, $expected)
    {
        $array      = array('test', 42);
        $request    = new \fpoirotte\XRL\Request('numArray', array($array));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithNumericArray2($encoder, $expected)
    {
        $array      = array();
        $request    = new \fpoirotte\XRL\Request('numArray', array($array));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithAssociativeArray($encoder, $expected)
    {
        $array      = array('foo' => 'test', 'bar' => 42);
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithAssociativeArray2($encoder, $expected)
    {
        $array      = array('foo', 42 => 'bar');
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider requestProvider
     */
    public function testEncodeRequestWithAssociativeArray3($encoder, $expected)
    {
        $array      = array(42 => 'foo', 'bar');
        $request    = new \fpoirotte\XRL\Request('assocArray', array($array));
        $received   = $encoder->encodeRequest($request);
        $this->assertEquals($expected, $received, var_export($encoder, true));
    }

    /**
     * @dataProvider responseProvider
     */
    public function testEncodeFailure($encoder, $expected, $indented)
    {
        $failure    = new \Exception('Test_failure', 42);
        $received   = $encoder->encodeError($failure);
        if (!$indented) {
            $received = str_replace('Exception: ', 'Exception:', $received);
        }
        $this->assertEquals($expected, $received);
    }

    /**
     * @dataProvider responseProvider
     */
    public function testEncodeSuccessfulResponse($encoder, $expected)
    {
        $response   = array(42, 'test');
        $received   = $encoder->encodeResponse($response);
        $this->assertEquals($expected, $received);
    }
}
