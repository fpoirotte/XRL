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

class Client extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->encoder = $this->getMockBuilder('\\fpoirotte\\XRL\\EncoderInterface')->getMock();
        $this->decoder = $this->getMockBuilder('\\fpoirotte\\XRL\\DecoderInterface')->getMock();
    }

    /**
     * @covers          \fpoirotte\XRL\Client
     */
    public function testClient()
    {
        $this->encoder
            ->expects($this->once())
            ->method('encodeRequest')
            ->with($this->isInstanceOf('\\fpoirotte\\XRL\\Request'))
            ->will($this->returnValue('something'));

        $this->decoder
            ->expects($this->once())
            ->method('decodeResponse')
            ->with($this->equalTo('http://example.com'))
            ->will($this->returnValue('qux'));

        $client = new \fpoirotte\XRL\Client('http://example.com', $this->encoder, $this->decoder);

        $this->assertSame('qux', $client->foo());

#        $context    = stream_context_get_default();
#        $options    = stream_context_get_options($context);
#        $this->assertSame('something', $options['http']['content']);
    }

    /**
     * @covers          \fpoirotte\XRL\Client
     */
    public function testClient2()
    {
        $data   = 'data://;base64,' . base64_encode(
            file_get_contents(
                __DIR__ .
                DIRECTORY_SEPARATOR . 'testdata' .
                DIRECTORY_SEPARATOR . 'responses' .
                DIRECTORY_SEPARATOR . 'success.xml'
            )
        );
        $client = new \fpoirotte\XRL\Client($data);
        $this->assertSame(array(42, 'test'), $client->foo());
    }
}
