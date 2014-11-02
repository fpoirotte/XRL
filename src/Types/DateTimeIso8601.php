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
 *      The XML-RPC "dateTime.iso8601" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class DateTimeIso8601 extends \fpoirotte\XRL\Types\AbstractType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::__toString()
    public function __toString()
    {
        // We can't just use DateTime::ISO8601 (= "Y-m-d\\TH:i:sO")
        // because the XML-RPC specification forbids timezones.
        return $this->value->format('Y-m-d\\TH:i:s');
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::set()
    public function set($value)
    {
        if (!is_object($value) || !($value instanceof \DateTime)) {
            throw new \InvalidArgumentException('Expected date-time value');
        }
        $this->value = $value;
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::write()
    public function write(\XMLWriter $writer)
    {
        return $writer->writeElement('dateTime.iso8601', $this->value);
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::parse()
    protected static function parse($value, \DateTimeZone $timezone = null)
    {
        if ($timezone === null) {
            $timezone = new \DateTimeZone(@date_default_timezone_get());
        }

        $result = new \DateTime($value, $timezone);

        // We can't just use DateTime::ISO8601 (= "Y-m-d\\TH:i:sO")
        // because the XML-RPC specification forbids timezones.
        if (strcasecmp($value, $result->format('Y-m-d\\TH:i:s'))) {
            throw new \InvalidArgumentException('Invalid date/time');
        }

        return $result;
    }
}
