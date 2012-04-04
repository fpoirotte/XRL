<?php

class       XRL_Decoder
implements  XRL_DecoderInterface
{
    protected $_validate;
    protected $_currentNode;

    public function __construct($validate = TRUE)
    {
        if (!is_bool($validate))
            ; /// @TODO

        $this->_validate    = $validate;
        $this->_currentNode = NULL;
    }

    protected function _getReader($data, $request)
    {
        if (!is_bool($request))
            ; /// @TODO

        $this->_currentNode = NULL;
        $reader = new XMLReader();
        $reader->setParserProperty(XMLReader::LOADDTD, FALSE);
        $reader->setParserProperty(XMLReader::DEFAULTATTRS, FALSE);
        $reader->setParserProperty(XMLReader::VALIDATE, FALSE);
        $reader->setParserProperty(XMLReader::SUBST_ENTITIES, TRUE);
        $reader->xml($data, NULL, LIBXML_NONET | LIBXML_NOENT);
//        $reader->setRelaxNGSchema();
        return $reader;
    }

    protected function _readNode($reader)
    {
        if ($this->_currentNode !== NULL)
            return $this->_currentNode;

        $this->_currentNode = new XRL_Node($reader);
        return $this->_currentNode;
    }

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
                "Got opening $readTag tag instead of $expectedTag"
            );
        }

        $this->_currentNode = NULL;
    }

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
                "Got closing $readTag tag instead of $expectedTag"
            );
        }

        $this->_currentNode = NULL;
    }

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
        $this->_currentNode = NULL;
        return $value;
    }

    static protected function _checkType(array $allowedTypes, $type, $value)
    {
        if (!count($allowedTypes) && !in_array($type, $allowedTypes)) {
            $allowed = implode(', ', $allowedTypes);
            throw new InvalidArgumentException(
                "Expected one of: $allowed, but got $type"
            );
        }

        return $value;
    }

    protected function _decodeValue($reader, array $allowedTypes = array())
    {
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
                    $value = NULL;
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
                $key = $this->_decodeValue($reader, array('string'));
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
        $value = $this->_parseText($reader);
        return self::_checkType($allowedTypes, 'string', $value);
    }

    public function decodeRequest($data)
    {
        if (!is_string($data))
            ; /// @TODO

        $reader = $this->_getReader($data, TRUE);
        $this->_expectStartTag($reader, 'methodCall');
        $this->_expectStartTag($reader, 'methodName');
        $methodName = $this->_parseText($reader);
        $this->_expectEndTag($reader, 'methodName');

        $params = array();
        try {
            $this->_expectStartTag($reader, 'params');
            try {
                $this->_expectStartTag($reader, 'param');
                $this->_expectStartTag($reader, 'value');
                $params[] = $this->_parseValue($reader);
                $this->_expectEndTag($reader, 'value');
                $this->_expectEndTag($reader, 'param');
            }
            $this->_expectEndTag($reader, 'params');
        }
        catch (InvalidArgumentException $e) {
        }
        $this->_expectEndTag($reader, 'methodCall');

        $eof = NULL;
        try {
            $this->_readNode($reader);
        }
        catch (InvalidArgumentException $eof) {
        }

        if (!$eof)
            throw new InvalidArgumentException('Expected end of document');

        $request = new XRL_Request($methodName, $params);
        return $request;
    }

    public function decodeResponse($data)
    {
        if (!is_string($data))
            ; /// @TODO

        $error  = NULL;
        $reader = $this->_getReader($data, TRUE);
        $this->_expectStartTag($reader, 'methodResponse');
        try {
            // Try to parse a successful response first.
            $this->_expectStartTag($reader, 'params');
            $this->_expectStartTag($reader, 'param');
            $this->_expectStartTag($reader, 'value');
            $response = $this->_parseValue($reader);
            $this->_expectEndTag($reader, 'value');
            $this->_expectEndTag($reader, 'param');
            $this->_expectEndTag($reader, 'params');
        }
        catch (InvalidArgumentException $error) {
            // Try to parse a fault instead.
            $this->_expectStartTag($reader, 'fault');
            $this->_expectStartTag($reader, 'value');

            $response = $this->_parseValue($reader);
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

        $eof = NULL;
        try {
            $this->_readNode($reader);
        }
        catch (InvalidArgumentException $eof) {
        }

        if (!$eof)
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

