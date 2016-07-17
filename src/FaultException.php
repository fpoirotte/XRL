<?php
/*
 * This file is part of XRL, a simple XML-RPC Library for PHP.
 *
 * Copyright (c) 2015, XRL Team. All rights reserved.
 * XRL is licensed under the 3-clause BSD License.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace fpoirotte\XRL;

/**
 * \brief
 *      Abstract definition of an interoperable fault.
 *
 * \see
 *      http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
abstract class FaultException extends \fpoirotte\XRL\Exception
{
    public function __construct($message = null, $code = -32000, \Exception $previous = null)
    {
        if ($code < -32768 || $code > -32000) {
            throw new \InvalidArgumentException('Invalid error code');
        }

        parent::__construct($message, $code, $previous);
    }
}
