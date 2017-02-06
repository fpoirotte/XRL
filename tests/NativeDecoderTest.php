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

class NativeDecoder extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        $this->decoder = $this->getMockBuilder('\\fpoirotte\\XRL\\DecoderInterface')->getMock();
    }

    /**
     * @covers          \fpoirotte\XRL\NativeDecoder::__construct
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeRequest
     */
    public function testDecodeRequest()
    {
        $URI = 'foo';
        $type = $this->getMockBuilder('\\fpoirotte\\XRL\\Types\\AbstractType')
            ->disableOriginalConstructor()
            ->getMock();

        $type
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue('baz'));
        $this->decoder
            ->expects($this->once())
            ->method('decodeRequest')
            ->with($this->equalTo($URI))
            ->will($this->returnValue(
                new \fpoirotte\XRL\Request('bar', array($type))
            ));

        $decoder = new \fpoirotte\XRL\NativeDecoder($this->decoder);
        $request = $decoder->decodeRequest($URI);
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Request', $request);
        $this->assertSame('bar', $request->getProcedure());
        $this->assertSame(array('baz'), $request->getParams());
    }

    /**
     * @covers          \fpoirotte\XRL\NativeDecoder::__construct
     * @covers          \fpoirotte\XRL\NativeDecoder::decodeResponse
     */
    public function testDecodeResponse()
    {
        $URI = 'foo';
        $type = $this->getMockBuilder('\\fpoirotte\\XRL\\Types\\AbstractType')
            ->disableOriginalConstructor()
            ->getMock();

        $type
            ->expects($this->once())
            ->method('get')
            ->will($this->returnValue('baz'));
        $this->decoder
            ->expects($this->once())
            ->method('decodeResponse')
            ->with($this->equalTo($URI))
            ->will($this->returnValue($type));

        $decoder = new \fpoirotte\XRL\NativeDecoder($this->decoder);
        $response = $decoder->decodeResponse($URI);
        $this->assertSame('baz', $response);
    }
}
