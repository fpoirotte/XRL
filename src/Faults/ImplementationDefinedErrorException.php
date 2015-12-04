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

namespace fpoirotte\XRL\Faults;

/**
 * \brief
 *      Definitions of interoperability faults.
 *
 * \see
 *      http://xmlrpc-epi.sourceforge.net/specs/rfc.fault_codes.php
 */
class ImplementationDefinedErrorException extends \fpoirotte\XRL\FaultException
{
    public function __construct($code = -32000, $message = 'implementation-defined error', \Exception $previous = null)
    {
        if ($code < -32099 || $code > -32000) {
            throw new \InvalidArgumentException('Invalid error code');
        }

        parent::__construct($message, $code, $previous);
    }
}
