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
 *      A decoder that can process XML-RPC requests
 *      and responses, with optional XML validation.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Decoder implements \fpoirotte\XRL\DecoderInterface
{
    /// Whether the documents should be validated or not.
    protected $validate;

    /// The fpoirotte::XRL::Node currently being processed.
    protected $currentNode;

    /// Timezone used to decode date/times.
    protected $timezone;

    /// Names for the various types of XML nodes in libxml.
    protected static $types = array(
        \XMLReader::NONE                    => 'NONE',
        \XMLReader::ELEMENT                 => 'ELEMENT',
        \XMLReader::ATTRIBUTE               => 'ATTRIBUTE',
        \XMLReader::TEXT                    => 'TEXT',
        \XMLReader::CDATA                   => 'CDATA',
        \XMLReader::ENTITY_REF              => 'ENTITY_REF',
        \XMLReader::ENTITY                  => 'ENTITY',
        \XMLReader::PI                      => 'PI',
        \XMLReader::COMMENT                 => 'COMMENT',
        \XMLReader::DOC                     => 'DOC',
        \XMLReader::DOC_TYPE                => 'DOC_TYPE',
        \XMLReader::DOC_FRAGMENT            => 'DOC_FRAGMENT',
        \XMLReader::NOTATION                => 'NOTATION',
        \XMLReader::WHITESPACE              => 'WHITESPACE',
        \XMLReader::SIGNIFICANT_WHITESPACE  => 'SIGNIFICANT_WHITESPACE',
        \XMLReader::END_ELEMENT             => 'END_ELEMENT',
        \XMLReader::END_ENTITY              => 'END_ENTITY',
        \XMLReader::XML_DECLARATION         => 'XML_DECLARATION',
    );

    /**
     * Creates a new decoder.
     *
     * \param DateTimeZone $timezone
     *      (optional) Information on the timezone incoming
     *      date/times come from.
     *      If omitted, the machine's current timezone is used.
     *
     * \param bool $validate
     *      (optional) Whether the decoder should validate
     *      its input (\c true) or not (\c false).
     *      Validation is enabled by default.
     *
     * \throw InvalidArgumentException
     *      The value passed for \c $validate was
     *      not a boolean.
     */
    public function __construct(
        \DateTimeZone $timezone = null,
        $validate = true
    ) {
        if (!is_bool($validate)) {
            throw new \InvalidArgumentException('Not a boolean');
        }

        if ($timezone === null) {
            try {
                $timezone = new \DateTimeZone(@date_default_timezone_get());
            } catch (\Exception $e) {
                throw new \InvalidArgumentException($e->getMessage(), $e->getCode());
            }
        }

        $this->validate    = $validate;
        $this->currentNode = null;
        $this->timezone    = $timezone;
    }

    /**
     * Returns an XML reader for some data.
     *
     * \param string $URI
     *      URI to the XML data to process.
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
    protected function getReader($URI, $request)
    {
        if (!is_string($URI)) {
            throw new \InvalidArgumentException('Not a string');
        }

        if (!is_bool($request)) {
            throw new \InvalidArgumentException('Not a boolean');
        }

        $this->currentNode = null;
        $reader = new \XMLReader();
        $reader->open($URI, null, LIBXML_NONET | LIBXML_NOENT);
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

        $this->currentNode = new \fpoirotte\XRL\Node($reader, $this->validate, true);
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
        if ($type !== \XMLReader::ELEMENT) {
            $type = isset(self::$types[$type]) ? self::$types[$type] : "#$type";
            throw new \InvalidArgumentException(
                "Expected an opening $expectedTag tag ".
                "but got a node of type $type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag !== $expectedTag) {
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
        if ($type !== \XMLReader::END_ELEMENT) {
            $type = isset(self::$types[$type]) ? self::$types[$type] : "#$type";
            throw new \InvalidArgumentException(
                "Expected a closing $expectedTag tag ".
                "but got a node of type $type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag !== $expectedTag) {
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
        if ($type !== \XMLReader::TEXT) {
            $type = isset(self::$types[$type]) ? self::$types[$type] : "#$type";
            throw new \InvalidArgumentException(
                "Expected a text node, but got ".
                "a node of type $type instead"
            );
        }

        $value = $node->value;
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
                "Expected one of: $allowed; got $type"
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
    protected function decodeValue(\XMLReader $reader, array $allowedTypes = array())
    {
        // Basic types.
        $types = array(
            // Support for the <nil> extension
            // (http://ontosys.com/xml-rpc/extensions.php)
            'nil'               => '\\fpoirotte\\XRL\\Types\\Nil',
            'i4'                => '\\fpoirotte\\XRL\\Types\\I4',
            'i8'                => '\\fpoirotte\\XRL\\Types\\I8',
            'int'               => '\\fpoirotte\\XRL\\Types\\Int',
            'boolean'           => '\\fpoirotte\\XRL\\Types\\Boolean',
            'string'            => '\\fpoirotte\\XRL\\Types\\String',
            'double'            => '\\fpoirotte\\XRL\\Types\\Double',
            'dateTime.iso8601'  => '\\fpoirotte\\XRL\\Types\\DateTimeIso8601',
            'base64'            => '\\fpoirotte\\XRL\\Types\\Base64',

            // Some Apache extensions.
            // See http://ws.apache.org/xmlrpc/types.html
            '{http://ws.apache.org/xmlrpc/namespaces/extensions}nil'
                => '\\fpoirotte\\XRL\\Types\\Nil',
            '{http://ws.apache.org/xmlrpc/namespaces/extensions}i1'
                => '\\fpoirotte\\XRL\\Types\\I1',
            '{http://ws.apache.org/xmlrpc/namespaces/extensions}i8'
                => '\\fpoirotte\\XRL\\Types\\I8',
            '{http://ws.apache.org/xmlrpc/namespaces/extensions}i2'
                => '\\fpoirotte\\XRL\\Types\\I2',
            '{http://ws.apache.org/xmlrpc/namespaces/extensions}biginteger'
                => '\\fpoirotte\\XRL\\Types\\BigInteger',
            '{http://ws.apache.org/xmlrpc/namespaces/extensions}dateTime'
                => '\\fpoirotte\\XRL\\Types\\DateTime',
        );

        foreach ($types as $type => $cls) {
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
                if ($type !== 'string' && $type !== 'base64') {
                    throw $e;
                }
                $value = '';
            }
            $this->expectEndTag($reader, $type);
            $value = $cls::read($value, $this->timezone);
            return self::checkType($allowedTypes, $type, $value);
        }

        // Handle structures.
        $error = null;
        try {
            $this->expectStartTag($reader, 'struct');
        } catch (\InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = new \fpoirotte\XRL\Types\Struct(array());
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
                $key = $this->parseText($reader);
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
            $value = new \fpoirotte\XRL\Types\ArrayType(array());
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

        // Handle Apache's <dom> type.
        $error = null;
        try {
            $this->expectStartTag($reader, '{http://ws.apache.org/xmlrpc/namespaces/extensions}dom');
        } catch (\InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = \fpoirotte\XRL\Types\Dom::read($reader->readInnerXML());
            // Move to next sibling, skipping subtrees, and save the result.
            $this->currentNode = new \fpoirotte\XRL\Node($reader, $this->validate, false);
            return self::checkType(
                $allowedTypes,
                '{http://ws.apache.org/xmlrpc/namespaces/extensions}dom',
                $value
            );
        }

        // Default type (string).
        try {
            $value = $this->parseText($reader);
        } catch (\InvalidArgumentException $e) {
            $value = '';
        }
        $value = new \fpoirotte\XRL\Types\String($value);
        return self::checkType($allowedTypes, 'string', $value);
    }

    /// \copydoc fpoirotte::XRL::DecoderInterface::decodeRequest()
    public function decodeRequest($URI)
    {
        if (!is_string($URI)) {
            throw new \InvalidArgumentException('A string was expected');
        }

        $reader = $this->getReader($URI, true);
        $ldel = libxml_disable_entity_loader(true);
        $luie = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
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
            libxml_disable_entity_loader($ldel);
            libxml_clear_errors();
            libxml_use_internal_errors($luie);
            return $request;
        } catch (\Exception $e) {
            libxml_disable_entity_loader($ldel);
            libxml_clear_errors();
            libxml_use_internal_errors($luie);
            throw $e;
        }
    }

    /// \copydoc fpoirotte::XRL::DecoderInterface::decodeResponse()
    public function decodeResponse($URI)
    {
        if (!is_string($URI)) {
            throw new \InvalidArgumentException('A string was expected');
        }

        $error  = null;
        $reader = $this->getReader($URI, false);
        $ldel = libxml_disable_entity_loader(true);
        $luie = libxml_use_internal_errors(true);
        libxml_clear_errors();
        try {
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
                if (!($response instanceof \fpoirotte\XRL\Types\Struct) ||
                    count($response) != 2) {
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
                    (string) $response['faultString'],
                    $response['faultCode']->get()
                );
            }

            libxml_disable_entity_loader($ldel);
            libxml_clear_errors();
            libxml_use_internal_errors($luie);
            return $response;
        } catch (\Exception $e) {
            libxml_disable_entity_loader($ldel);
            libxml_clear_errors();
            libxml_use_internal_errors($luie);
            throw $e;
        }
    }
}
