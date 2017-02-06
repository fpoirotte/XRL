<?php
/*
 * This file is part of XRL, a simple XML-RPC Library for PHP.
 *
 * Copyright (c) 2015, XRL Team. All rights reserved.
 * XRL is licensed under the 3-clause BSD License.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fpoirotte\XRL\tests;

class TestResponse extends \fpoirotte\XRL\Response
{
    public $headers = array();

    protected function addHeader($header)
    {
        $this->headers[] = $header;
        return $this;
    }

    protected function finalize($result)
    {
        // We just echo the result so that phpunit can capture it.
        echo $result;
    }
}

class Response extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \fpoirotte\XRL\Response::__construct
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Not a valid response
     */
    public function testConstructor()
    {
        $dummy = new \fpoirotte\XRL\Response(null);
    }

    /**
     * @covers \fpoirotte\XRL\Response::__construct
     * @covers \fpoirotte\XRL\Response::__toString
     */
    public function testRendering()
    {
        $response = new \fpoirotte\XRL\Response('abc');
        $this->assertSame('abc', (string) $response);
    }

    /**
     * @covers \fpoirotte\XRL\Response::__construct
     * @covers \fpoirotte\XRL\Response::publish
     */
    public function testPublication()
    {
        $response = new TestResponse('abc');
        $this->expectOutputString('abc');

        $response->publish();
        $expected = array(
            'Content-Type: text/xml',
            'Content-Length: 3',
        );
        $this->assertEquals($expected, $response->headers);
    }
}
