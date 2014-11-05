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
 *      The XML-RPC "int" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Int extends \fpoirotte\XRL\Types\AbstractType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        // Versions before PHP 5.6 used resources to represent big numbers
        // while new versions use objects instead.
        if ((is_resource($value) && get_resource_type($value) === 'GMP integer') ||
            (is_object($value) && ($value instanceof \GMP))) {
            // It is already a GMP integer.
        } else {
            $value = @gmp_init($value, 10);
        }
        if ($value === false) {
            throw new \InvalidArgumentException('Expected a signed 32-bits integer value');
        }

        // Check type bounds.
        $binval = gmp_strval($value, 2);
        if (!strncmp($binval, '-1', 2)) {
            $binval = (string) substr($binval, 2);
        }
        if (strlen($binval) >= 32) {
            throw new \InvalidArgumentException('Expected a signed 32-bits integer value');
        }
        $this->value = gmp_intval($value);
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        $writer->writeElement('int', $this->value);
    }
}
