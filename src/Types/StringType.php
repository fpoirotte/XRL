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
 *      The XML-RPC "string" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class StringType extends \fpoirotte\XRL\Types\AbstractType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('Expected string value');
        }
        $this->value = $value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        if ($stringTag) {
            $writer->writeElement('string', $this->value);
        } else {
            $writer->text($this->value);
        }
    }
}
