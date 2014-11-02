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
 *      Interface for the response to an XML-RPC
 *      request, as produced by an XML-RPC server.
 *
 * \note
 *      This interface is never used by XML-RPC
 *      clients.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface ResponseInterface
{
    /**
     * Return the XML-RPC response this object
     * represents, as serialized XML.
     *
     * \retval string
     *      This XML-RPC response, as serialized XML.
     */
    public function __toString();

    /**
     * Publish this XML-RPC response to a browser.
     *
     * This method sets the proper HTTP headers
     * and then sends the XML-RPC response to a
     * browser.
     *
     * \warning
     *      This method never returns.
     */
    public function publish();
}
