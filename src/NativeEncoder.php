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
     * Can be used to determine if a string contains a sequence
     * of valid UTF-8 encoded codepoints.
     *
     * \param string $text
     *      Some text to test for UTF-8 correctness.
     *
     * \retval bool
     *      \c true if the $text contains a valid UTF-8 sequence,
     *      \c false otherwise.
     */
    protected static function isUTF8($text)
    {
        // From http://w3.org/International/questions/qa-forms-utf-8.html
        // Pointed out by bitseeker on http://php.net/utf8_encode
        return (bool) preg_match(
            '%^(?:
                  [\x09\x0A\x0D\x20-\x7E]            # ASCII
                | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
                |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
                |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
                |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
                | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
                |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )*$%SDxs',
            $text
        );
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
     *      -   integer (encoded using the "int" XML-RPC type).
     *      -   boolean (encoded using the "boolean" XML-RPC type).
     *      -   string (encoded using either the default type
     *          of XML-RPC [string] or "base64" if the string
     *          contains invalid UTF-8 sequences, such as when
     *          encoding binary data).
     *      -   double (encoded using the "double" XML-RPC type).
     *      -   array (encoded using either the "array" or "struct"
     *          XML-RPC type). "array" is used for numerically-indexed
     *          arrays where the keys are [0..len(array)-1] (aka "list").
     *          "struct" is used for all other arrays (aka "hash").
     *      --  DateTime objects (encoded using the "dateTime.iso8601"
     *          XML-RPC type).
     *      -   objects that support serialization (encoded as an XML-RPC
     *          "string", where the content of the string is the object's
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
                // Encode as a regular string if possible.
                if (static::isUTF8($value)) {
                    return new \fpoirotte\XRL\Types\String($value);
                }
                return new \fpoirotte\XRL\Types\Base64($value);

            case 'array':
                $newValue = array_map("static::convert", $value);
                try {
                    return new \fpoirotte\XRL\Types\ArrayType($newValue);
                } catch (\InvalidArgumentException $e) {
                }
                return new \fpoirotte\XRL\Types\Struct($newValue);

            case 'object':
            case 'resource':
                // A special treatment is applied afterwards.
                break;
        }

        // Only objects remain after this points.
        if ($value instanceof \fpoirotte\XRL\Types\AbstractType) {
            return $value;
        }

        if ($value instanceof \GMP ||
            (is_resource($value) && get_resource_type($value) === 'GMP integer')) {
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
                    'faultCode'     => new \fpoirotte\XRL\Types\Int($value->getCode()),
                    'faultString'   => new \fpoirotte\XRL\Types\String(
                        get_class($value).': '.$value->getMessage()
                    ),
                )
            );
        }

        if (is_object($value) && (
            ($value instanceof \Serializable) ||
            method_exists($value, '__sleep'))) {
            $value = serialize($value);

            // Encode as a regular string if possible.
            if (static::isUTF8($value)) {
                return new \fpoirotte\XRL\Types\String($value);
            }
            return new \fpoirotte\XRL\Types\Base64($value);
        }

        throw new \InvalidArgumentException('Unconvertible type');
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
