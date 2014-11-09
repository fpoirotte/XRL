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
 *      Interface for an XML-RPC decoder.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface DecoderInterface
{
    /**
     * Decode an XML-RPC request.
     *
     * \param string $URI
     *      URI to XML-RPC request.
     *
     * \retval fpoirotte::XRL::Request
     *      An object representing an XML-RPC request.
     *
     * \throw InvalidArgumentException
     *      The given \c $data was invalid. For example,
     *      it wasn't a string, it didn"t contain any XML
     *      or the request was malformed.
     */
    public function decodeRequest($URI);

    /**
     * Decode an XML-RPC response.
     *
     * \param string $URI
     *      URI to the XML-RPC response.
     *
     * \retval mixed
     *      The return value represented by the XML-RPC response.
     *
     * \throw fpoirotte::XRL::Exception
     *      Thrown whenever the response described
     *      a failure. This exception's \c getCode()
     *      and \c getMessage() methods can be used
     *      to retrieve the original failure's code
     *      and description, respectively.
     *
     * \throw InvalidArgumentException
     *      The given \c $data was invalid. For example,
     *      it wasn't a string, it didn"t contain any XML
     *      or the request was malformed.
     */
    public function decodeResponse($URI);
}
