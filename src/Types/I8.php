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
 * The "i8" type extension, as described in
 * http://ws.apache.org/xmlrpc/types.html
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class I8 extends \fpoirotte\XRL\Types\AbstractInteger
{
    /// XML-RPC type for this class.
    const XMLRPC_TYPE   = '{http://ws.apache.org/xmlrpc/namespaces/extensions}i8';

    /// Integer size in bits.
    const INTEGER_BITS  = 64;

    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        try {
            // Try to use PHP's integer type to hold the value.
            // This will only work on 64-bit versions of PHP.
            parent::set($value);
        } catch (\InvalidArgumentException $e) {
            // Try to use a GMP resource/object instead,
            // but make sure the value does not overflow/underflow.
            $value  = new \fpoirotte\XRL\Types\BigInteger($value);
            $gmp    = $value->get();
            if (gmp_cmp($gmp, "-9223372036854775808") < 0 || gmp_cmp($gmp, "9223372036854775807") > 0) {
                throw $e;
            }
            $this->value = $value;
        }
    }
}
