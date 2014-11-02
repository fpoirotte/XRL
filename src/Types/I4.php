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

// "i4" is mostly an alias for "int",
// but it has its own type nonetheless.
class I4 extends \fpoirotte\XRL\Types\Int
{
    public function write(\XMLWriter $writer)
    {
        $writer->writeElement('i4', $this->value);
    }
}
