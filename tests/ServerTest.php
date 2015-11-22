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

class Server extends \PHPUnit_Framework_TestCase
{
    protected $server;
    protected $cls;

    public function setUp()
    {
        $this->server   = new \fpoirotte\XRL\Server();
        $this->cls      = '\\fpoirotte\\XRL\\tests\\stub\\TestServer';
        $this->server->expose($this->cls);
    }

    /**
     * @covers \fpoirotte\XRL\Server
     */
    public function testCountProcedures()
    {
        $count = count(get_class_methods($this->cls));
        $this->assertEquals($count, count($this->server));
    }
}
