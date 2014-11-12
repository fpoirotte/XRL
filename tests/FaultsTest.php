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

class Faults extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->server       = new \fpoirotte\XRL\Server();
        $this->server->foo  = function ($s) { return $s; };
    }

    /**
     * @covers      \fpoirotte\XRL\Faults
     */
    public function testIllFormed()
    {
        $res = $this->server->handle('data://,%2F');
        $expected =<<<EXPECTED
<methodResponse><fault><value><struct><member><name>faultCode</name><value>
<int>-32700</int></value></member><member><name>faultString</name><value>
<string>fpoirotte\XRL\Exception: parse error. not well formed</string></value>
</member></struct></value></fault></methodResponse>
EXPECTED;
        $this->assertSame(
            str_replace(array("\r", "\n"), '', $expected),
            (string) $res
        );
    }

    /**
     * @covers      \fpoirotte\XRL\Faults
     */
    public function testUnsupportedEncoding()
    {
        $res = $this->server->handle(
            'data://;base64,' .
            base64_encode(
                '<?xml version="1.0" encoding="Some-Invalid-Encoding"?><foo/>'
            )
        );
        $expected =<<<EXPECTED
<methodResponse><fault><value><struct><member><name>faultCode</name><value>
<int>-32701</int></value></member><member><name>faultString</name><value>
<string>fpoirotte\XRL\Exception: parse error. unsupported encoding</string>
</value></member></struct></value></fault></methodResponse>
EXPECTED;
        $this->assertSame(
            str_replace(array("\r", "\n"), '', $expected),
            (string) $res
        );
    }

    /**
     * @covers      \fpoirotte\XRL\Faults
     */
    public function testInvalidCharacter()
    {
        $res = $this->server->handle(
            'data://;base64,' .
            base64_encode(
                '<?xml version="1.0" encoding="UTF-8"?>' .
                "<foo>\xE8\xE9\xE0</foo>"
            )
        );
        $expected =<<<EXPECTED
<methodResponse><fault><value><struct><member><name>faultCode</name><value>
<int>-32700</int></value></member><member><name>faultString</name><value>
<string>fpoirotte\XRL\Exception: parse error. not well formed</string>
</value></member></struct></value></fault></methodResponse>
EXPECTED;
        $this->assertSame(
            str_replace(array("\r", "\n"), '', $expected),
            (string) $res
        );
    }

    /**
     * @covers      \fpoirotte\XRL\Faults
     */
    public function testInvalidXmlRpc()
    {
        $res = $this->server->handle(
            'data://;base64,' .
            base64_encode(
                '<?xml version="1.0" encoding="UTF-8"?><foo/>'
            )
        );
        $expected =<<<EXPECTED
<methodResponse><fault><value><struct><member><name>faultCode</name><value>
<int>-32600</int></value></member><member><name>faultString</name><value>
<string>fpoirotte\XRL\Exception: server error. invalid xml-rpc. not conforming
 to spec</string></value></member></struct></value></fault></methodResponse>
EXPECTED;
        $this->assertSame(
            str_replace(array("\r", "\n"), '', $expected),
            (string) $res
        );
    }

    /**
     * @covers      \fpoirotte\XRL\Faults
     */
    public function testMethodNotFound()
    {
        $res = $this->server->handle(
            'data://;base64,' .
            base64_encode(
                file_get_contents(
                    __DIR__ .
                    DIRECTORY_SEPARATOR . 'testdata' .
                    DIRECTORY_SEPARATOR . 'requests' .
                    DIRECTORY_SEPARATOR . 'empty.xml'
                )
            )
        );
        $expected =<<<EXPECTED
<methodResponse><fault><value><struct><member><name>faultCode</name><value>
<int>-32601</int></value></member><member><name>faultString</name><value>
<string>fpoirotte\XRL\Exception: server error. requested method not found
</string></value></member></struct></value></fault></methodResponse>
EXPECTED;
        $this->assertSame(
            str_replace(array("\r", "\n"), '', $expected),
            (string) $res
        );
    }

#    public function testInvalidParameters()
#    {
#        $res = $this->server->handle(
#            'data://;base64,' .
#            base64_encode(
#            )
#        );
#    }
}
