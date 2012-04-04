<?php

/**
 * \brief
 *      This class represents the response
 *      to an XML-RPC request.
 */
class XRL_Response
{
    /// The result of an XML-RPC request, as serialized XML.
    protected $_result;

    /**
     * Create the response to an XML-RPC request.
     *
     * \param mixed $xmlResult
     *      The result of the request. This may be a scalar
     *      (integer, boolean, float, string), an array,
     *      an exception or a DateTime object.
     */
    protected function __construct($xmlResult)
    {
        $this->_result  = $xmlResult;
    }

    /**
     * Returns the response for an XML-RPC request,
     * as serialized XML.
     *
     * \retval string
     *      An XML-RPC response, as a string.
     */
    public function __string()
    {
        return $this->_result;
    }

    /**
     * Send this XML-RPC response back to a browser.
     *
     * \warning
     *      This method never returns.
     */
    public function publish()
    {
        header('Content-Type: text/xml');
        header('Content-Length: '.strlen($this->_result));
        exit($this->_result);
    }
}
