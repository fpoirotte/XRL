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
 *      A class that represents an XML-RPC request.
 *
 * \authors François Poirotte <clicky@erebot.net>
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

        foreach ($params as $param) {
            if (!($param instanceof \fpoirotte\XRL\Types\AbstractType)) {
                throw new \InvalidArgumentException('Invalid parameter');
            }
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
