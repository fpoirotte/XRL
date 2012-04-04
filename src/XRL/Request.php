<?php

/**
 * \brief
 *      A class that represents an XML-RPC request.
 */
class XRL_Request
{
    /// Name of the remote procedure to call.
    protected $_procedure;

    /// Parameters to pass to the remote procedure.
    protected $_params;

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
        if (!is_string($procedure))
            throw new InvalidArgumentException('Invalid procedure name');

        $this->_procedure   = $procedure;
        $this->_params      = $params;
    }

    /**
     * Returns the remote procedure's name.
     *
     * \retval string
     *      The name of the remote procedure this request
     *      is meant to call.
     */
    public function getProcedure()
    {
        return $this->_procedure;
    }

    /**
     * Returns the parameters to pass
     * to the remote procedure.
     *
     * \retval array
     *      Parameters to pass to the
     *      remote procedure.
     */
    public function getParams()
    {
        return $this->_params;
    }
}
