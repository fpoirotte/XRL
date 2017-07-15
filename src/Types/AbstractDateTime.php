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
 *      Abstract class for various "date-time" types.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
abstract class AbstractDateTime extends \fpoirotte\XRL\Types\AbstractType
{
    /// \copydoc fpoirotte::XRL::Types::AbstractType::__toString()
    public function __toString()
    {
        return $this->value->format(static::XMLRPC_FORMAT);
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
    public function write(\XMLWriter $writer, \DateTimeZone $timezone, $stringTag)
    {
        // PHP has serious issues with timezone handling.
        // As a workaround, we use format(), specifying a UNIX timestamp
        // as the format to use, which we then reinject in a new DateTime.
        $date = new \DateTime('@'.$this->value->format('U'), $timezone);
        if (strpos(static::XMLRPC_TYPE, '}') !== false) {
            list($ns, $tagName) = explode('}', static::XMLRPC_TYPE, 2);
            $ns = (string) substr($ns, 1);
            return $writer->writeElementNS('ex', $tagName, $ns, $date->format(static::XMLRPC_FORMAT));
        }
        return $writer->writeElement(static::XMLRPC_TYPE, $date->format(static::XMLRPC_FORMAT));
    }

    /// \copydoc fpoirotte::XRL::Types::AbstractType::parse()
    protected static function parse($value, \DateTimeZone $timezone = null)
    {
        if ($timezone === null) {
            $timezone = new \DateTimeZone(@date_default_timezone_get());
        }

        $result = \DateTime::createFromFormat(static::XMLRPC_FORMAT, $value, $timezone);

        if (!is_object($result) || strcasecmp($value, $result->format(static::XMLRPC_FORMAT))) {
            throw new \InvalidArgumentException('Invalid date/time');
        }

        return $result;
    }
}
