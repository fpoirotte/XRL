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
 *      The XML-RPC "i8" type.
 *
 * This type represents a big integer.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class BigInteger extends \fpoirotte\XRL\Types\AbstractInteger
{
    /// XML-RPC type for this class.
    const XMLRPC_TYPE   = '{http://ws.apache.org/xmlrpc/namespaces/extensions}biginteger';

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
            throw new \InvalidArgumentException("Expected a big integer value");
        }

        $this->value = $value;
    }
}
