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
 *      A factory that produces objects meant
 *      to represent XML-RPC requests.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class RequestFactory implements \fpoirotte\XRL\RequestFactoryInterface
{
    /// \copydoc fpoirotte::XRL::RequestFactoryInterface::createRequest()
    public function createRequest($method, array $params)
    {
        return new \fpoirotte\XRL\Request($method, $params);
    }
}
