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

class Struct extends \fpoirotte\XRL\Types\AbstractCollection
{
    public function get()
    {
        $res = array();
        foreach ($this->value as $key => $val) {
            $res[$key] = $val->get();
        }
        return $res;
    }

    public function set($value) {
        if (!is_array($value)) {
            throw new \InvalidArgumentException('Expected struct value');
        }

        foreach ($value as $key => $val) {
            if (!is_string($key)) {
                throw new \InvalidArgumentException('Expected struct value');
            }

            if (!($val instanceof \fpoirotte\XRL\Types\AbstractType)) {
                throw new \InvalidArgumentException('Expected a valid XML-RPC type');
            }
        }

        $this->value = $value;
    }

    public function write(\XMLWriter $writer)
    {
        $writer->startElement('struct');
        foreach ($this->value as $key => $val) {
            $writer->startElement('member');
            $writer->startElement('name');
            $writer->text((string) $key);
            $writer->endElement();
            $writer->startElement('value');
            $val->write($writer);
            $writer->endElement();
            $writer->endElement();
        }
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
        if (!is_string($offset)) {
            throw new \InvalidArgumentException('Expected string offset');
        }
        $this->value[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->value[$offset]);
    }

    public function key()
    {
        $keys = array_keys($this->value);
        return $keys[$this->index];
    }
}
