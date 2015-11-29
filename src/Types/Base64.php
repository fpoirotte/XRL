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
 *      The XML-RPC "base64" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Base64 extends \fpoirotte\XRL\Types\StringType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        $writer->writeElement('base64', base64_encode($this->value));
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::parse()
    protected static function parse($value, \DateTimeZone $timezone = null)
    {
        $res = base64_decode($value, true);
        if ($res === false) {
            throw new \InvalidArgumentException('Expected base64-encoded input');
        }
        return $res;
    }
}
