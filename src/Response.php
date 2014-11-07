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
 *      This class represents the response
 *      to an XML-RPC request.
 *
 * \authors François Poirotte <clicky@erebot.net>
 */
class Response implements \fpoirotte\XRL\ResponseInterface
{
    /// The response to an XML-RPC request, as serialized XML.
    protected $result;

    /**
     * Create the response to an XML-RPC request.
     *
     * \param mixed $xmlResult
     *      The result of the request as a string,
     *      encoded using XML-RPC types.
     */
    public function __construct($xmlResult)
    {
        if (!is_string($xmlResult)) {
            throw new \InvalidArgumentException('Not a valid response');
        }

        $this->result = $xmlResult;
    }

    /// \copydoc fpoirotte::XRL::ResponseInterface::__toString()
    public function __toString()
    {
        return $this->result;
    }

    /// \copydoc fpoirotte::XRL::ResponseInterface::publish()
    public function publish()
    {
        header('Content-Type: text/xml');
        header('Content-Length: '.strlen($this->result));
        exit($this->result);
    }
}
