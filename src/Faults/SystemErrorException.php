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
class SystemErrorException extends \fpoirotte\XRL\FaultException
{
    public function __construct($message = 'system error', \Exception $previous = null)
    {
        parent::__construct($message, -32400, $previous);
    }
}
