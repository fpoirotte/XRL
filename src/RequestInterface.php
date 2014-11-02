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
 *      Interface for an object representing an XML-RPC
 *      request.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface RequestInterface
{
    /**
     * Return the name of the procedure this request
     * refers to.
     *
     * \retval string
     *      Name of the XML-RPC procedure this request
     *      refers to.
     */
    public function getProcedure();

    /**
     * Return the parameters that will be passed
     * to that request's procedure.
     *
     * \retval array
     *      Parameters for the XML-RPC procedure,
     *      using native PHP types.
     */
    public function getParams();
}
