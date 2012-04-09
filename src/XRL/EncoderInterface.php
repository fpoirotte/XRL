<?php
/*
    This file is part of XRL.

    XRL is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    XRL is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with XRL.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * \brief
 *      Interface for an XML-RPC encoder.
 */
interface XRL_EncoderInterface
{
    /**
     * Encode an XML-RPC request.
     *
     * \param XRL_Request $request
     *      The XML-RPC request to encode.
     *
     * \retval string
     *      The XML-RPC request,
     *      encoded as serialized XML.
     */
    public function encodeRequest(XRL_Request $request);

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
    public function encodeError(Exception $error);

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

