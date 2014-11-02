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

class Nil extends \fpoirotte\XRL\Types\AbstractType
{
    public function set($value) {
        if ($value !== null) {
            throw new \InvalidArgumentException('Expected null value');
        }
    }

    public function write(\XMLWriter $writer)
    {
        return $writer->writeElement('nil');
    }

    protected static function parse($value)
    {
        return null;
    }
}
