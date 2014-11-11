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
 *      The XML-RPC "int" type.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Int extends \fpoirotte\XRL\Types\AbstractInteger
{
    /// XML-RPC type for this class.
    const XMLRPC_TYPE   = 'int';

    /// Integer size in bits.
    const INTEGER_BITS  = 32;
}
