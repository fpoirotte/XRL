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

class Boolean extends \fpoirotte\XRL\Types\AbstractType
{
    public function __toString()
    {
        return ($this->value ? 'true' : 'false');
    }

    public function set($value) {
        if (!is_bool($value)) {
            throw new \InvalidArgumentException('Expected boolean value');
        }
        $this->value = $value;
    }

    public function write(\XMLWriter $writer)
    {
        $writer->writeElement('boolean', $this->value);
    }

    protected static function parse(
        \XMLReader $reader,
        $value,
        \DateTimeZone $timezone = null
    ) {
        return (bool) $value;
    }
}
