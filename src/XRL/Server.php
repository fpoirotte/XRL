<?php

/**
 * A simple XML-RPC server.
 */
class XRL_Server
{
    /// Registered "procedures".
    protected $_funcs;

    /**
     * Create a new XML-RPC server.
     */
    public function __construct()
    {
        $this->_funcs   = array();
    }

    /**
     * Register a new procedure with this XML-RPC server.
     *
     * \param string $func
     *      A valid name for the procedure.
     *
     * \param mixed $callback
     *      Any valid PHP callback.
     *
     * \note
     *      See the "Payload format" section at
     *      http://xmlrpc.scripting.com/spec.html
     *      for information on valid procedure names.
     *
     * \note
     *      Several syntaxes can be used to refer to a PHP callback, see
     *      http://php.net/language.pseudo-types.php#language.types.callback
     *      for the full list of supported constructs.
     */
    public function register($func, $callback)
    {
        $this->_funcs[$func] = new XRL_Callable($callback);
    }

    /**
     * Handles an XML-RPC request and returns a response
     * for that request.
     *
     * \param XRL_Request $request
     *      (optional) The XML-RPC request to handle.
     *      If omitted, this method will try to create one
     *      automatically from the POST data the current
     *      HTTP request contains.
     *
     * \retval XRL_Response
     *      The response for that request. This response
     *      may indicate either success or failure of the
     *      Remote Procedure Call 
     */
    public function handle(
                                $data       = NULL,
        XRL_EncoderInterface    $encoder    = NULL,
        XRL_DecoderInterface    $decoder    = NULL
    )
    {
        if ($encoder === NULL)
            $encoder = new XRL_Encoder();
        if ($decoder === NULL)
            $decoder = new XRL_Decoder();

        if ($data === NULL)
            $data = file_get_contents('php://input');

        $exception = FALSE;
        try {
            $request    = $decoder->decodeRequest($data);
            $procedure  = $request->getProcedure();

            if (!isset($this->_funcs[$procedure])) {
                throw new BadFunctionCallException(
                    "No such procedure ($procedure)"
                );
            }

            $callable   = $this->_funcs[$procedure];
            $result     = $callable->invokeArgs($request->getParams());
            $response   = $encoder->encodeResponse($result);
        }
        catch (Exception $result) {
            $response   = $encoder->encodeError($result);
        }

        $returnValue = new XRL_Response($response);
        return $returnValue;
    }
}

