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
 *      Abstract class for fixed-length integer types.
 *
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
abstract class AbstractInteger extends \fpoirotte\XRL\Types\AbstractType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::__toString()
    public function __toString()
    {
        return gmp_strval($this->value);
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        $size = static::INTEGER_BITS;

        // Versions before PHP 5.6 used resources to represent big numbers
        // while new versions use objects instead.
        if ((is_resource($value) && get_resource_type($value) === 'GMP integer') ||
            (is_object($value) && ($value instanceof \GMP))) {
            // It is already a GMP integer.
        } else {
            $value = @gmp_init($value, 10);
        }
        if ($value === false) {
            throw new \InvalidArgumentException("Expected a signed $size-bits integer value");
        }

        // Check type bounds.
        $binval = gmp_strval($value, 2);
        if (!strncmp($binval, '-1', 2)) {
            $binval = (string) substr($binval, 2);
        }
        if (strlen($binval) >= $size) {
            throw new \InvalidArgumentException("Expected a signed $size-bits integer value");
        }

        $this->value = ($size <= 32) ? gmp_intval($value) : $value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        if (strpos(static::XMLRPC_TYPE, '}') !== false) {
            list($ns, $tagName) = explode('}', static::XMLRPC_TYPE, 2);
            $ns = (string) substr($ns, 1);
            return $writer->writeElementNS($ns, $tagName, gmp_strval($this->value));
        }
        return $writer->writeElement(static::XMLRPC_TYPE, gmp_strval($this->value));
    }
}
