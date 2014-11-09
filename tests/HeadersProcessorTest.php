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

class HeadersProcessor extends \PHPUnit_Framework_TestCase
{
    protected $processor;

    public function setUp()
    {
        $this->processor = new \fpoirotte\XRL\HeadersProcessor();
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testNoContentType()
    {
        $headers    = array(
            'HTTP/1.1 200 OK',
            'Connection: closed',
        );
        $expected   = array('type' => null, 'params' => array());
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testSimpleContentType()
    {
        $headers    = array(
            'HTTP/1.1 200 OK',
            'Content-Type: text/plain',
        );
        $expected   = array('type' => 'text/plain', 'params' => array());
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testOverwrites()
    {
        $headers    = array(
            'Content-Type: text/plain; charset=utf-8',
            'Content-Type: text/html',
        );
        $expected   = array('type' => 'text/html', 'params' => array());
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testOverwrites2()
    {
        $headers    = array(
            'Content-Type: text/plain; charset=utf-8',
            'Content-Type: text/html; charset=us-ascii',
        );
        $expected   = array(
            'type'      => 'text/html',
            'params'    => array('charset' => 'us-ascii'),
        );
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testContentTypeWithCharset()
    {
        $headers    = array('Content-Type: text/plain; charset=utf-8');
        $expected   = array(
            'type' => 'text/plain',
            'params' => array('charset' => 'utf-8'),
        );
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testCaseSensitivity()
    {
        $headers    = array('CONTENT-type: text/plain; ChArSeT=utf-8');
        $expected   = array(
            'type' => 'text/plain',
            'params' => array('charset' => 'utf-8'),
        );
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testMultipleParameters()
    {
        $headers    = array('Content-Type: text/plain; charset=utf-8; foo=bar');
        $expected   = array(
            'type' => 'text/plain',
            'params' => array('charset' => 'utf-8', 'foo' => 'bar'),
        );
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testQuotedString()
    {
        $headers    = array('Content-Type: text/plain; charset="utf-8"');
        $expected   = array(
            'type' => 'text/plain',
            'params' => array('charset' => 'utf-8'),
        );
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testComments()
    {
        $headers    = array('Content-Type: text/plain; charset=us-ascii (Plain text)');
        $expected   = array(
            'type' => 'text/plain',
            'params' => array('charset' => 'us-ascii'),
        );
        $this->assertSame($expected, $this->processor->process($headers));
    }

    /// @covers \fpoirotte\XRL\HeadersProcessor::process
    public function testComments2()
    {
        $headers    = array('Content-Type: text/plain; foo=bar ((nested) comments)');
        $expected   = array(
            'type' => 'text/plain',
            'params' => array('foo' => 'bar'),
        );
        $this->assertSame($expected, $this->processor->process($headers));
    }

    public function corruptDataProvider()
    {
        $inputs = array(
            // EOL in escape sequence.
            'Content-Type: text/plain; foo="\\',
            // Unescaped carriage return.
            'Content-Type: text/plain; foo="'."\r",
            // Unterminated quoted-string.
            'Content-Type: text/plain; foo="',
            // Unterminated comment.
            'Content-Type: text/plain; foo=bar (comment',
            // Unterminated nested comment.
            'Content-Type: text/plain; foo=bar ((nested comment)',
        );
        return array($inputs);
    }

    /**
     * @dataProvider corruptDataProvider
     * @covers \fpoirotte\XRL\HeadersProcessor::process
     */
    public function testCorruptHeader($header)
    {
        $headers    = array($header);
        $expected   = array('type' => null, 'params' => array());
        $this->assertSame($expected, $this->processor->process($headers));
    }
}
