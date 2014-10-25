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
 *      An XML node as read from an XML reader.
 *
 * This class is used to memorize the last XML node
 * that was read from an XML reader, so as to push
 * the node back in case it could not be processed
 * by an XML-RPC decoder.
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
     *      Whether an exception should be raised (\c TRUE)
     *      or not (\c FALSE) if the current node is not valid.
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
            'name',
            'nodeType',
            'value',
            'isEmptyElement',
        );

        $this->properties = array();
        foreach ($fields as $field) {
            $this->properties[$field] = $reader->$field;
        }
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
     *      \c TRUE if this node was an empty one and it
     *      has been successfully expanded, or \c FALSE
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
