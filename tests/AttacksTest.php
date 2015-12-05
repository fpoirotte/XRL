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

class Attacks extends \PHPUnit_Framework_TestCase
{
    //
    // Server
    //

    /**
     * This test is a control to ensure that the standard
     * entities "&lt;", "&gt;", "&amp;", "&apos;" & "&quot;"
     * are still decoded properly by the server.
     */
    public function testServerStandardEntityProcessing()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'server' .
            DIRECTORY_SEPARATOR . 'valid.xml'
        );

        $decoder = new \fpoirotte\XRL\Decoder(
            new \DateTimeZone("Europe/Dublin"),
            true
        );

        $request = $decoder->decodeRequest(
            'data://;base64,' . base64_encode($content)
        );
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Request', $request);

        $params = $request->getParams();
        $this->assertSame('<>', $request->getProcedure());
    }

    /**
     * Launch an XML External Entity (XXE) attack against the server.
     * Such attacks are used to reveal the contents of sensitive files.
     * See https://www.owasp.org/index.php/XML_External_Entity_%28XXE%29_Processing
     * for more information.
     *
     * Technically, the document is still well-formed (and in this case,
     * it is actually a valid XML-RPC request), but the decoder should
     * reject external entities to avoid this attack.
     *
     * @expectedException           \fpoirotte\XRL\Faults\NotWellFormedException
     * @expectedExceptionMessage    parse error. not well formed
     */
    public function testServerXmlExternalEntityProcessing()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'server' .
            DIRECTORY_SEPARATOR . 'xxe.xml'
        );

        chdir(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'server'
        );

        $decoder = new \fpoirotte\XRL\Decoder();
        $response = $decoder->decodeRequest(
            'data://;base64,' . base64_encode($content)
        );
    }

    /**
     * Launch an XML bomb against the server.
     * Such an attack can be used to cause a Denial of Service
     * by consuming all available resources on the machine.
     *
     * In this case, we try to allocate a bit less than 30 GB
     * of memory in the XML parser, which should be enough to
     * consume all available memory in case the attack works.
     * See https://en.wikipedia.org/wiki/Billion_laughs for more information.
     *
     * Technically, the document is still well-formed, but the decoder
     * should reject internal entities to avoid this attack.
     * Only the 5 default entities may be used.
     *
     * @expectedException           \fpoirotte\XRL\Faults\NotWellFormedException
     * @expectedExceptionMessage    parse error. not well formed
     */
    public function testServerXmlBomb()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'server' .
            DIRECTORY_SEPARATOR . 'bomb.xml'
        );

        $decoder = new \fpoirotte\XRL\Decoder();
        $response = $decoder->decodeRequest(
            'data://;base64,' . base64_encode($content)
        );
    }


    //
    // Client
    //

    /**
     * This test is a control to ensure that the standard
     * entities "&lt;", "&gt;", "&amp;", "&apos;" & "&quot;"
     * are still decoded properly by the client.
     */
    public function testClientStandardEntityProcessing()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'client' .
            DIRECTORY_SEPARATOR . 'valid.xml'
        );

        $decoder = new \fpoirotte\XRL\Decoder(
            new \DateTimeZone("Europe/Dublin"),
            true
        );

        $response = $decoder->decodeResponse(
            'data://;base64,' . base64_encode($content)
        );
        $this->assertInstanceOf('\\fpoirotte\\XRL\\Types\\StringType', $response);
        $this->assertSame('<> OK <>', $response->get());
    }

    /**
     * Launch an XML External Entity (XXE) attack against the client.
     *
     * @expectedException           \fpoirotte\XRL\Faults\NotWellFormedException
     * @expectedExceptionMessage    parse error. not well formed
     */
    public function testClientXmlExternalEntityProcessing()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'client' .
            DIRECTORY_SEPARATOR . 'xxe.xml'
        );

        chdir(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'client'
        );

        $decoder = new \fpoirotte\XRL\Decoder();
        $response = $decoder->decodeResponse(
            'data://;base64,' . base64_encode($content)
        );
    }

    /**
     * Launch an XML bomb against the client.
     *
     * @expectedException           \fpoirotte\XRL\Faults\NotWellFormedException
     * @expectedExceptionMessage    parse error. not well formed
     */
    public function testClientXmlBomb()
    {
        $content = file_get_contents(
            __DIR__ .
            DIRECTORY_SEPARATOR . 'testdata' .
            DIRECTORY_SEPARATOR . 'attacks' .
            DIRECTORY_SEPARATOR . 'client' .
            DIRECTORY_SEPARATOR . 'bomb.xml'
        );

        $decoder = new \fpoirotte\XRL\Decoder();
        $response = $decoder->decodeResponse(
            'data://;base64,' . base64_encode($content)
        );
    }
}
