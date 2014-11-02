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

abstract class AbstractType
{
    protected $value;

    public function __construct($value)
    {
        $this->set($value);
    }

    public function get()
    {
        return $this->value;
    }

    final public static function read(
        \XMLReader $reader,
        $value,
        \DateTimeZone $timezone = null
    ) {
        return new static(static::parse($reader, $value, $timezone));
    }

    public function __toString()
    {
        return (string) $this->get();
    }

    static protected function parse(
        \XMLReader $reader,
        $value,
        \DateTimeZone $timezone = null
    ) {
        return $value;
    }

    abstract public function set($value);
    abstract public function write(\XMLWriter $writer);
}
