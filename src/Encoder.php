<?php
/**
 * \file
 *
 * Copyright (c) 2012, XRL Team
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace fpoirotte\XRL;

/**
 * \brief
 *      An XML-RPC encoder that can produce either
 *      compact documents or pretty documents.
 */
class Encoder implements \fpoirotte\XRL\EncoderInterface
{
    /// Whether the output should be indented (\c true) or not (\c false).
    protected $indent;

    /// Whether the "\<string\>" tag should be used (\c true) or not (\c false).
    protected $stringTag;

    /// Timezone used to encode date/times.
    protected $timezone;

    /**
     * Create a new XML-RPC encoder.
     *
     * \param DateTimeZone $timezone
     *      Information on the timezone for which
     *      date/times should be encoded.
     *
     * \param bool $indent
     *      Whether the XML produced should be indented (\c true)
     *      or not (\c false).
     *
     * \param bool $stringTag
     *      Whether strings should be encoded using the \<string\>
     *      tag (\c true) or using the defaut type (\c false).
     *
     * \throw InvalidArgumentException
     *      An invalid value was passed for either the \c $indent
     *      or \c $stringTag argument.
     */
    public function __construct(
        \DateTimeZone $timezone,
        $indent = false,
        $stringTag = false
    ) {
        if (!is_bool($indent)) {
            throw new \InvalidArgumentException('$indent must be a boolean');
        }
        if (!is_bool($stringTag)) {
            throw new \InvalidArgumentException('$stringTag must be a boolean');
        }

        $this->indent       = $indent;
        $this->stringTag    = $stringTag;
        $this->timezone     = $timezone;
    }

    /**
     * Return an XML writer that will be used
     * to produce XML-RPC requests and responses.
     *
     * \retval XMLWriter
     *      XML writer to use to produce documents.
     */
    protected function getWriter()
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        if ($this->indent) {
            $writer->setIndent(true);
            $writer->startDocument('1.0', 'UTF-8');
        } else {
            $writer->setIndent(false);
            $writer->startDocument();
        }
        return $writer;
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
     * Encode the given PHP value as an XML-RPC one
     * and write the result into a buffer.
     *
     * \param XMLWriter $writer
     *      A writer object that acts as a buffer and
     *      where the encoded value will be written.
     *
     * \param mixed $value
     *      The PHP value to write.
     *
     * \return
     *      The value returned by this methid is meaningless.
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
     *      -   NULL (encoded using the "nil" XML-RPC type).
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
    protected function writeValue(\XMLWriter $writer, $value)
    {
        // Support for the <nil> extension
        // (http://ontosys.com/xml-rpc/extensions.php)
        if (is_null($value)) {
            return $writer->writeElement('nil');
        }

        if (is_int($value)) {
            return $writer->writeElement('int', $value);
        }

        if (is_bool($value)) {
            return $writer->writeElement('boolean', (int) $value);
        }

        if (is_string($value)) {
            // Encode as a regular string if possible.
            if (self::isUTF8($value)) {
                // Use a <string> tag if this is what should be done.
                if ($this->stringTag) {
                    return $writer->writeElement('string', $value);
                }
                // Otherwise, rely on "string" being the default type.
                return $writer->text($value);
            }
            // Otherwise, use a base64-encoded string.
            return $writer->writeElement('base64', base64_encode($value));
        }

        if (is_double($value)) {
            return $writer->writeElement('double', $value);
        }

        if (is_array($value)) {
            $keys       = array_keys($value);
            $length     = count($value);

            // Empty arrays must be handled with care.
            if (!$length) {
                $numeric = array();
            } else {
                $numeric = range(0, $length - 1);
                sort($keys);
            }

            // Hash / associative array.
            if ($keys != $numeric) {
                $writer->startElement('struct');
                foreach ($value as $key => $val) {
                    $writer->startElement('member');
                    $writer->startElement('name');
                    $writer->text((string) $key);
                    $writer->endElement();

                    $writer->startElement('value');
                    $this->writeValue($writer, $val);
                    $writer->endElement();
                    $writer->endElement();
                }
                $writer->endElement();
                return;
            }

            // List / numerically-indexed array.
            $writer->startElement('array');
            $writer->startElement('data');
            foreach ($value as $val) {
                $writer->startElement('value');
                $this->writeValue($writer, $val);
                $writer->endElement();
            }
            $writer->endElement();
            $writer->endElement();
            return;
        }

        if (!is_object($value)) {
            throw new \InvalidArgumentException('Unsupported type');
        }

        if ($value instanceof \DateTime) {
            // PHP has serious issues with timezone handling.
            // Also, DateTime::getTimestamp() only exists since PHP 5.3.0.
            // As a workaround, we use format(), specifying a UNIX timestamp
            // as the format to use, which we then reinject in a new DateTime.
            $value = new \DateTime('@'.$value->format('U'), $this->timezone);
            $value = $value->format('Y-m-d\\TH:i:s');
            return $writer->writeElement('dateTime.iso8601', $value);
        }

        if (($value instanceof \Serializable) ||
            method_exists($value, '__sleep')) {
            return $this->writeValue($writer, serialize($value));
        }

        throw new \InvalidArgumentException('Could not serialize object');
    }

    /**
     * This method must be called when the document
     * is complete and returns the document.
     *
     * \param XMLWriter $writer
     *      XML writer used to produce the document.
     *
     * \retval string
     *      The XML document that was generated,
     *      as serialized XML.
     */
    protected function finalizeWrite(\XMLWriter $writer)
    {
        $writer->endDocument();
        $result = $writer->outputMemory(true);

        if (!$this->indent) {
            // Remove the XML declaration for an even
            // more compact result.
            if (!strncmp($result, '<'.'?xml', 5)) {
                $pos    = strpos($result, '?'.'>');
                if ($pos !== false) {
                    $result = (string) substr($result, $pos + 2);
                }
            }
            // Remove leading & trailing whitespace.
            $result = trim($result);
        }

        return $result;
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeRequest()
    public function encodeRequest(\fpoirotte\XRL\Request $request)
    {
        $writer = $this->getWriter();
        $writer->startElement('methodCall');
        $writer->writeElement('methodName', $request->getProcedure());
        if (count($request->getParams())) {
            $writer->startElement('params');
            foreach ($request->getParams() as $param) {
                $writer->startElement('param');
                $writer->startElement('value');
                $this->writeValue($writer, $param);
                $writer->endElement();
                $writer->endElement();
            }
            $writer->endElement();
        }
        $writer->endElement();
        $result = $this->finalizeWrite($writer);
        return $result;
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeError()
    public function encodeError(\Exception $error)
    {
        $writer = $this->getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('fault');
        $writer->startElement('value');
        $this->writeValue(
            $writer,
            array(
                'faultCode'     => $error->getCode(),
                'faultString'   => get_class($error).': '.$error->getMessage(),
            )
        );
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->finalizeWrite($writer);
        return $result;
    }

    /// \copydoc fpoirotte::XRL::EncoderInterface::encodeResponse()
    public function encodeResponse($response)
    {
        $writer = $this->getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('params');
        $writer->startElement('param');
        $writer->startElement('value');
        $this->writeValue($writer, $response);
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->finalizeWrite($writer);
        return $result;
    }
}
