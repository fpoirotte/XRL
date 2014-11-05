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
 *      The XML-RPC "double" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Double extends \fpoirotte\XRL\Types\AbstractType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        if (!is_double($value)) {
            throw new \InvalidArgumentException('Expected double value');
        }
        $this->value = $value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        $writer->writeElement('double', $this->value);
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::parse()
    protected static function parse($value, \DateTimeZone $timezone = null)
    {
        /// @FIXME implement stricter checks
        return (double) $value;
    }
}
