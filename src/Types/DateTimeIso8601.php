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

class DateTimeIso8601 extends \fpoirotte\XRL\Types\AbstractType
{
    public function get()
    {
        return $this->value->format(\DateTime::ISO8601);
    }

    public function set($value) {
        if (!is_object($value) || !($value instanceof \DateTime)) {
            throw new \InvalidArgumentException('Expected date-time value');
        }
        $this->value = $value;
    }

    public function write(\XMLWriter $writer)
    {
        return $writer->writeElement('dateTime.iso8601', $this->value);
    }

    protected static function parse(
        \XMLReader $reader,
        $value,
        \DateTimeZone $timezone = null
    ) {
        if ($timezone === null) {
            $timezone = new \DateTimeZone(@date_default_timezone_get());
        }

        $result = new \DateTime($value, $timezone);
        if (strcasecmp($value, $result->format(\DateTime::ISO8601))) {
            throw new \InvalidArgumentException('Invalid date/time');
        }

        return $result;
    }
}
