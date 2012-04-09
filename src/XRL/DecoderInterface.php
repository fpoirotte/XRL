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
 *      Interface for an XML-RPC decoder.
 */
interface XRL_DecoderInterface
{
    /**
     * Decode an XML-RPC request.
     *
     * \param string $data
     *      An XML-RPC request as serialized XML.
     *
     * \retval XRL_Request
     *      An object representing an XML-RPC request.
     *
     * \throw InvalidArgumentException
     *      The given \c $data was invalid. For example,
     *      it wasn't a string, it didn"t contain any XML
     *      or the request was malformed.
     */
    public function decodeRequest($data);

    /**
     * Decode an XML-RPC response.
     *
     * \param string $data
     *      An XML-RPC response as serialized XML.
     *
     * \retval mixed
     *      The return value represented by the
     *      XML-RPC response, using native PHP types.
     *
     * \throw XRL_Exception
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
    public function decodeResponse($data);
}

