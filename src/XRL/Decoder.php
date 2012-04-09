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
 *      A decoder that can process XML-RPC requests
 *      and responses, with optional XML validation.
 */
class       XRL_Decoder
implements  XRL_DecoderInterface
{
    /// Whether the documents should be validated or not.
    protected $_validate;

    /// The XRL_Node currently being processed.
    protected $_currentNode;

    /**
     * Creates a new decoder.
     *
     * \param bool $validate
     *      Whether the decoder should validate
     *      its input (\c TRUE) or not (\c FALSE).
     */
    public function __construct($validate = TRUE)
    {
        if (!is_bool($validate))
            ; /// @TODO

        $this->_validate    = $validate;
        $this->_currentNode = NULL;
    }

    /**
     * Returns an XML reader for some data.
     *
     * \param string $data
     *      XML data to process.
     *
     * \param bool $request
     *      Whether the data refers to an XML-RPC
     *      request (\c TRUE) or a response (\c FALSE).
     *
     * \retval XMLReader
     *      An XML reader for the given data.
     *
     * \note
     *      The reader is set to validate the document
     *      on the fly if that's what this decoder was
     *      configured to do during construction.
     */
    protected function _getReader($data, $request)
    {
        if (!is_bool($request))
            ; /// @TODO

        $this->_currentNode = NULL;
        $reader = new XMLReader();
        $reader->xml($data, NULL, LIBXML_NONET | LIBXML_NOENT);
        if ($this->_validate) {
            if ('@data_dir@' != '@'.'data_dir'.'@') {
                $schema = '@data_dir@' .
                    DIRECTORY_SEPARATOR . 'pear.erebot.net' .
                    DIRECTORY_SEPARATOR . 'XRL';
            }
            else
                $schema = dirname(dirname(dirname(__FILE__))) .
                    DIRECTORY_SEPARATOR . 'data';

            $schema .= DIRECTORY_SEPARATOR;
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
     * \retval XRL_Node
     *      The XML node that's been read.
     */
    protected function _readNode($reader)
    {
        if ($this->_currentNode !== NULL)
            return $this->_currentNode;

        $this->_currentNode = new XRL_Node($reader, $this->_validate);
        return $this->_currentNode;
    }

    /**
     * Prepare for the next XML node read.
     * This method should be called after each
     * successful node parsing.
     */
    protected function _prepareNextNode()
    {
        if (!$this->_currentNode->emptyNodeExpansionWorked())
            $this->_currentNode = NULL;
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
    protected function _expectStartTag($reader, $expectedTag)
    {
        $node = $this->_readNode($reader);

        $type = $node->nodeType;
        if ($type != XMLReader::ELEMENT) {
            throw new InvalidArgumentException(
                "Expected an opening $expectedTag tag ".
                "but got a node of type #$type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag != $expectedTag) {
            throw new InvalidArgumentException(
                "Got opening tag for $readTag instead of $expectedTag"
            );
        }

        $this->_prepareNextNode();
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
    protected function _expectEndTag($reader, $expectedTag)
    {
        $node = $this->_readNode($reader);

        $type = $node->nodeType;
        if ($type != XMLReader::END_ELEMENT) {
            throw new InvalidArgumentException(
                "Expected a closing $expectedTag tag ".
                "but got a node of type #$type instead"
            );
        }

        $readTag = $node->name;
        if ($readTag != $expectedTag) {
            throw new InvalidArgumentException(
                "Got closing tag for $readTag instead of $expectedTag"
            );
        }

        $this->_prepareNextNode();
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
    protected function _parseText($reader)
    {
        $node = $this->_readNode($reader);

        $type = $node->nodeType;
        if ($type != XMLReader::TEXT) {
            throw new InvalidArgumentException(
                "Expected a text node, but got ".
                "a node of type #$type instead"
            );
        }

        $value              = $node->value;
        $this->_prepareNextNode();
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
    static protected function _checkType(array $allowedTypes, $type, $value)
    {
        if (count($allowedTypes) && !in_array($type, $allowedTypes)) {
            $allowed = implode(', ', $allowedTypes);
            throw new InvalidArgumentException(
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
    protected function _decodeValue($reader, array $allowedTypes = array())
    {
        // Support for the <nil> extension
        // (http://ontosys.com/xml-rpc/extensions.php)
        $error = NULL;
        try {
            $this->_expectStartTag($reader, 'nil');
        }
        catch (InvalidArgumentException $error) {
        }

        if (!$error) {
            $this->_expectEndTag($reader, 'nil');
            return self::_checkType($allowedTypes, 'nil', NULL);
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
                $this->_expectStartTag($reader, $type);
            }
            catch (InvalidArgumentException $e) {
                continue;
            }

            $value = $this->_parseText($reader);
            $this->_expectEndTag($reader, $type);

            switch ($type) {
                case 'i4':
                    // "i4" is an alias for "int".
                    $type = 'int';
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
                    $value = NULL; /// @TODO
                    break;

                case 'base64':
                    $value = base64_decode($value);
                    break;
            }

            return self::_checkType($allowedTypes, $type, $value);
        }

        // Handle structures.
        $error = NULL;
        try {
            $this->_expectStartTag($reader, 'struct');
        }
        catch (InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = array();
            // Read values.
            while (TRUE) {
                $error = NULL;
                try {
                    $this->_expectStartTag($reader, 'member');
                }
                catch (InvalidArgumentException $error) {
                }

                if ($error)
                    break;

                // Read key.
                $this->_expectStartTag($reader, 'name');
                $key = $this->_decodeValue($reader, array('string', 'int'));
                $this->_expectEndTag($reader, 'name');

                $this->_expectStartTag($reader, 'value');
                $value[$key] = $this->_decodeValue($reader);
                $this->_expectEndTag($reader, 'value');
                $this->_expectEndTag($reader, 'member');
            }
            $this->_expectEndTag($reader, 'struct');
            return self::_checkType($allowedTypes, 'struct', $value);
        }

        // Handle arrays.
        $error = NULL;
        try {
            $this->_expectStartTag($reader, 'array');
        }
        catch (InvalidArgumentException $error) {
        }

        if (!$error) {
            $value = array();
            $this->_expectStartTag($reader, 'data');
            // Read values.
            while (TRUE) {
                $error = NULL;
                try {
                    $this->_expectStartTag($reader, 'value');
                }
                catch (InvalidArgumentException $error) {
                }

                if ($error)
                    break;

                $value[] = $this->_decodeValue($reader);
                $this->_expectEndTag($reader, 'value');
            }
            $this->_expectEndTag($reader, 'data');
            $this->_expectEndTag($reader, 'array');
            return self::_checkType($allowedTypes, 'array', $value);
        }

        // Default type (string).
        try {
            $value = $this->_parseText($reader);
        }
        catch (InvalidArgumentException $e) {
            $value = '';
        }
        return self::_checkType($allowedTypes, 'string', $value);
    }

    /// \copydoc XRL_DecoderInterface::decodeRequest()
    public function decodeRequest($data)
    {
        if (!is_string($data))
            ; /// @TODO

        $reader = $this->_getReader($data, TRUE);
        $this->_expectStartTag($reader, 'methodCall');
        $this->_expectStartTag($reader, 'methodName');
        $methodName = $this->_parseText($reader);
        $this->_expectEndTag($reader, 'methodName');

        $params         = array();
        $emptyParams    = NULL;
        try {
            $this->_expectStartTag($reader, 'params');
        }
        catch (InvalidArgumentException $emptyParams) {
            // Nothing to do here (no arguments given).
        }

        if (!$emptyParams) {
            $endOfParams = NULL;
            while (TRUE) {
                try {
                    $this->_expectStartTag($reader, 'param');
                }
                catch (InvalidArgumentException $endOfParams) {
                    // Nothing to do here (end of arguments).
                }

                if ($endOfParams)
                    break;

                $this->_expectStartTag($reader, 'value');
                $params[] = $this->_decodeValue($reader);
                $this->_expectEndTag($reader, 'value');
                $this->_expectEndTag($reader, 'param');
            }
            $this->_expectEndTag($reader, 'params');
        }
        $this->_expectEndTag($reader, 'methodCall');

        $endOfFile = NULL;
        try {
            $this->_readNode($reader);
        }
        catch (InvalidArgumentException $endOfFile) {
        }

        if (!$endOfFile)
            throw new InvalidArgumentException('Expected end of document');

        $request = new XRL_Request($methodName, $params);
        return $request;
    }

    /// \copydoc XRL_DecoderInterface::decodeResponse()
    public function decodeResponse($data)
    {
        if (!is_string($data))
            ; /// @TODO

        $error  = NULL;
        $reader = $this->_getReader($data, FALSE);
        $this->_expectStartTag($reader, 'methodResponse');
        try {
            // Try to parse a successful response first.
            $this->_expectStartTag($reader, 'params');
            $this->_expectStartTag($reader, 'param');
            $this->_expectStartTag($reader, 'value');
            $response = $this->_decodeValue($reader);
            $this->_expectEndTag($reader, 'value');
            $this->_expectEndTag($reader, 'param');
            $this->_expectEndTag($reader, 'params');
        }
        catch (InvalidArgumentException $error) {
            // Try to parse a fault instead.
            $this->_expectStartTag($reader, 'fault');
            $this->_expectStartTag($reader, 'value');

            $response = $this->_decodeValue($reader);
            if (!is_array($response) || count($response) != 2) {
                throw new UnexpectedValueException(
                    'An associative array with exactly '.
                    'two entries was expected'
                );
            }

            if (!isset($response['faultCode']))
                throw new DomainException('The failure lacks a faultCode');

            if (!isset($response['faultString']))
                throw new DomainException('The failure lacks a faultString');

            $this->_expectEndTag($reader, 'value');
            $this->_expectEndTag($reader, 'fault');
        }
        $this->_expectEndTag($reader, 'methodResponse');

        $endOfFile = NULL;
        try {
            $this->_readNode($reader);
        }
        catch (InvalidArgumentException $endOfFile) {
        }

        if (!$endOfFile)
            throw new InvalidArgumentException('Expected end of document');

        if ($error) {
            throw new XRL_Exception(
                $response['faultString'],
                $response['faultCode']
            );
        }

        return $response;
    }
}

