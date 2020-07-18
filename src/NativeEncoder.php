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

namespace fpoirotte\XRL;

/**
 * \brief
 *      An XML-RPC encoder that transparently converts
 *      PHP types to their XML-RPC counterpart.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class NativeEncoder implements \fpoirotte\XRL\EncoderInterface
{
    /// Sub-encoder.
    protected $encoder;

    /**
     * Creates a new encoder.
     *
     * \param fpoirotte::XRL::EncoderInterface $encoder
     *      Sub-encoder to use.
     */
    public function __construct(\fpoirotte\XRL\EncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Determine if a string contains a valid sequence of UTF-8 encoded
     * Unicode codepoints.
     *
     * \param string $text
     *      Some text to test for UTF-8 validity.
     *
     * \retval bool
     *      \c true if the $text contains a valid UTF-8 sequence
     *      which does not make use of non-characters/reserved characters,
     *      \c null if it contains a valid UTF-8 containing
     *      non-characters/reserved characters, or \c false otherwise.
     */
    protected static function isUTF8($text)
    {
        /*
            Based on http://w3.org/International/questions/qa-forms-utf-8.html,
            but rewritten to avoid a dependency on PCRE.

            Also, the regular expression in the post above seems to limit
            valid inputs to printable characters (for compatibility reasons?).
            See also section 2.2 of http://www.w3.org/TR/xml11 and section 2.2
            of http://www.w3.org/TR/xml/ for a possible explanation.

            The code below does not suffer from such limitations.
        */
        $len = strlen($text);
        $res = true;
        for ($i = 0; $i < $len; $i++) {
            // U+0000 - U+007F
            $byte1 = ord($text[$i]);
            if ($byte1 >= 0 && $byte1 <= 0x7F) {
                continue;
            }

            // Look for 2nd byte
            if (++$i >= $len) {
                return false;
            }
            $byte2 = ord($text[$i]);

            // U+0080 - U-07FF
            if ($byte1 >= 0xC2 && $byte1 <= 0xDF) {
                if ($byte2 >= 0x80 && $byte2 <= 0xBF) {
                    continue;
                }
                return false;
            }

            // Look for 3nd byte
            if (++$i >= $len) {
                return false;
            }
            $byte3 = ord($text[$i]);

            // U+0800 - U+0FFF
            if ($byte1 == 0xE0) {
                if ($byte2 >= 0xA0 && $byte2 <= 0xBF &&
                    $byte3 >= 0x80 && $byte3 <= 0xBF) {
                    continue;
                }
                return false;
            }

            // U+1000 - U+CFFF & U+E000 - U+FFFF
            if ($byte1 >= 0xE1 && $byte1 <= 0xEF && $byte1 !== 0xED) {
                if ($byte2 >= 0x80 && $byte2 <= 0xBF &&
                    $byte3 >= 0x80 && $byte3 <= 0xBF) {
                    $codepoint = (($byte1 & 0x0F) << 12) + (($byte2 & 0x3F) << 6) + ($byte3 & 0x3F);
                    if (($codepoint >= 0xE000 && $codepoint <= 0xF8FF) ||   // Private range
                        ($codepoint >= 0xFDD0 && $codepoint <= 0xFDEF) ||   // Non-characters
                        $codepoint == 0xFFFE || $codepoint == 0xFFFF) {     // Non-characters
                        $res = null;
                    }
                    continue;
                }
                return false;
            }

            // U+D000 - U+D7FF
            if ($byte1 == 0xED) {
                if ($byte2 >= 0x80 && $byte2 <= 0x9F &&
                    $byte3 >= 0x80 && $byte3 <= 0xBF) {
                    continue;
                }
                return false;
            }

            // Look for 4nd byte
            if (++$i >= $len) {
                return false;
            }
            $byte4 = ord($text[$i]);

            // U+10000 - U+3FFFF
            if ($byte1 == 0xF0) {
                if ($byte2 >= 0x90 && $byte2 <= 0xBF &&
                    $byte3 >= 0x80 && $byte3 <= 0xBF &&
                    $byte4 >= 0x80 && $byte4 <= 0xBF) {
                    $codepoint = (($byte1 & 0x07) << 18) +
                                 (($byte2 & 0x3F) << 12) +
                                 (($byte3 & 0x3F) << 6) +
                                 ($byte4 & 0x3F);
                    //  Non-characters                                    Reserved range
                    if ($codepoint == 0x1FFFE || $codepoint == 0x1FFFF || $codepoint >= 0x2FFFE) {
                        $res = null;
                    }
                    continue;
                }
                return false;
            }

            // U+40000 - U+FFFFF
            if ($byte1 >= 0xF1 && $byte1 <= 0xF3) {
                if ($byte2 >= 0x80 && $byte2 <= 0xBF &&
                    $byte3 >= 0x80 && $byte3 <= 0xBF &&
                    $byte4 >= 0x80 && $byte4 <= 0xBF) {
                    $codepoint = (($byte1 & 0x07) << 18) +
                                 (($byte2 & 0x3F) << 12) +
                                 (($byte3 & 0x3F) << 6) +
                                 ($byte4 & 0x3F);
                    //  Reserved range           Non characters & private ranges
                    if ($codepoint < 0xE0000 || $codepoint >= 0xEFFFE) {
                        $res = null;
                    }
                    continue;
                }
                return false;
            }

            // U+100000 - U+10FFFF
            if ($byte1 == 0xF4) {
                if ($byte2 >= 0x80 && $byte2 <= 0x8F &&
                    $byte3 >= 0x80 && $byte3 <= 0xBF &&
                    $byte4 >= 0x80 && $byte4 <= 0xBF) {
                    // This part contains only non-characters & private ranges.
                    $res = null;
                    continue;
                }
                return false;
            }

            // Byte #1 contained an invalid value.
            return false;
        }

        // No decoding error detected, but the given input may contain
        // non-characters/reserved characters, depending on the value of $res.
        return $res;
    }

    protected static function isBinaryString($text)
    {
        if (!static::isUTF8($text)) {
            return true;
        }

        // Based on XML 1.1, section 2.2 (compatibility characters).
        // We're a bit more lax as long as the whole string forms
        // a valid UTF-8 codepoints sequence.
        $restrictedChars =
            "\x00\x01\x02\x03\x04\x05\x06\x07\x08" .
            "\x0B\x0C" .
            "\x0E\x0F\x10" .
            "\x11\x12\x13\x14\15\x16\x17\x18\x19\x1A\x1B\x1C\x1D\x1E\x1F" .
            "\x7F";

        // If the text contains restricted characters,
        // encode it as a binary string.
        if (strcspn($text, $restrictedChars) !== strlen($text)) {
            return true;
        }

        // Otherwise, it is probably safe to encode the string "as is".
        return false;
    }

    /**
     * Convert the given PHP value to an XML-RPC type.
     *
     * \param mixed $value
     *      The PHP value to convert.
     *
     * \retval fpoirotte::XRL::Types::AbstractType
     *      The value encapsulated in an XML-RPC type.
     *
     * \note
     *      This implementation supports the "nil" XML-RPC extension
     *      (http://ontosys.com/xml-rpc/extensions.php).
     *
     * \note
     *      An empty PHP array will always be encoded as an empty
     *      XML-RPC array.
     *
     * \note
     *      The following PHP types are currently supported:
     *      -   \c null (encoded using the "nil" XML-RPC type).
     *      -   integer (encoded using the "i4" or "i8" XML-RPC type,
     *          depending on the actual size required to store the value).
     *      -   boolean (encoded using the "boolean" XML-RPC type).
     *      -   string (encoded using either the default type
     *          of XML-RPC [string] or "base64" if the string
     *          contains invalid UTF-8 sequences, such as when
     *          encoding binary data).
     *      -   double (encoded using the "double" XML-RPC type).
     *      -   array (encoded using either the "array" or "struct"
     *          XML-RPC type):
     *          -   "array" is used for numerically-indexed arrays
     *              where the keys are in the range [0..len(array)-1],
     *              aka "lists".
     *          -   "struct" is used for all other arrays, aka "hashes".
     *      -   DateTime objects (encoded using the "dateTime.iso8601"
     *          XML-RPC type).
     *      -   GMP objects (encoded as "i4", "i8" or "BigInteger",
     *          depending on their actual storage size).
     *      -   XML objects (SimpleXML, DOM and XMLWriter objects)
     *          are encoded as XML DOM fragments.
     *      -   objects that support serialization (encoded as an XML-RPC
     *          "string" or "base64" type depending on the object's
     *          representation in serialized form).
     */
    public static function convert($value)
    {
        switch (gettype($value)) {
            case 'NULL':
                // Support for the <nil> extension
                // (http://ontosys.com/xml-rpc/extensions.php)
                return new \fpoirotte\XRL\Types\Nil(null);

            case 'boolean':
                return new \fpoirotte\XRL\Types\Boolean($value);

            case 'integer':
                try {
                    return new \fpoirotte\XRL\Types\I4($value);
                } catch (\InvalidArgumentException $e) {
                }
                return new \fpoirotte\XRL\Types\I8($value);

            case 'double':
                return new \fpoirotte\XRL\Types\Double($value);

            case 'string':
                // We try to encode it as a regular string if possible.
                if (static::isBinaryString($value)) {
                    return new \fpoirotte\XRL\Types\Base64($value);
                }
                return new \fpoirotte\XRL\Types\StringType($value);

            case 'array':
                $newValue = array_map("static::convert", $value);
                try {
                    return new \fpoirotte\XRL\Types\ArrayType($newValue);
                } catch (\InvalidArgumentException $e) {
                }
                return new \fpoirotte\XRL\Types\Struct($newValue);

            case 'resource':
                throw new \InvalidArgument('Cannot encode PHP resource as XML-RPC type');

            case 'object':
                // A special treatment is applied afterwards.
                break;
        }

        // Only objects & resources remain after this point.
        if ($value instanceof \fpoirotte\XRL\Types\AbstractType) {
            return $value;
        }

        if ($value instanceof \GMP) {
            $candidates = array(
                '\\fpoirotte\\XRL\\Types\\I4',
                '\\fpoirotte\\XRL\\Types\\I8',
                '\\fpoirotte\\XRL\\Types\\BigInteger',
            );
            foreach ($candidates as $candidate) {
                try {
                    return new $candidate($value);
                } catch (\InvalidArgumentException $e) {
                }
            }
        }

        if ($value instanceof \DateTime) {
            return new \fpoirotte\XRL\Types\DateTimeIso8601($value);
        }

        if (($value instanceof \DOMNode) ||
            ($value instanceof \XMLWriter) ||
            ($value instanceof \SimpleXMLElement)) {
            return new \fpoirotte\XRL\Types\Dom($value);
        }

        if ($value instanceof \Exception) {
            return new \fpoirotte\XRL\Types\Struct(
                array(
                    'faultCode'     => new \fpoirotte\XRL\Types\IntType($value->getCode()),
                    'faultString'   => new \fpoirotte\XRL\Types\StringType(
                        get_class($value).': '.$value->getMessage()
                    ),
                )
            );
        }

        if (is_object($value) && (
            ($value instanceof \Serializable) ||
            method_exists($value, '__sleep'))) {
            $value = serialize($value);

            // We try to encode it as a regular string if possible.
            if (static::isBinaryString($value)) {
                return new \fpoirotte\XRL\Types\Base64($value);
            }
            return new \fpoirotte\XRL\Types\StringType($value);
        }

        throw new \InvalidArgumentException('Cannot convert the given object to an XML-RPC type');
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeRequest()
    public function encodeRequest(\fpoirotte\XRL\Request $request)
    {
        $newParams = array_map('static::convert', $request->getParams());
        return $this->encoder->encodeRequest(
            new \fpoirotte\XRL\Request($request->getProcedure(), $newParams)
        );
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeError()
    public function encodeError(\Exception $error)
    {
        return $this->encoder->encodeError($error);
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeResponse()
    public function encodeResponse($response)
    {
        return $this->encoder->encodeResponse(static::convert($response));
    }
}
