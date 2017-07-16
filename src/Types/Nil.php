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
 *      The XML-RPC "nil" type for null values.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Nil extends \fpoirotte\XRL\Types\AbstractType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        if ($value !== null) {
            throw new \InvalidArgumentException('A null value was expected');
        }
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        return $writer->writeElementNS(
            'ex',
            'nil',
            'http://ws.apache.org/xmlrpc/namespaces/extensions'
        );
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::parse()
    protected static function parse($value, \DateTimeZone $timezone = null)
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return $value;
    }
}
