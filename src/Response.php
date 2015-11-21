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
        return (string) $this->result;
    }

    /**
     * Adds an HTTP header to the current response.
     *
     * \param string $header
     *      Header to add.
     *
     * \retval fpoirotte::XRL::ResponseInterface
     *      Returns this response.
     *
     * @codeCoverageIgnore
     */
    protected function addHeader($header)
    {
        header($header);
        return $this;
    }

    /**
     * A function that echoes its input and exits.
     *
     * \param string $result
     *      Some string to echo before exiting.
     *
     * @codeCoverageIgnore
     */
    protected function finalize($result)
    {
        exit($result);
    }

    /// \copydoc fpoirotte::XRL::ResponseInterface::publish()
    public function publish()
    {
        $result = (string) $this->result;
        $this->addHeader('Content-Type: text/xml')
             ->addHeader('Content-Length: ' . strlen($result))
             ->finalize($result);
    }
}
