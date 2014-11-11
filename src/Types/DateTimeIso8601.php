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
class DateTimeIso8601 extends \fpoirotte\XRL\Types\AbstractDateTime
{
    /**
     * \brief
     *      Date/time format.
     *
     * We can't just use DateTime::ISO8601 (= "Y-m-d\\TH:i:sO")
     * because the XML-RPC specification forbids timezones.
     */
    const XMLRPC_FORMAT = 'Y-m-d\\TH:i:s';

    /// XML-RPC type for this class.
    const XMLRPC_TYPE   = 'dateTime.iso8601';
}
