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

class Node extends \PHPUnit\Framework\TestCase
{
    protected $reader;
    protected $data;

    public function setUp(): void
    {
        $this->reader = new \XMLReader();
        $this->data   = 'data://;base64,' .
            base64_encode(
                '<?xml version="1.0" encoding="UTF-8"?'.'>' .
                '<root><foo xmlns="http://example.com"/>' .
                '<bar></bar><baz>foo</baz></root>'
            );
        $this->reader->open($this->data, null, LIBXML_NONET | LIBXML_NOENT);
    }

    public function tearDown(): void
    {
        $this->reader->close();
    }

    /**
     * @covers      \fpoirotte\XRL\Node::emptyNodeExpansionWorked
     */
    public function testEmptyNodeExpansion()
    {
        // <root> (not empty)
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->emptyNodeExpansionWorked());
        // <foo/> (empty; expansion needed)
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertTrue($node->emptyNodeExpansionWorked());
        // <bar></bar> (empty; no expansion needed)
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->emptyNodeExpansionWorked());
        // <baz>...</baz> (not empty)
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->emptyNodeExpansionWorked());
    }

    /**
     * @covers                      \fpoirotte\XRL\Node
     * @expectedException           \UnexpectedValueException
     * @expectedExceptionMessage    Unknown property 'invalidProperty'
     */
    public function testInvalidProperties()
    {
        $node   = new \fpoirotte\XRL\Node($this->reader, false, true);
        $dummy  = $node->invalidProperty;
    }

    /**
     * @covers \fpoirotte\XRL\Node
     */
    public function testValidProperties()
    {
        // <root>
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->isEmptyElement);
        $this->assertSame('root',                       $node->localName);
        $this->assertSame('root',                       $node->name);
        $this->assertSame('',                           $node->namespaceURI);
        $this->assertSame(\XMLReader::ELEMENT,          $node->nodeType);
        $this->assertSame('',                           $node->value);

        // <foo/>
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertTrue($node->isEmptyElement);
        $this->assertSame('foo',                        $node->localName);
        $this->assertSame('{http://example.com}foo',    $node->name);
        $this->assertSame('http://example.com',         $node->namespaceURI);
        $this->assertSame(\XMLReader::ELEMENT,          $node->nodeType);
        $this->assertSame('',                           $node->value);

        // <bar>
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->isEmptyElement);
        $this->assertSame('bar',                        $node->localName);
        $this->assertSame('bar',                        $node->name);
        $this->assertSame('',                           $node->namespaceURI);
        $this->assertSame(\XMLReader::ELEMENT,          $node->nodeType);
        $this->assertSame('',                           $node->value);

        // </bar>
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->isEmptyElement);
        $this->assertSame('bar',                        $node->localName);
        $this->assertSame('bar',                        $node->name);
        $this->assertSame('',                           $node->namespaceURI);
        $this->assertSame(\XMLReader::END_ELEMENT,      $node->nodeType);
        $this->assertSame('',                           $node->value);

        // <baz>
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->isEmptyElement);
        $this->assertSame('baz',                        $node->localName);
        $this->assertSame('baz',                        $node->name);
        $this->assertSame('',                           $node->namespaceURI);
        $this->assertSame(\XMLReader::ELEMENT,          $node->nodeType);
        $this->assertSame('',                           $node->value);

        // ...
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->isEmptyElement);
        $this->assertSame('#text',                      $node->localName);
        $this->assertSame('#text',                      $node->name);
        $this->assertSame('',                           $node->namespaceURI);
        $this->assertSame(\XMLReader::TEXT,             $node->nodeType);
        $this->assertSame('foo',                        $node->value);

        // </baz>
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->isEmptyElement);
        $this->assertSame('baz',                        $node->localName);
        $this->assertSame('baz',                        $node->name);
        $this->assertSame('',                           $node->namespaceURI);
        $this->assertSame(\XMLReader::END_ELEMENT,      $node->nodeType);
        $this->assertSame('',                           $node->value);

        // </root>
        $node = new \fpoirotte\XRL\Node($this->reader, false, true);
        $this->assertFalse($node->isEmptyElement);
        $this->assertSame('root',                       $node->localName);
        $this->assertSame('root',                       $node->name);
        $this->assertSame('',                           $node->namespaceURI);
        $this->assertSame(\XMLReader::END_ELEMENT,      $node->nodeType);
        $this->assertSame('',                           $node->value);
    }

    /**
     * @covers                      \fpoirotte\XRL\Node::__construct
     * @expectedException           \InvalidArgumentException
     * @expectedExceptionMessage    End of document
     */
    public function testConstructor()
    {
        $this->reader->close();
        $this->reader->XML(
            '<?xml version="1.0" encoding="UTF-8"?'.'><root/>',
            null,
            LIBXML_NONET | LIBXML_NOENT
        );
        $dummy = new \fpoirotte\XRL\Node($this->reader, false, true);
        // Try to read past End Of Document.
        $dummy = new \fpoirotte\XRL\Node($this->reader, false, true);
    }

    /**
     * @covers                      \fpoirotte\XRL\Node::__construct
     * @expectedException           \fpoirotte\XRL\Exception
     * @expectedExceptionMessage    parse error. unsupported encoding
     */
    public function testConstructor2()
    {
        $this->reader->close();
        // Unsupported encoding.
        $this->reader->XML(
            '<?xml version="1.0" encoding="XXX"?'.'><root/>',
            null,
            LIBXML_NONET | LIBXML_NOENT
        );
        $dummy = new \fpoirotte\XRL\Node($this->reader, false, true);
    }

    /**
     * @covers                      \fpoirotte\XRL\Node::__construct
     * @expectedException           \fpoirotte\XRL\Exception
     * @expectedExceptionMessage    parse error. not well formed
     */
    public function testConstructor3()
    {
        $this->reader->close();
        // Not well-formed.
        $this->reader->XML(
            '<?xml version="1.0" encoding="UTF-8"?'.'><>',
            null,
            LIBXML_NONET | LIBXML_NOENT
        );
        $dummy = new \fpoirotte\XRL\Node($this->reader, false, true);
    }

    /**
     * @covers                      \fpoirotte\XRL\Node::__construct
     * @expectedException           \fpoirotte\XRL\Exception
     * @expectedExceptionMessage    server error. invalid xml-rpc. not conforming to spec
     */
    public function testConstructor4()
    {
        $this->reader->close();
        // Invalid XML-RPC.
        $this->reader->XML(
            '<?xml version="1.0" encoding="UTF-8"?'.'><root/>',
            null,
            LIBXML_NONET | LIBXML_NOENT
        );
        $dummy = new \fpoirotte\XRL\Node($this->reader, true, true);
    }
}
