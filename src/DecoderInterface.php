<?php
/**
 * \file
 *
 * Copyright (c) 2012, XRL Team
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace fpoirotte\XRL;

/**
 * \brief
 *      Interface for an XML-RPC decoder.
 */
interface DecoderInterface
{
    /**
     * Decode an XML-RPC request.
     *
     * \param string $data
     *      An XML-RPC request as serialized XML.
     *
     * \retval fpoirotte::XRL::Request
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
    public function decodeResponse($data);
}
