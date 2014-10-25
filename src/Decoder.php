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
 *      A decoder that can process XML-RPC requests
 *      and responses, with optional XML validation.
 */
class Decoder implements \fpoirotte\XRL\DecoderInterface
{
    /// Whether the documents should be validated or not.
    protected $validate;

    /// The fpoirotte::XRL::Node currently being processed.
    protected $currentNode;

    /// Timezone used to decode date/times.
    protected $timezone;

    /**
     * Creates a new decoder.
     *
     * \param DateTimeZone $timezone
     *      Information on the timezone incoming
     *      date/times come from.
     *
     * \param bool $validate
     *      Whether the decoder should validate
     *      its input (\c true) or not (\c false).
     *
     * \throw InvalidArgumentException
     *      The value passed for \c $validate was
     *      not a boolean.
     */
    public function __construct(\DateTimeZone $timezone, $validate = true)
    {
        if (!is_bool($validate)) {
            throw new \InvalidArgumentException('Not a boolean');
        }

        $this->validate    = $validate;
        $this->currentNode = null;
        $this->timezone    = $timezone;
    }

    /**
     * Returns an XML reader for some data.
     *
     * \param string $data
     *      XML data to process.
     *
     * \param bool $request
     *      Whether the data refers to an XML-RPC
     *      request (\c true) or a response (\c false).
     *
     * \retval XMLReader
     *      An XML reader for the given data.
     *
     * \throw InvalidArgumentException
     *      You tried to pass something that is not
     *      a boolean as the \c $request parameter.
     *
     * \note
     *      The reader is set to validate the document
     *      on the fly if that's what this decoder was
     *      configured to do during construction.
     */
    protected function getReader($data, $request)
    {
        if (!is_bool($request)) {
            throw new \InvalidArgumentException('Not a boolean');
        }

        $this->currentNode = null;
        $reader = new \XMLReader();
        $reader->xml($data, null, LIBXML_NONET | LIBXML_NOENT);
        if ($this->validate) {
            $schema = dirname(__DIR__) .
                DIRECTORY_SEPARATOR . 'data' .
                DIRECTORY_SEPARATOR;
            $schema .= $request ? 'request.rng' : 'response.rng';
            $reader->setRelaxNGSchema($schema);
        }
        return $reader;
    }

    /**
     * Read a node from the XML reader
     * and return it.
     *
     * \param XMLReader $reader
     *      Reader to read the node from.
     *
     * \retval fpoirotte::XRL::Node
     *      The XML node that's been read.
     */
    protected function readNode($reader)
    {
        if ($this->currentNode !== null) {
            return $this->currentNode;
        }

        $this->currentNode = new \fpoirotte\XRL\Node($reader, $this->validate);
        return $this->currentNode;
    }

    /**
     * Prepare for the next XML node read.
     * This method should be called after each
     * successful node parsing.
     */
    protected function prepareNextNode()
    {
        if (!$this->currentNode->emptyNodeExpansionWorked()) {
            $this->currentNode = null;
        }
    }

    /**
     * Read a node from the document and throw
     * an exception if it is not an opening tag
     * with the given name.
     *
     * \param XMLReader $reader
     *      Reader object the node will be read from.
     *
     * \param string $expectedTag
     *      Name of the tag we're expecting.
     *
     * \throw InvalidArgumentException
     *      Thrown whenever one of the following
     *      conditions is met:
     *      - We reached the end of the document.
     *      - The next node was not an opening tag.
     *      - The next node was an opening tag, but
     *        its name was not the one we expected.
     */
    protected function expectStartTag($reader, $expectedTag)
    {
        $node = $this->readNode($reader);

        $type = $node->nodeType;
        if ($type != \XMLReader::ELEMENT) {
            throw new \InvalidArgumentException(
                "Expected an opening $expectedTag tag ".
                "but got a node of type #$type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag != $expectedTag) {
            throw new \InvalidArgumentException(
                "Got opening tag for $readTag instead of $expectedTag"
            );
        }

        $this->prepareNextNode();
    }

    /**
     * Read a node from the document and throw
     * an exception if it is not a closing tag
     * with the given name.
     *
     * \param XMLReader $reader
     *      Reader object the node will be read from.
     *
     * \param string $expectedTag
     *      Name of the tag we're expecting.
     *
     * \throw InvalidArgumentException
     *      Thrown whenever one of the following
     *      conditions is met:
     *      - We reached the end of the document.
     *      - The next node was not a closing tag.
     *      - The next node was a closing tag, but
     *        its name was not the one we expected.
     */
    protected function expectEndTag($reader, $expectedTag)
    {
        $node = $this->readNode($reader);

        $type = $node->nodeType;
        if ($type != \XMLReader::END_ELEMENT) {
            throw new \InvalidArgumentException(
                "Expected a closing $expectedTag tag ".
                "but got a node of type #$type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag != $expectedTag) {
            throw new \InvalidArgumentException(
                "Got closing tag for $readTag instead of $expectedTag"
            );
        }

        $this->prepareNextNode();
    }

    /**
     * Read a node from the document and throw
     * an exception if it is not a text node.
     * Otherwise, return its content.
     *
     * \param XMLReader $reader
     *      Reader object the node will be read from.
     *
     * \retval string
     *      The value of the text node.
     *
     * \throw InvalidArgumentException
     *      Thrown whenever one of the following
     *      conditions is met:
     *      - We reached the end of the document.
     *      - The next node was not a text node.
     */
    protected function parseText($reader)
    {
        $node = $this->readNode($reader);

        $type = $node->nodeType;
        if ($type != \XMLReader::TEXT) {
            throw new \InvalidArgumentException(
                "Expected a text node, but got ".
                "a node of type #$type instead"
            );
        }

        $value              = $node->value;
        $this->prepareNextNode();
        return $value;
    }

    /**
     * Check the type of a value.
     *
     * \param array $allowedTypes
     *      Whitelist of allowed types.
     *      If empty, any type is allowed.
     *
     * \param string $type
     *      The actual type of the value
     *      being tested.
     *
     * \param mixed $value
     *      The value being tested.
     *
     * \retval mixed
     *      The original value that was passed
     *      to this method, if type allows.
     *
     * \throw InvalidArgumentException
     *      The given type cannot be used
     *      in this context (disallowed).
     */
    protected static function checkType(array $allowedTypes, $type, $value)
    {
        if (count($allowedTypes) && !in_array($type, $allowedTypes)) {
            $allowed = implode(', ', $allowedTypes);
            throw new \InvalidArgumentException(
                "Expected one of: $allowed, but got $type"
            );
        }

        return $value;
    }

    /**
     * Decodes a value encoded using XML-RPC types.
     *
     * \param XMLReader $reader
     *      Reader the value will be read from.
     *
     * \param array $allowedTypes
     *      Whitelist will the names of the types
     *      that are allowed in this context.
     *
     * \retval mixed
     *      The value that was decoded, if any,
     *      with the appropriate PHP type.
     *
     * \throw InvalidArgumentException
     *      No value could be decoded (probably because
     *      the input document was invalid) or the decoded
     *      value was of a type that is not allowed in this
     *      context.
     */
    protected function decodeValue($reader, array $allowedTypes = array())
    {
        // Support for the <nil> extension
        // (http://ontosys.com/xml-rpc/extensions.php)
        $error = null;
        try {
            $this->expectStartTag($reader, 'nil');
        } catch (\InvalidArgumentException $error) {
        }

        if (!$error) {
            $this->expectEndTag($reader, 'nil');
            return self::checkType($allowedTypes, 'nil', null);
        }

        // Other basic types.
        $types = array(
            'i4',
            'int',
            'boolean',
            'string',
            'double',
            'dateTime.iso8601',
            'base64',
        );

        foreach ($types as $type) {
            try {
                $this->expectStartTag($reader, $type);
            } catch (\InvalidArgumentException $e) {
                continue;
            }

            try {
                $value = $this->parseText($reader);
            } catch (\InvalidArgumentException $e) {
                // Both "string" & "base64" may refer
                // to an empty string.
                if ($type != 'string' && $type != 'base64') {
                    throw $e;
                }
                $value = '';
            }
            $this->expectEndTag($reader, $type);

            switch ($type) {
                case 'i4':
                    $type = 'int';
                    // fall-through as "i4" is an alias for "int".
                case 'int':
                    $value = (int) $value;
                    break;

                case 'boolean':
                    $value = (bool) $value;
                    break;

                case 'string':
                    break;

                case 'double':
                    $value = (double) $value;
                    break;

                case 'dateTime.iso8601':
                    $result = new \DateTime($value, $this->timezone);
                    if ($result->format('Y-m-d\\TH:i:s') != $value) {
                        throw new \InvalidArgumentException('Invalid date/time');
                    }
                    $value = $result;
                    break;

                case 'base64':
                    $value = base64_decode($value);
                    break;
            }

            return self::checkType($allowedTypes, $type, $value);
        }

        // Handle structures.
        $error = null;
        try {
            $this->expectStartTag($reader, 'struct');
        } catch (\InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = array();
            // Read values.
            while (true) {
                $error = null;
                try {
                    $this->expectStartTag($reader, 'member');
                } catch (\InvalidArgumentException $error) {
                }

                if ($error) {
                    break;
                }

                // Read key.
                $this->expectStartTag($reader, 'name');
                $key = $this->decodeValue($reader, array('string', 'int'));
                $this->expectEndTag($reader, 'name');

                $this->expectStartTag($reader, 'value');
                $value[$key] = $this->decodeValue($reader);
                $this->expectEndTag($reader, 'value');
                $this->expectEndTag($reader, 'member');
            }
            $this->expectEndTag($reader, 'struct');
            return self::checkType($allowedTypes, 'struct', $value);
        }

        // Handle arrays.
        $error = null;
        try {
            $this->expectStartTag($reader, 'array');
        } catch (\InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = array();
            $this->expectStartTag($reader, 'data');
            // Read values.
            while (true) {
                $error = null;
                try {
                    $this->expectStartTag($reader, 'value');
                } catch (\InvalidArgumentException $error) {
                }

                if ($error) {
                    break;
                }

                $value[] = $this->decodeValue($reader);
                $this->expectEndTag($reader, 'value');
            }
            $this->expectEndTag($reader, 'data');
            $this->expectEndTag($reader, 'array');
            return self::checkType($allowedTypes, 'array', $value);
        }

        // Default type (string).
        try {
            $value = $this->parseText($reader);
        } catch (\InvalidArgumentException $e) {
            $value = '';
        }
        return self::checkType($allowedTypes, 'string', $value);
    }

    /// \copydoc fpoirotte::XRL::DecoderInterface::decodeRequest()
    public function decodeRequest($data)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('A string was expected');
        }

        $reader = $this->getReader($data, true);
        $this->expectStartTag($reader, 'methodCall');
        $this->expectStartTag($reader, 'methodName');
        $methodName = $this->parseText($reader);
        $this->expectEndTag($reader, 'methodName');

        $params         = array();
        $emptyParams    = null;
        try {
            $this->expectStartTag($reader, 'params');
        } catch (\InvalidArgumentException $emptyParams) {
            // Nothing to do here (no arguments given).
        }

        if (!$emptyParams) {
            $endOfParams = null;
            while (true) {
                try {
                    $this->expectStartTag($reader, 'param');
                } catch (\InvalidArgumentException $endOfParams) {
                    // Nothing to do here (end of arguments).
                }

                if ($endOfParams) {
                    break;
                }

                $this->expectStartTag($reader, 'value');
                $params[] = $this->decodeValue($reader);
                $this->expectEndTag($reader, 'value');
                $this->expectEndTag($reader, 'param');
            }
            $this->expectEndTag($reader, 'params');
        }
        $this->expectEndTag($reader, 'methodCall');

        $endOfFile = null;
        try {
            $this->readNode($reader);
        } catch (\InvalidArgumentException $endOfFile) {
        }

        if (!$endOfFile) {
            throw new \InvalidArgumentException('Expected end of document');
        }

        $request = new \fpoirotte\XRL\Request($methodName, $params);
        return $request;
    }

    /// \copydoc fpoirotte::XRL::DecoderInterface::decodeResponse()
    public function decodeResponse($data)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('A string was expected');
        }

        $error  = null;
        $reader = $this->getReader($data, false);
        $this->expectStartTag($reader, 'methodResponse');
        try {
            // Try to parse a successful response first.
            $this->expectStartTag($reader, 'params');
            $this->expectStartTag($reader, 'param');
            $this->expectStartTag($reader, 'value');
            $response = $this->decodeValue($reader);
            $this->expectEndTag($reader, 'value');
            $this->expectEndTag($reader, 'param');
            $this->expectEndTag($reader, 'params');
        } catch (\InvalidArgumentException $error) {
            // Try to parse a fault instead.
            $this->expectStartTag($reader, 'fault');
            $this->expectStartTag($reader, 'value');

            $response = $this->decodeValue($reader);
            if (!is_array($response) || count($response) != 2) {
                throw new \UnexpectedValueException(
                    'An associative array with exactly '.
                    'two entries was expected'
                );
            }

            if (!isset($response['faultCode'])) {
                throw new \DomainException('The failure lacks a faultCode');
            }

            if (!isset($response['faultString'])) {
                throw new \DomainException('The failure lacks a faultString');
            }

            $this->expectEndTag($reader, 'value');
            $this->expectEndTag($reader, 'fault');
        }
        $this->expectEndTag($reader, 'methodResponse');

        $endOfFile = null;
        try {
            $this->readNode($reader);
        } catch (\InvalidArgumentException $endOfFile) {
        }

        if (!$endOfFile) {
            throw new \InvalidArgumentException('Expected end of document');
        }

        if ($error) {
            throw new \fpoirotte\XRL\Exception(
                $response['faultString'],
                $response['faultCode']
            );
        }

        return $response;
    }
}
