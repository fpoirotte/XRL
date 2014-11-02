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
 *      Interface for an XML-RPC encoder.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
interface EncoderInterface
{
    /**
     * Encode an XML-RPC request.
     *
     * \param fpoirotte::XRL::Request $request
     *      The XML-RPC request to encode.
     *
     * \retval string
     *      The XML-RPC request,
     *      encoded as serialized XML.
     */
    public function encodeRequest(\fpoirotte\XRL\Request $request);

    /**
     * Encode an exception as an XML-RPC failure.
     *
     * \param Exception $error
     *      The exception to encode.
     *
     * \retval string
     *      The exception, encoded as a XML-RPC
     *      failure in serialized form.
     */
    public function encodeError(\Exception $error);

    /**
     * Encode an XML-RPC response.
     *
     * \param mixed $response
     *      The XML-RPC response to encode.
     *
     * \retval string
     *      The XML-RPC reponse,
     *      encoded as serialized XML.
     */
    public function encodeResponse($response);
}
