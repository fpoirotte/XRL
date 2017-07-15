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
 *      The XML-RPC "array" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class ArrayType extends \fpoirotte\XRL\Types\AbstractCollection
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::get()
    public function get()
    {
        $res = array();
        foreach ($this->value as $val) {
            $res[] = $val->get();
        }
        return $res;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Expected array value');
        }

        $keys       = array_keys($value);
        $length     = count($value);

        // Empty arrays must be handled with care.
        if (!$length) {
            $numeric = array();
        } else {
            $numeric = range(0, $length - 1);
            sort($keys);
        }

        // Detect associative arrays (which are invalid for this type).
        if ($keys !== $numeric) {
            throw new \InvalidArgumentException('Expected an indexed array');
        }

        foreach ($value as $val) {
            if (!($val instanceof \fpoirotte\XRL\Types\AbstractType)) {
                throw new \InvalidArgumentException('Expected a valid XML-RPC type');
            }
        }

        $this->value = $value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        $writer->startElement('array');
        $writer->startElement('data');
        foreach ($this->value as $val) {
            $writer->startElement('value');
            $val->write($writer, $timezone, $stringTag);
            $writer->endElement();
        }
        $writer->endElement();
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
        if ($offset === null) {
            $this->value[] = $value;
            return;
        } elseif (!is_int($offset)) {
            throw new \InvalidArgumentException('Expected integer offset');
        } elseif (!array_key_exists($offset, $this->value) && $offset !== count($this->value)) {
            throw new \InvalidArgumentException('Cannot set arbitrary offset in array');
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
        // Force reindexation.
        $this->value = array_values($value);
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
        return $this->index;
    }
}
