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

class Double extends \fpoirotte\XRL\Types\AbstractType
{
    public function set($value) {
        if (!is_double($value)) {
            throw new \InvalidArgumentException('Expected double value');
        }
        $this->value = $value;
    }

    public function write(\XMLWriter $writer)
    {
        $writer->writeElement('double', $this->value);
    }

    protected static function parse(
        \XMLReader $reader,
        $value,
        \DateTimeZone $timezone = null
    ) {
        /// @FIXME implement stricter checks
        return (double) $value;
    }
}
