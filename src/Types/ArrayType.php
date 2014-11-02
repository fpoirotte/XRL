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

class ArrayType extends \fpoirotte\XRL\Types\AbstractCollection
{
    public function get()
    {
        $res = array();
        foreach ($this->value as $val) {
            $res[] = $val->get();
        }
        return $res;
    }

    public function set($value) {
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

        // Hash / associative array.
        if ($keys !== $numeric) {
            throw new \InvalidArgumentException('Expected array value');
        }

        foreach ($value as $val) {
            if (!($val instanceof \fpoirotte\XRL\Types\AbstractType)) {
                throw new \InvalidArgumentException('Expected a valid XML-RPC type');
            }
        }

        $this->value = $value;
    }

    public function write(\XMLWriter $writer)
    {
        $writer->startElement('array');
        $writer->startElement('data');
        foreach ($this->value as $val) {
            $writer->startElement('value');
            $val->write($writer);
            $writer->endElement();
        }
        $writer->endElement();
        $writer->endElement();
    }

    protected static function parse(
        \XMLReader $reader,
        $value,
        \DateTimeZone $timezone = null
    ) {
        return array();
    }

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

    public function offsetUnset($offset) {
        unset($this->value[$offset]);
        // Force reindexation.
        $this->value = array_values($value);
    }

    public function key()
    {
        return $this->index;
    }
}
