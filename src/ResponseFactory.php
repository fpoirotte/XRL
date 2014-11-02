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
 *      A factory that produces objects wrapping
 *      XML-RPC responses in a way that makes it
 *      easy to publish them.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class ResponseFactory implements \fpoirotte\XRL\ResponseFactoryInterface
{
    /// \copydoc fpoirotte::XRL::ResponseFactoryInterface::createResponse()
    public function createResponse($response)
    {
        return new \fpoirotte\XRL\Response($response);
    }
}
