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
 *      A class that represents an XML-RPC request.
 */
class Request implements \fpoirotte\XRL\RequestInterface
{
    /// Name of the remote procedure to call.
    protected $procedure;

    /// Parameters to pass to the remote procedure.
    protected $params;

    /**
     * Creates a new XML-RPC request.
     *
     * \param string $procedure
     *      Name of the remote procedure to call.
     *
     * \param array $params
     *      Parameters to pass to the remote procedure.
     *
     * \throw InvalidArgumentException
     *      An invalid procedure name was given.
     */
    public function __construct($procedure, array $params)
    {
        if (!is_string($procedure)) {
            throw new \InvalidArgumentException('Invalid procedure name');
        }

        $this->procedure    = $procedure;
        $this->params       = $params;
    }

    /// \copydoc fpoirotte::XRL::RequestInterface::getProcedure()
    public function getProcedure()
    {
        return $this->procedure;
    }

    /// \copydoc fpoirotte::XRL::RequestInterface::getParams()
    public function getParams()
    {
        return $this->params;
    }
}
