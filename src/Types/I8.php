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

class I8 extends \fpoirotte\XRL\Types\AbstractType
{
    public function get()
    {
        return gmp_strval($this->value);
    }

    public function set($value) {
        // Versions before PHP 5.6 used resources to represent big numbers
        // while new versions use objects instead.
        if ((is_resource($value) && get_resource_type($value) === 'GMP integer') ||
            (is_object($value) && ($value instanceof \GMP))) {
            // It is already a GMP integer.
        } else {
            $value = @gmp_init($value, 10);
        }
        if ($value === false) {
            throw new \InvalidArgumentException('Expected a signed 64-bits integer value');
        }

        // Check type bounds.
        $binval = gmp_strval($value, 2);
        if (!strncmp($binval, '-1', 2)) {
            $binval = (string) substr($binval, 2);
        }
        if (strlen($binval) >= 64) {
            throw new \InvalidArgumentException('Expected a signed 64-bits integer value');
        }

        $this->value = $value;
    }

    public function write(\XMLWriter $writer)
    {
        $writer->writeElement('i8', gmp_strval($this->value));
    }
}
