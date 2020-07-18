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
        return (string) $this->value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        $size = static::INTEGER_BITS;

        if (is_object($value) && $value instanceof \GMP) {
            $value = gmp_strval($value, 10);
        }

        // If the value is a string, make sure it can be converted
        // without triggering an overflow/underflow.
        // This includes values obtained from GMP objects too (see above).
        if (is_string($value) && $value === (string) (int) $value) {
            $value = (int) $value;
        }

        // Detect integer overflows/underflows.
        if (is_int($value) && (
            ($value < 0 && strlen(base_convert(-$value, 10, 2)) < $size) ||
            ($value >= 0 && strlen(base_convert($value, 10, 2)) < ($size - 1)))) {
            $this->value = $value;
        } else {
            // Either the value used an incompatible type,
            // was a string with an invalid value,
            // or was a string or an integer whose value
            // could not be held by the given integer size.
            throw new \InvalidArgumentException("A $size-bit signed integer was expected");
        }
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        if (strpos(static::XMLRPC_TYPE, '}') !== false) {
            list($ns, $tagName) = explode('}', static::XMLRPC_TYPE, 2);
            $ns = (string) substr($ns, 1);
            return $writer->writeElementNS('ex', $tagName, $ns, (string) $this);
        }
        return $writer->writeElement(static::XMLRPC_TYPE, (string) $this);
    }
}
