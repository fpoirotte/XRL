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
 *      An XML node as read from an XML reader.
 *
 * This class is used to memorize the last XML node
 * that was read from an XML reader, so as to push
 * the node back in case it could not be processed
 * by an XML-RPC decoder.
 */
class XRL_Node
{
    /// Fields that make up this node.
    protected $_properties;

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
    public function __construct(XMLReader $reader, $validate)
    {
        $skipNodes = array(XMLReader::SIGNIFICANT_WHITESPACE);
        do {
            if (!$reader->read()) {
                throw new InvalidArgumentException(
                    'Unexpected end of document'
                );
            }
            if ($validate && !$reader->isValid())
                throw new InvalidArgumentException('Invalid document');
        } while (in_array($reader->nodeType, $skipNodes));

        $fields = array(
            'name',
            'nodeType',
            'value',
            'isEmptyElement',
        );

        $this->_properties = array();
        foreach ($fields as $field)
            $this->_properties[$field] = $reader->$field;
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
        if (!isset($this->_properties[$field]))
            throw new UnexpectedValueException("Unknown property '$field'");

        return $this->_properties[$field];
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
        if ($this->_properties['nodeType'] == XMLReader::ELEMENT &&
            $this->_properties['isEmptyElement'] == TRUE) {
            $this->_properties['nodeType'] = XMLReader::END_ELEMENT;
            $this->_properties['isEmptyElement'] = FALSE;
            return TRUE;
        }
        return FALSE;
    }
}

