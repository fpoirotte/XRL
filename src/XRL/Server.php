<?php

/**
 * \brief
 *      A simple XML-RPC server.
 */
class       XRL_Server
implements  Countable,
            ArrayAccess,
            IteratorAggregate
{
    /// Registered "procedures".
    protected $_funcs;

    protected $_callableWrapper     = 'XRL_Callable';

    protected $_responseCls         = 'XRL_Response';

    protected $_defaultEncoderCls   = 'XRL_Encoder';

    protected $_defaultDecoderCls   = 'XRL_Decoder';

    /**
     * Create a new XML-RPC server.
     */
    public function __construct()
    {
        $this->_funcs           = array();
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
    public function offsetSet($func, $callback)
    {
        $cls = $this->_callableWrapper;
        $this->_funcs[$func] = new $cls($callback);
    }

    public function offsetGet($func)
    {
        return $this->_funcs[$func];
    }

    public function offsetExists($func)
    {
        return isset($this->_funcs[$func]);
    }

    public function offsetUnset($func)
    {
        unset($this->_funcs[$func]);
    }

    public function count()
    {
        return count($this->_funcs);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_funcs);
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
        if ($encoder === NULL) {
            $cls        = $this->_defaultEncoderCls;
            $encoder    = new $cls();
        }
        if ($decoder === NULL) {
            $cls        = $this->_defaultDecoderCls;
            $decoder    = new $cls();
        }
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
            $params     = $request->getParams();
            $result     = $callable->invokeArgs($params);
            $response   = $encoder->encodeResponse($result);
        }
        catch (Exception $result) {
            $response   = $encoder->encodeError($result);
        }

        $responseCls = $this->_responseCls;
        $returnValue = new $responseCls($response);
        return $returnValue;
    }
}

