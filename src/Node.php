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
 *      An XML node as read from an XML reader.
 *
 * This class is used to memorize the last XML node
 * that was read from an XML reader, so as to push
 * the node back in case it could not be processed
 * by an XML-RPC decoder.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Node
{
    /// Fields that make up this node.
    protected $properties;

    /**
     * Create a new XML node.
     *
     * \param XMLReader $reader
     *      XML reader object that will be used to create
     *      this node.
     *
     * \param bool $validate
     *      Whether an exception should be raised (\c true)
     *      or not (\c false) if the current node is not valid.
     */
    public function __construct(\XMLReader $reader, $validate)
    {
        $skipNodes = array(\XMLReader::SIGNIFICANT_WHITESPACE);
        do {
            if (!@$reader->read()) {
                throw new \InvalidArgumentException(
                    'Unexpected end of document'
                );
            }
            if ($validate && !$reader->isValid()) {
                throw new \InvalidArgumentException('Invalid document');
            }
        } while (in_array($reader->nodeType, $skipNodes));

        $fields = array(
            'nodeType',
            'value',
            'isEmptyElement',
            'localName',
            'namespaceURI',
        );

        $this->properties = array();
        foreach ($fields as $field) {
            $this->properties[$field] = $reader->$field;
        }
        $name = $reader->localName;
        if ($reader->namespaceURI !== '') {
            $name = '{' . $reader->namespaceURI . '}' . $name;
        }
        $this->properties['name'] = $name;
    }

    /**
     * Magic method that returns one of the fields
     * of this node.
     *
     * \param string $field
     *      The name of the field to return.
     *
     * \throw UnexpectedValueException
     *      Raised when the given field does not exist.
     *
     * \note
     *      Currently, only those field are valid:
     *      -   \c name
     *      -   \c nodeType
     *      -   \c value
     *      -   \c isEmptyElement
     */
    public function __get($field)
    {
        if (!isset($this->properties[$field])) {
            throw new \UnexpectedValueException("Unknown property '$field'");
        }

        return $this->properties[$field];
    }

    /**
     * Try to expand the current node if it's an empty one
     * and return whether the expansion worked or not.
     *
     * \retval bool
     *      \c true if this node was an empty one and it
     *      has been successfully expanded, or \c false
     *      otherwise.
     */
    public function emptyNodeExpansionWorked()
    {
        if ($this->properties['nodeType'] == \XMLReader::ELEMENT &&
            $this->properties['isEmptyElement'] == true) {
            $this->properties['nodeType'] = \XMLReader::END_ELEMENT;
            $this->properties['isEmptyElement'] = false;
            return true;
        }
        return false;
    }
}
