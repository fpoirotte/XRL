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
 *      An interoperable fault representing an error
 *      due to an invalid XML-RPC message.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class InvalidXmlRpcException extends \fpoirotte\XRL\FaultException
{
    const DEFAULT_MSG = 'server error. invalid xml-rpc. not conforming to spec';


    public function __construct($message = self::DEFAULT_MSG, \Exception $previous = null)
    {
        parent::__construct($message, -32600, $previous);
    }
}
