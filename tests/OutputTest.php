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

class Output extends \PHPUnit\Framework\TestCase
{
    protected $stream;

    public function setUp(): void
    {
        $this->stream = fopen('php://temp', 'w+');
    }

    public function tearDown(): void
    {
        fclose($this->stream);
    }

    /**
     * @covers \fpoirotte\XRL\Output::__construct
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    Not a valid stream
     */
    public function testConstructor()
    {
        $output = new \fpoirotte\XRL\Output(null);
    }

    /**
     * @covers \fpoirotte\XRL\Output::__construct
     * @covers \fpoirotte\XRL\Output::write
     */
    public function testWrite()
    {
        $output = new \fpoirotte\XRL\Output($this->stream);
        $output->write('Format attack... %s');
        $output->write('Not a format attack... %s', 'really');
        rewind($this->stream);
        $expected = 'Format attack... %s' . PHP_EOL .
                    'Not a format attack... really' . PHP_EOL;
        $this->assertSame($expected, stream_get_contents($this->stream));
    }
}
