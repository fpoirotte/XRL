<?php
// © copyright XRL Team, 2012. All rights reserved.
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * \brief
 *      An XML-RPC encoder that can produce either
 *      compact documents or pretty documents.
 */
class       XRL_Encoder
implements  XRL_EncoderInterface
{
    /// Whether the output should be indented (\c TRUE) or not (\c FALSE).
    protected $_indent;

    /// Whether the "\<string\>" tag should be used (\c TRUE) or not (\c FALSE).
    protected $_stringTag;

    /// Timezone used to encode date/times.
    protected $_timezone;

    /**
     * Create a new XML-RPC encoder.
     *
     * \param DateTimeZone $timezone
     *      Information on the timezone for which
     *      date/times should be encoded.
     *
     * \param bool $indent
     *      Whether the XML produced should be indented (\c TRUE)
     *      or not (\c FALSE).
     *
     * \param bool $stringTag
     *      Whether strings should be encoded using the \<string\>
     *      tag (\c TRUE) or using the defaut type (\c FALSE).
     *
     * \throw InvalidArgumentException
     *      An invalid value was passed for either the \c $indent
     *      or \c $stringTag argument.
     */
    public function __construct(
        DateTimeZone    $timezone,
                        $indent     = FALSE,
                        $stringTag  = FALSE
    )
    {
        if (!is_bool($indent))
            throw new InvalidArgumentException('$indent must be a boolean');
        if (!is_bool($stringTag))
            throw new InvalidArgumentException('$stringTag must be a boolean');

        $this->_indent      = $indent;
        $this->_stringTag   = $stringTag;
        $this->_timezone    = $timezone;
    }

    /**
     * Return an XML writer that will be used
     * to produce XML-RPC requests and responses.
     *
     * \retval XMLWriter
     *      XML writer to use to produce documents.
     */
    protected function _getWriter()
    {
        $writer = new XMLWriter();
        $writer->openMemory();
        if ($this->_indent) {
            $writer->setIndent(TRUE);
            $writer->startDocument('1.0', 'UTF-8');
        }
        else {
            $writer->setIndent(FALSE);
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
     *      \c TRUE if the $text contains a valid UTF-8 sequence,
     *      \c FALSE otherwise.
     */
    static protected function _isUTF8($text)
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
            )*$%SDxs', $text
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
    protected function _writeValue(XMLWriter $writer, $value)
    {
        // Support for the <nil> extension
        // (http://ontosys.com/xml-rpc/extensions.php)
        if (is_null($value))
            return $writer->writeElement('nil');

        if (is_int($value))
            return $writer->writeElement('int', $value);

        if (is_bool($value))
            return $writer->writeElement('boolean', (int) $value);

        if (is_string($value)) {
            // Encode as a regular string if possible.
            if (self::_isUTF8($value)) {
                // Use a <string> tag if this is what should be done.
                if ($this->_stringTag)
                    return $writer->writeElement('string', $value);
                // Otherwise, rely on "string" being the default type.
                return $writer->text($value);
            }
            // Otherwise, use a base64-encoded string.
            return $writer->writeElement('base64', base64_encode($value));
        }

        if (is_double($value))
            return $writer->writeElement('double', $value);

        if (is_array($value)) {
            $keys       = array_keys($value);
            $length     = count($value);

            // Empty arrays must be handled with care.
            if (!$length)
                $numeric = array();
            else {
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
                    $this->_writeValue($writer, $val);
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
                $this->_writeValue($writer, $val);
                $writer->endElement();
            }
            $writer->endElement();
            $writer->endElement();
            return;
        }

        if (!is_object($value))
            throw new InvalidArgumentException('Unsupported type');

        if ($value instanceof DateTime) {
            // PHP has serious issues with timezone handling.
            // Also, DateTime::getTimestamp() only exists since PHP 5.3.0.
            // As a workaround, we use format(), specifying a UNIX timestamp
            // as the format to use, which we then reinject in a new DateTime.
            $value = new DateTime('@'.$value->format('U'), $this->_timezone);
            $value = $value->format('Y-m-d\\TH:i:s');
            return $writer->writeElement('dateTime.iso8601', $value);
        }

        if (($value instanceof Serializable) ||
            method_exists($value, '__sleep'))
            return $this->_writeValue($writer, serialize($value));

        throw new InvalidArgumentException('Could not serialize object');
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
    protected function _finalizeWrite(XMLWriter $writer)
    {
        $writer->endDocument();
        $result = $writer->outputMemory(TRUE);

        if (!$this->_indent) {
            // Remove the XML declaration for an even
            // more compact result.
            if (!strncmp($result, '<'.'?xml', 5)) {
                $pos    = strpos($result, '?'.'>');
                if ($pos !== FALSE)
                    $result = (string) substr($result, $pos + 2);
            }
            // Remove leading & trailing whitespace.
            $result = trim($result);
        }

        return $result;
    }

    /// \copydoc XRL_EncoderInterface::encodeRequest()
    public function encodeRequest(XRL_Request $request)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodCall');
        $writer->writeElement('methodName', $request->getProcedure());
        if (count($request->getParams())) {
            $writer->startElement('params');
            foreach ($request->getParams() as $param) {
                $writer->startElement('param');
                $writer->startElement('value');
                $this->_writeValue($writer, $param);
                $writer->endElement();
                $writer->endElement();
            }
            $writer->endElement();
        }
        $writer->endElement();
        $result = $this->_finalizeWrite($writer);
        return $result;
    }

    /// \copydoc XRL_EncoderInterface::encodeError()
    public function encodeError(Exception $error)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('fault');
        $writer->startElement('value');
        $this->_writeValue(
            $writer,
            array(
                'faultCode'     => $error->getCode(),
                'faultString'   => get_class($error).': '.$error->getMessage(),
            )
        );
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->_finalizeWrite($writer);
        return $result;
    }

    /// \copydoc XRL_EncoderInterface::encodeResponse()
    public function encodeResponse($response)
    {
        $writer = $this->_getWriter();
        $writer->startElement('methodResponse');
        $writer->startElement('params');
        $writer->startElement('param');
        $writer->startElement('value');
        $this->_writeValue($writer, $response);
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $result = $this->_finalizeWrite($writer);
        return $result;
    }
}

