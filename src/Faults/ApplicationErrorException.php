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
 *      An interoperable fault representing an application error.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class ApplicationErrorException extends \fpoirotte\XRL\FaultException
{
    public function __construct($message = 'application error', \Exception $previous = null)
    {
        parent::__construct($message, -32500, $previous);
    }
}
