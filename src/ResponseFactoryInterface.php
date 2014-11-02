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
 *      Interface for a factory that produces
 *      XML-RPC responses.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface ResponseFactoryInterface
{
    /**
     * Create an XML-RPC response.
     *
     * \param string $response
     *      The content of the XML-RPC response,
     *      as serialized XML.
     *
     * \retval fpoirotte::XRL::ResponseInterface
     *      An object wrapping the XML-RPC response
     *      with simple publishing features.
     */
    public function createResponse($response);
}
