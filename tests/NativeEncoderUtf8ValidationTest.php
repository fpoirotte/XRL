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

class NativeEncoderUTF8Validator extends \fpoirotte\XRL\NativeEncoder
{
    public static function isUTF8($text)
    {
        return parent::isUTF8($text);
    }
}

class NativeEncoderUtf8Validation extends \PHPUnit\Framework\TestCase
{
    public function utf8Provider()
    {
        // Most of those test vectors come from
        // https://www.cl.cam.ac.uk/~mgk25/ucs/examples/UTF-8-test.txt
        // by Markus Kuhn <http://www.cl.cam.ac.uk/~mgk25/> - CC BY 4.0
        //
        // Various vectors have been removed or edited due to restrictions
        // imposed by RFC 3629 on valid codepoints supported by UTF-8.
        return array(
            // 1 Correct UTF-8 text
            "1.1"       => array("", true),
            "1.2"       => array("regular ASCII", true),
            "1.3"       => array("\xCE\xBA\xE1\xBD\xB9\xCF\x83\xCE\xBC\xCE\xB5", true),

            // 2 Valid boundaries
            // RFC 3629 limited UTF-8 to the range U+0000 to U+10FFFF,
            // so the values had to be adapted here.
            // 2.1 First possible sequence for each length
            "2.1.1"     => array("\x00", true),             // U+000000
            "2.1.2"     => array("\xC2\x80", true),         // U+000080
            "2.1.3"     => array("\xE0\xA0\x80", true),     // U+000800
            "2.1.4"     => array("\xF0\x90\x80\x80", true), // U+010000 (private range)
            // 2.2 Last possible sequence for each length
            "2.2.1"     => array("\x7F", true),             // U+00007F
            "2.2.2"     => array("\xDF\xBF", true),         // U+0007FF
            "2.2.3"     => array("\xEF\xBF\xBF", null),     // U+00FFFF (non-character)
            "2.2.4"     => array("\xF4\x8F\xBF\xBF", null), // U+10FFFF (non-character)

            // 2.3 Other boundary conditions
            "2.3.1"     => array("\xED\x9F\xBF", true),
            "2.3.2"     => array("\xEE\x80\x80", null),     // U+00E000 (private range)
            "2.3.3"     => array("\xEF\xBF\xBD", true),
            "2.3.4"     => array("\xF4\x8F\xBF\xBF", null), // U+10FFFF (non-character)

            // 3.1 Unexpected continuation bytes
            "3.1.1"     => array("\x80", false),
            "3.1.2"     => array("\xBF", false),
            "3.1.3"     => array("\x80\xBF", false),
            "3.1.4"     => array("\x80\xBF\x80", false),
            "3.1.5"     => array("\x80\xBF\x80\xBF", false),
            "3.1.6"     => array("\x80\xBF\x80\xBF\x80", false),
            "3.1.7"     => array("\x80\xBF\x80\xBF\x80\xBF", false),
            "3.1.8"     => array("\x80\xBF\x80\xBF\x80\xBF\x80", false),

            // 3.1.9 All continuation bytes
            "3.1.9"     => array(
                "\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8A\x8B\x8C\x8D\x8E\x8F" .
                "\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9A\x9B\x9C\x9D\x9E\x9F" .
                "\xA0\xA1\xA2\xA3\xA4\xA5\xA6\xA7\xA8\xA9\xAA\xAB\xAC\xAD\xAE\xAF" .
                "\xB0\xB1\xB2\xB3\xB4\xB5\xB6\xB7\xB8\xB9\xBA\xBB\xBC\xBD\xBE\xBF",
                false
            ),

            // 3.2 Lonely start characters
            "3.2.1"     => array(
                "\xC0 \xC1 \xC2 \xC3 \xC4 \xC5 \xC6 \xC7 \xC8 \xC9 \xCA \xCB \xCC \xCD \xCE \xCF " .
                "\xD0 \xD1 \xD2 \xD3 \xD4 \xD5 \xD6 \xD7 \xD8 \xD9 \xDA \xDB \xDC \xDD \xDE \xDF ",
                false
            ),
            "3.2.2"     => array(
                "\xE0 \xE1 \xE2 \xE3 \xE4 \xE5 \xE6 \xE7 \xE8 \xE9 \xEA \xEB \xEC \xED \xEE \xEF ",
                false
            ),
            "3.2.3"     => array(
                "\xF0 \xF1 \xF2 \xF3 \xF4 \xF5 \xF6 \xF7 ",
                false
            ),
            "3.2.4"     => array(
                "\xF8 \xF9 \xFA \xFB ",
                false
            ),
            "3.2.5"     => array(
                "\xFC \xFD ",
                false
            ),

            // 3.3 Sequence with last continuation byte missing
            "3.3.1"     => array("\xC0", false),
            "3.3.2"     => array("\xE0\xA4", false),
            "3.3.3"     => array("\xF0\xA4\xA4", false),
            "3.3.4"     => array("\xF8\xA4\xA4\xA4", false),
            "3.3.5"     => array("\xFC\xA4\xA4\xA4\xA4", false),
            "3.3.6"     => array("\xDF", false),
            "3.3.7"     => array("\xEF\xBF", false),
            "3.3.8"     => array("\xF7\xBF\xBF", false),
            "3.3.9"     => array("\xFB\xBF\xBF\xBF", false),
            "3.3.10"    => array("\xFF\xBF\xBF\xBF\xBF", false),

            // 3.4 Concatenation of incomplete sequences
            "3.4"  => array(
                "\xC0\xE0\xA4\xF0\xA4\xA4\xF8\xA4\xA4\xA4\xFC\xA4\xA4\xA4\xA4" .
                "\xDF\xEF\xBF\xF7\xBF\xBF\xFB\xBF\xBF\xBF\xFF\xBF\xBF\xBF\xBF",
                false
            ),

            // 3.5 Impossible bytes
            "3.5.1"     => array("\xFE", false),
            "3.5.2"     => array("\xFF", false),
            "3.5.3"     => array("\xFE\xFE\xFF\xFF", false),

            // 4.1 Overlong ASCII sequences
            "4.1.1"     => array("\xC0\xAF", false),
            "4.1.2"     => array("\xE0\x80\xAF", false),
            "4.1.3"     => array("\xF0\x80\x80\xAF", false),
            "4.1.4"     => array("\xF8\x80\x80\x80\xAF", false),
            "4.1.5"     => array("\xFC\x80\x80\x80\x80\xAF", false),

            // 4.2 Maximum overlong sequences
            "4.2.1"     => array("\xC1\xBF", false),
            "4.2.2"     => array("\xE0\x9F\xBF", false),
            "4.2.3"     => array("\xF0\x8F\xBF\xBF", false),
            "4.2.4"     => array("\xF8\x87\xBF\xBF\xBF", false),
            "4.2.5"     => array("\xFC\x83\xBF\xBF\xBF\xBF", false),

            // 4.3 Overlong sequences for NUL character
            "4.3.1"     => array("\xC0\x80", false),
            "4.3.2"     => array("\xE0\x80\x80", false),
            "4.3.3"     => array("\xF0\x80\x80\x80", false),
            "4.3.4"     => array("\xF8\x80\x80\x80\x80", false),
            "4.3.5"     => array("\xFC\x80\x80\x80\x80\x80", false),

            // 5.1 Single UTF-16 surrogates
            "5.1.1"     => array("\xED\xA0\x80", false),
            "5.1.2"     => array("\xED\xAD\xBF", false),
            "5.1.3"     => array("\xED\xAE\x80", false),
            "5.1.4"     => array("\xED\xAF\xBF", false),
            "5.1.5"     => array("\xED\xB0\x80", false),
            "5.1.6"     => array("\xED\xBE\x80", false),
            "5.1.7"     => array("\xED\xBF\xBF", false),

            // 5.2 Paired UTF-16 surrogates
            "5.2.1"     => array("\xED\xA0\x80\xED\xB0\x80", false),
            "5.2.2"     => array("\xED\xA0\x80\xED\xBF\xBF", false),
            "5.2.3"     => array("\xED\xAD\xBF\xED\xB0\x80", false),
            "5.2.4"     => array("\xED\xAD\xBF\xED\xBF\xBF", false),
            "5.2.5"     => array("\xED\xAE\x80\xED\xB0\x80", false),
            "5.2.6"     => array("\xED\xAE\x80\xED\xBF\xBF", false),
            "5.2.7"     => array("\xED\xAF\xBF\xED\xB0\x80", false),
            "5.2.8"     => array("\xED\xAF\xBF\xED\xBF\xBF", false),

            // 5.3 Noncharacter code positions
            "5.3.1"     => array("\xEF\xBF\xBE", null),
            "5.3.2"     => array("\xEF\xBF\xBF", null),
            "5.3.3"     => array(
                "\xEF\xB7\x90" .
                "\xEF\xB7\x91" .
                "\xEF\xB7\x92" .
                "\xEF\xB7\x93" .
                "\xEF\xB7\x94" .
                "\xEF\xB7\x95" .
                "\xEF\xB7\x96" .
                "\xEF\xB7\x97" .
                "\xEF\xB7\x98" .
                "\xEF\xB7\x99" .
                "\xEF\xB7\x9A" .
                "\xEF\xB7\x9B" .
                "\xEF\xB7\x9C" .
                "\xEF\xB7\x9D" .
                "\xEF\xB7\x9E" .
                "\xEF\xB7\x9F" .
                "\xEF\xB7\xA0" .
                "\xEF\xB7\xA1" .
                "\xEF\xB7\xA2" .
                "\xEF\xB7\xA3" .
                "\xEF\xB7\xA4" .
                "\xEF\xB7\xA5" .
                "\xEF\xB7\xA6" .
                "\xEF\xB7\xA7" .
                "\xEF\xB7\xA8" .
                "\xEF\xB7\xA9" .
                "\xEF\xB7\xAA" .
                "\xEF\xB7\xAB" .
                "\xEF\xB7\xAC" .
                "\xEF\xB7\xAD" .
                "\xEF\xB7\xAE" .
                "\xEF\xB7\xAF",
                null
            ),
            "5.3.4"     => array(
                "\xF0\x9F\xBF\xBE" .
                "\xF0\x9F\xBF\xBF" .
                "\xF0\xAF\xBF\xBE" .
                "\xF0\xAF\xBF\xBF" .
                "\xF0\xBF\xBF\xBE" .
                "\xF0\xBF\xBF\xBF" .
                "\xF1\x8F\xBF\xBE" .
                "\xF1\x8F\xBF\xBF" .
                "\xF1\x9F\xBF\xBE" .
                "\xF1\x9F\xBF\xBF" .
                "\xF1\xAF\xBF\xBE" .
                "\xF1\xAF\xBF\xBF" .
                "\xF1\xBF\xBF\xBE" .
                "\xF1\xBF\xBF\xBF" .
                "\xF2\x8F\xBF\xBE" .
                "\xF2\x8F\xBF\xBF" .
                "\xF2\x9F\xBF\xBE" .
                "\xF2\x9F\xBF\xBF" .
                "\xF4\x8F\xBF\xBE" .
                "\xF4\x8F\xBF\xBF",
                null
            ),
        );
    }

    /**
     * @covers          \fpoirotte\XRL\NativeEncoder::isUTF8
     * @dataProvider    utf8Provider
     */
    public function testUtf8Validation($text, $valid)
    {
        $this->assertSame($valid, NativeEncoderUTF8Validator::isUTF8($text));
    }
}
