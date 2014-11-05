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

namespace fpoirotte\XRL\Types;

/**
 * \brief
 *      The XML-RPC "struct" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Struct extends \fpoirotte\XRL\Types\AbstractCollection
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::get()
    public function get()
    {
        $res = array();
        foreach ($this->value as $key => $val) {
            $res[$key] = $val->get();
        }
        return $res;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Expected struct value');
        }

        foreach ($value as $key => $val) {
            if (!is_string($key) && !is_int($key)) {
                throw new \InvalidArgumentException('Expected struct value');
            }

            if (!($val instanceof \fpoirotte\XRL\Types\AbstractType)) {
                throw new \InvalidArgumentException('Expected a valid XML-RPC type');
            }
        }

        $this->value = $value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        $writer->startElement('struct');
        foreach ($this->value as $key => $val) {
            $writer->startElement('member');
            $writer->startElement('name');
            $writer->text((string) $key);
            $writer->endElement();
            $writer->startElement('value');
            $val->write($writer, $timezone, $stringTag);
            $writer->endElement();
            $writer->endElement();
        }
        $writer->endElement();
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::parse()
    protected static function parse($value, \DateTimeZone $timezone = null)
    {
        return array();
    }

    /**
     * Change the item at some index in the collection.
     *
     * \param int $offset
     *      Index of the item to modify.
     *
     * \param mixed $value
     *      New value for the item at the given index.
     *
     * \return
     *      This method does not return any value.
     */
    public function offsetSet($offset, $value)
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Expected string offset');
        }
        $this->value[$offset] = $value;
    }

    /**
     * Remove an item from the collection.
     *
     * \param mixed $offset
     *      Index of the item to remove.
     *
     * \return
     *      This method does not return any value.
     */
    public function offsetUnset($offset)
    {
        unset($this->value[$offset]);
    }

    /**
     * Return the current index
     * of this collection's cursor.
     *
     * \retval mixed
     *      Current index.
     */
    public function key()
    {
        $keys = array_keys($this->value);
        return $keys[$this->index];
    }
}
