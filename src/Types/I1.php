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
 *      The XML-RPC "i1" type.
 *
 * The "i1" type extension, as described in
 * http://ws.apache.org/xmlrpc/types.html
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class I1 extends \fpoirotte\XRL\Types\AbstractInteger
{
    /// XML-RPC type for this class.
    const XMLRPC_TYPE   = '{http://ws.apache.org/xmlrpc/namespaces/extensions}i1';

    /// Integer size in bits.
    const INTEGER_BITS  = 8;
}
