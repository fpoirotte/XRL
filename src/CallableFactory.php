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

namespace fpoirotte\XRL;

/**
 * \brief
 *      A factory that creates new callable objects.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class CallableFactory implements \fpoirotte\XRL\CallableFactoryInterface
{
    /// \copydoc fpoirotte::XRL::CallableFactoryInterface::fromPHP()
    public function fromPHP($callable)
    {
        return new \fpoirotte\XRL\CallableObject($callable);
    }
}
