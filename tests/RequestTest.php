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

class Request extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers                      \fpoirotte\XRL\Request::__construct
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Invalid procedure name
     */
    public function testErrorDetection()
    {
        $dummy = new \fpoirotte\XRL\Request(null, array());
    }

    /**
     * @covers \fpoirotte\XRL\Request
     */
    public function testContainer()
    {
        $request = new \fpoirotte\XRL\Request('procname', array(42, 'foo'));
        $this->assertSame('procname', $request->getProcedure());
        $this->assertSame(array(42, 'foo'), $request->getParams());
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
