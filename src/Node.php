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

    /// Error codes from http://www.xmlsoft.org/html/libxml-xmlerror.html.
    const XML_ERR_INTERNAL_ERROR        = 1;
    const XML_ERR_NO_MEMORY             = 2;
    const XML_ERR_DOCUMENT_EMPTY        = 4;
    const XML_ERR_DOCUMENT_END          = 5;
    const XML_ERR_UNKNOWN_ENCODING      = 31;
    const XML_ERR_UNSUPPORTED_ENCODING  = 32;

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
     *
     * \param bool $subtrees
     *      Whether to explore subtrees (\c true) or not (\c false).
     */
    public function __construct(\XMLReader $reader, $validate, $subtrees)
    {
        do {
            // We must silence read()/next() as old PHPs (5.3.x) emit warnings
            // which get caught by PHPUnit and other custom error handlers
            // when the methods fail and this is known to cause some issues.
            if (($subtrees && !@$reader->read()) ||
                (!$subtrees && !@$reader->next())) {
                $error = libxml_get_last_error();

                // We reached the end of the document.
                // This is not an error per-se,
                // but it causes read() to fail anyway.
                // We throw a special error which gets caught
                // and dealt with appropriately by the caller.
                if ($error === false)
                    throw new \InvalidArgumentException('End of document');

                switch ($error->code) {
                    case self::XML_ERR_UNKNOWN_ENCODING:
                    case self::XML_ERR_UNSUPPORTED_ENCODING:
                        throw \fpoirotte\XRL\Faults::get(
                            \fpoirotte\XRL\Faults::UNSUPPORTED_ENCODING
                        );

                    // @codeCoverageIgnoreStart
                    // Internal & memory errors are too hard to recreate
                    // and are thus excluded from code coverage analysis.
                    case self::XML_ERR_INTERNAL_ERROR:
                    case self::XML_ERR_NO_MEMORY:
                        throw \fpoirotte\XRL\Faults::get(
                            \fpoirotte\XRL\Faults::INTERNAL_ERROR
                        );
                    // @codeCoverageIgnoreEnd

                    // Generic error handling.
                    default:
                        throw \fpoirotte\XRL\Faults::get(
                            \fpoirotte\XRL\Faults::NOT_WELL_FORMED
                        );
                }
            }

            if ($validate && !$reader->isValid()) {
                throw \fpoirotte\XRL\Faults::get(
                    \fpoirotte\XRL\Faults::INVALID_XML_RPC
                );
            }

            $subtrees = true;
        } while ($reader->nodeType === \XMLReader::SIGNIFICANT_WHITESPACE);

        $fields = array(
            'isEmptyElement',
            'localName',
            'namespaceURI',
            'nodeType',
            'value',
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
     *      Currently, the following fields are valid:
     *      -   \c isEmptyElement
     *      -   \c localName
     *      -   \c name (= "{namespaceURI}localName" or just "localName")
     *      -   \c namespaceURI
     *      -   \c nodeType
     *      -   \c value
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
     *      was successfully expanded, \c false otherwise.
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
