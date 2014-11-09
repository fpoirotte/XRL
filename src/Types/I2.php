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
 *      The XML-RPC "i2" type.
 *
 * The "i2" type extension, as described in
 * http://ws.apache.org/xmlrpc/types.html
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class I2 extends \fpoirotte\XRL\Types\AbstractInteger
{
    const XMLRPC_TYPE   = '{http://ws.apache.org/xmlrpc/namespaces/extensions}i2';
    const INTEGER_BITS  = 16;
}
