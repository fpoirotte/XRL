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

class Base64 extends \fpoirotte\XRL\Types\String
{
    public function write(\XMLWriter $writer)
    {
        $writer->writeElement('base64', base64_encode($this->value));
    }

    protected static function parse(
        \XMLReader $reader,
        $value,
        \DateTimeZone $timezone = null
    ) {
        $res = base64_decode($value, true);
        if ($res === false) {
            throw new \InvalidArgumentException('Expected base64-encoded input');
        }
        return $res;
    }
}
